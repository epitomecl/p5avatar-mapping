<?php

namespace modules;

use admin\UserManagement as UserManagement;
use \JsonSerializable as JsonSerializable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../common/PHPMailer/src/Exception.php';
require_once __DIR__.'/../common/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../common/PHPMailer/src/SMTP.php';

/**
* The input data (given name, email, confirmation of data protection and terms of service) are stored into database. 
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
		/*
		$sql = "SELECT user_name, email, phone, ";
		$sql .= "DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.user_name ='%s' ";
		$sql .= "AND a.email ='%s' ";
		$sql .= "AND a.phone ='%s' ";
		$sql = sprintf($sql, $this->userName, $this->email, $this->phone);

		if ($result = $mysqli->query($sql)) {
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$this->salt = $row["salt"];

					if (strcmp($this->email, $row["email"]) != 0) {
						$this->errorCode = 48;
					} elseif (strcmp($this->phone, $row["phone"]) != 0) {
						$this->errorCode = 128;
					} elseif (intval($row["remaining"]) < 0) {
						$this->errorCode = 255;
					}
				}
				$result->free();
			} else {
				$this->errorCode = 64;
			}
		} else {
			$this->errorCode = 1;
		}
		*/
		
		$token = $this->getTokenAndUpdateUser($this->mysqli, $email, time());
	
		$this->sendEmail($email, $token);

		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param string $token The request token		
	*/	
	public function doGet($token) {
		/*
		$sql = "SELECT user_name, email, phone, ";
		$sql .= "DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.user_name ='%s' ";
		$sql .= "AND a.email ='%s' ";
		$sql .= "AND a.phone ='%s' ";
		$sql = sprintf($sql, $this->userName, $this->email, $this->phone);

		if ($result = $mysqli->query($sql)) {
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$this->salt = $row["salt"];

					if (strcmp($this->email, $row["email"]) != 0) {
						$this->errorCode = 48;
					} elseif (strcmp($this->phone, $row["phone"]) != 0) {
						$this->errorCode = 128;
					} elseif (intval($row["remaining"]) < 0) {
						$this->errorCode = 255;
					}
				}
				$result->free();
			} else {
				$this->errorCode = 64;
			}
		} else {
			$this->errorCode = 1;
		}
		*/
		
		$obj = new \stdClass();
		$obj->apikey = md5(sprintf("%s", time())); //$this->getTokenAndUpdateUser($mysqli);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	function insertToken($mysqli, $token) {
		$modified = time() + (15 * 60);
		$data = array($userName, $maId, $email, $modified);
		$token = getResetToken($data, $this->getClassName($this));

		$sql = "UPDATE user ";
		$sql .= "LEFT JOIN ma ON (ma.id = user.maID) ";
		$sql .= "SET user.modified = FROM_UNIXTIME(%d), token = '%s' ";
		$sql .= "WHERE ma.email = '%s' ";
		$sql .= "AND ma.locked = 0 AND user.deleted != 'Ja'";
		$sql = sprintf($sql, $modified, $token, $email);

		if ($mysqli->query($sql) === true) {
			
		}
	}
	
	function isValidEmail($email) {
		if (empty($email) || is_array($email) || is_numeric($email) || is_bool($email) || is_float($email) || is_file($email) || is_dir($email) || is_int($email)) {
			return 0;
		} else {
			$email=trim(strtolower($email));
			if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
				return 1;
			} else {
				$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
				return (preg_match($pattern, $email) === 1) ? 1 : 0;
			}
		}
	}

	function isValidUser($mysqli, $email) {
		$maId = 0;
		
		$sql = "SELECT user.id, user.maID FROM user ";
		$sql .= "WHERE user.email = '%s' ";
		$sql = sprintf($sql, $email);

		if ($result = $mysqli->query($sql)) {
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$maId = intval($row["maID"]);
					break;
				}
				$result->free();
			}
		}
		
		return $maId;
	}

	function getResetToken($array, $salt) {
		$salt = str_pad($salt, 32);
		$data = "";
		
		foreach($array as $value) {
			$data .= base64_encode($value);
		}
		
		return hash_hmac("sha256", $data, $salt);
	}

	public function getTokenAndUpdateUser($mysqli, $email, $phone) {
		$modified = time() + (15 * 60);
		$data = array($email, $phone, $modified);
		$token = UserManagement::getResetToken($data, "agaadfda");//$this->salt);
		
		$sql = "UPDATE user set modified=FROM_UNIXTIME(%d), token='%s' ";
		$sql .= "WHERE email='%s' ";
		$sql = sprintf($sql, $modified, $token, $email);
		
		//if (!$mysqli->query($sql)) {
		//	throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		//}

		return $token;
	}

	public function sendEmail($email, $token) {
		$config = $this->smtpConfig; //parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
		
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
	
	function getClassName($obj) {
		return substr(strrchr(get_class($obj), '\\'), 1);
	}
}