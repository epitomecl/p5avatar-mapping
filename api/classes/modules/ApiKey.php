<?php

namespace modules;

use \JsonSerializable as JsonSerializable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../common/PHPMailer/src/Exception.php';
require_once __DIR__.'/../common/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../common/PHPMailer/src/SMTP.php';

/**
* The input data (given email, confirmation of data protection and terms of service) are stored into database. 
* To given eMail address an random link will be send. If eMail has wrong spelling, success will be false. 
* The user has to check also spam folder. Inside the email is a link with access code. 
* The link is 15 min valid. After 15 min old invalid users will be delete. After confirming the link an apikey is available.
*/
class ApiKey implements JsonSerializable{
	private $mysqli;
	private $smtpConfig;

	public function jsonSerialize() {
		return array(
			'success' => true
        );
    }
	
	/**
	* something describes this method
	*/	
	public function __construct($mysqli, $smtpConfig) {
		$this->mysqli = $mysqli;
		$this->smtpConfig = $smtpConfig;
	}
	
	private function isValidEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL) 
			&& preg_match('/@.+\./', $email);
	}
	
	private function getRemaining($mysqli, $token) {
		$sql = "SELECT token, DATEDIFF(modified, NOW()) AS remaining ";
		$sql .= "FROM user WHERE token='%s';";
		$sql = sprintf($sql, $token);

		$remaining = 0;
		$value = "";
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$remaining = intval($row["remaining"]);
				$value = trim($row["token"]);
				break;
			}
			
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		if (strcmp($value, $token) != 0 || empty($value)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Unauthorized'), 401);
		}
			
		return remaining;
	}
	
	private function getUserId($mysqli, $email, $password) {
		$userId = 0;
		$sql = "SELECT userId, password, salt FROM profile ";
		$sql .= "LEFT JOIN user ON (user.id = profile.userId) ";
		$sql .= "WHERE email='%s'";
		$sql = sprintf($sql, $email);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$sha256 = trim($row["password"]);
				$salt = trim($row["salt"]);
				
				if (strcmp(hash_hmac("sha256", $sha256, $salt), $password) == 0) {
					$userId = intval($row["userId"]);					
				}
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		return $userId;
	}

	private function hasApikey($mysqli, $userId) {
		$id = 0;
		$sql = "SELECT id FROM apikey WHERE userId='%s'";
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$id = intval($row["id"]);					
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		return ($id > 0);
	}		
		
	/**
	* something describes this method
	*
	* @param string $email The email for receiving request token
	* @param string $password The password
	* @param string $password2 The password confirmation
	* @param int $dataProtection Data protection [1 : accepted, 0 : not accepted]
	* @param int $termsOfService Terms of Service [1 : accepted, 0 : not accepted]
	*/	
	public function doPost($email, $password, $password2, $dataProtection, $termsOfService) {
		$email = strip_tags(stripcslashes(trim($email)));
		$password = strip_tags(stripcslashes(trim($password)));

		if (strcmp($password, $password2) == 0 && strlen($password) >= 8 && strlen($email) > 0 && isValidEmail($email)) {
			$userId = $this->getUserId($mysqli, $email, $password);	
			$modified = time() + (15 * 60);	
			$sha256 = hash_hmac("sha256", get_class($this), $modified);
			
			if ($userId == 0) {
				$salt = md5(sprintf("%s%s", get_class($this), time()));
				$sha256 = hash_hmac("sha256", $password, $salt);
				$sql = "INSERT INTO user SET password='%s', salt='%s%', token='%s', modified=FROM_UNIXTIME(%d), dataProtection=%d, termsOfService=%d;";
				$sql = sprintf($sql, $sha256, $salt, $sha256, $modified, $dataProtection, $termsOfService);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}

				$sql = "INSERT INTO profile SET userId=%d, email='', modified=NOW();";
				$sql = sprintf($sql, $mysqli->insert_id, $email);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}
			} else {
				$sql = "UPDATE user set modified=FROM_UNIXTIME(%d), token='%s' WHERE id='%d'";
				$sql = sprintf($sql, $modified, $sha256);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}				
			}
			
			$this->sendEmail($email, $sha256);
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Acceptable'), 406);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* Update the apikey and set user as active user.
	*
	* @param string $token The request token	
	*/	
	public function doGet($token) {
		$mysqli = $this->mysqli;
		
		$token = strip_tags(stripcslashes(trim($token)));
		$remaining = $this->getRemaining($mysqli, $token);
				
		if ($remaining < 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Request Time-out'), 408);
		}
		
		$userId = 0;
		$sql = "SELECT userId FROM user WHERE token='%s'";
		$sql = sprintf($sql, $token);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$userId = intval($row["userId"]);					
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if ($userId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), "Gone"), 410);
		}
		
		$sql = "UPDATE user set token='', active=1 WHERE id='%s';";
		$sql = sprintf($sql, $userId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$modified = time() + (15 * 60);	
		$sha256 = hash_hmac("sha256", get_class($this), $modified);
		$sql = "INSERT INTO apikey SET apikey='%s', userId=%d, modified=NOW();";		
		if ($this->hasApikey($mysqli, $userId)) {
			$sql = "UPDATE apikey SET apikey='%s', modified=NOW() WHERE userId=%d;";
		}
		$sql = sprintf($sql, $apikey, $userId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$obj = stdClass();
		$obj->apikey = $sha256;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	private function sendEmail($email, $token) {
		$config = $this->smtpConfig;
		
		$to = $email;
		$from = "marian@epitomecl.com";
		$module = strtolower($this->getClassName($this));
		$subject = "YOUR API KEY REQUEST TOKEN";
		$url = $this->siteURL()."api/?module=$module&token=$token";
		$link = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
		$body = "<!DOCTYPE html>";
		$body .= "<html>";
		$body .= "<head><meta charset='charset=utf-8'>";
		$body .= "<title>$subject</title></head>";
		$body .= "<body>";
		$body .= "<h4>API KEY request instruction!</h4>";
		$body .= "<p>This given token is around 15 minutes alive.</p>";
		$body .= "<p>Follow this link above and grab your api key.</p>";
		$body .= "<p>Request-Token: $token</p>";
		$body .= "<p>External link: <a href='".$link."' target='_blank'>".$token."</a></p>";
		$body .= "<p>Browser link: ".$link."</p>";
		$body .= "<p>Best regards.</p>";
		$body .= "<p>Avarkey</p>";
		$body .= "</body>";
		$body .= "</html>";
	
		$mail = new PHPMailer(true);            // Passing `true` enables exceptions
		try {
			//Server settings
			$mail->SMTPDebug = 0;               // 2 : Enable verbose debug output
			$mail->isSMTP();                    // Set mailer to use SMTP
			$mail->Host = $config['HOST'];  	// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;             // Enable SMTP authentication
			$mail->Username = $config['USER'];  // SMTP username
			$mail->Password = $config['PASS'];  // SMTP password
			$mail->SMTPSecure = 'tls';          // Enable TLS encryption, `ssl` also accepted
			$mail->Port = $config['PORT'];      // TCP port to connect to

			//Recipients
			$mail->setFrom($from, 'Avarkey');
			$mail->addAddress($to);

			//Content
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $this->cleanup(array("</title>", "</h4>", "</p>"), $body);

			$mail->send();
			$this->errorMsg = 'Message has been sent.';
			$this->errorCode = 0;
		} catch (Exception $e) {
			$this->errorMsg = 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
			$this->errorCode = 96;
			
			throw new Exception($e->getMessage(), 406);
		}
	}
	
	private function cleanup($labels, $body) {
		foreach ($labels as $index => $search) {
			$body = str_replace($search, PHP_EOL, $body);
		}
		
		return strip_tags($body);
	}
	
	private function siteURL() {
		$protocol = (!empty($_SERVER['HTTPS']) && 
					$_SERVER['HTTPS'] !== 'off' || 
					$_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'].'/';
		
		return $protocol.$domainName;
	}
	
	private function getClassName($obj) {
		return substr(strrchr(get_class($obj), '\\'), 1);
	}
}