<?php

namespace modules;

use \mysqli as mysqli;
use \JsonSerializable as JsonSerializable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../common/PHPMailer/src/Exception.php';
require_once __DIR__.'/../common/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../common/PHPMailer/src/SMTP.php';

/**
* If user session is alive, password data can be managed inside a time slot of 15 minutes.
* A POST request will send an email with an token to the email receiver.
* A GET request will perform some action on client side (maybe show a password input form).
* A PUT request will be handle current token and password.
*/
class Password implements JsonSerializable{
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
	
	private function isValidEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL) 
			&& preg_match('/@.+\./', $email);
	}
	
	/**
	* something describes this method
	*
	* @param string $email The email for receiving request token
	* @param string $firstName The firstName of user
	*/	
	public function doPost($email, $firstName) {
		$mysqli = $this->mysqli;
		
		$userId = 0;
		$email = strip_tags(stripcslashes(trim($email)));
		$firstName = strip_tags(stripcslashes(trim($firstName)));
		
		$sql = "SELECT userID FROM profile WHERE firstName='%s' AND email='%s';";
		$sql = sprintf($sql, $firstName, $email);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$userId = intval($row["userID"]);
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}	
		
		if ($userId > 0 && strlen($email) > 0 && isValidEmail($email)) {
			$modified = time() + (15 * 60);	
			$salt = md5(sprintf("%s%s", get_class($this), time()));
			$sha256 = hash_hmac("sha256", get_class($this), $salt);
			$sql = "UPDATE user set modified=FROM_UNIXTIME(%d), token='%s', salt='%s', password='' WHERE id='%d'";
			$sql = sprintf($sql, $modified, $sha256, $salt, $userId);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}	
		
			$this->sendEmail($email, $sha256);
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Acceptable'), 406);			
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of user	
	* @param string $token The token		
	* @param string $password The password
	* @param string $password2 The password confirmation	
	*/	
	public function doPut($userId, $token, $password, $password2) {
		$mysqli = $this->mysqli;
		
		$remaining = $this->getRemaining($mysqli, $token);
		
		if ($remaining < 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Request Time-out'), 408);
		} elseif (strcmp($password, $password2) == 0 && strlen($password) >= 8) {
			$salt = md5(sprintf("%s%s", get_class($this), time()));
			$sha256 = hash_hmac("sha256", $password, $salt);
			
			$sql = "UPDATE user set password='%s', salt='%s%', token='' WHERE id=%d AND token='%s';";
			$sql = sprintf($sql, $sha256, $salt, $userId, $token);
			
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Acceptable'), 406);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param string $token The request token		
	*/	
	public function doGet($token) {
		$mysqli = $this->mysqli;
		
		$remaining = $this->getRemaining($mysqli, $token);
				
		if ($remaining < 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Request Time-out'), 408);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	private function sendEmail($email, $token) {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
		
		$to = $email;
		$from = "marian@epitomecl.com";
		$module = strtolower($this->getClassName($this));
		$subject = "YOUR PASSWORD RESET TOKEN";
		$url = $this->siteURL()."api/?module=$module&token=$token";
		$link = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
		$body = "<!DOCTYPE html>";
		$body .= "<html>";
		$body .= "<head><meta charset='charset=utf-8'>";
		$body .= "<title>$subject</title></head>";
		$body .= "<body>";
		$body .= "<h4>Password reset instruction!</h4>";
		$body .= "<p>This given token is around 15 minutes alive.</p>";
		$body .= "<p>Follow this link above and reset your password.</p>";
		$body .= "<p>Reset-Token: $token</p>";
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