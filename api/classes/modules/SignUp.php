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
* To given eMail address an random link will be send. If eMail has wrong spelling, success will be false. 
* The user has to check also spam folder. Inside the email is a link with access code. 
* The link is 15 min valid. The user can set a new password. After 15 min old invalid users will be delete.
*/
class SignUp implements JsonSerializable{
	private $mysqli;
	private $smtpConfig;	
	private $userName;
	private $email;
	private $errorCode;
	private $errorMsg;
	private $phone;
	private $salt;
	
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
		$this->errorCode = 0;
		$this->errorMsg = "";
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
		$mysqli = $this->mysqli;
		
		$sql = "SELECT user_name, email, phone, ";
		$sql .= "DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.user_name ='%s' ";
		$sql .= "AND a.email ='%s' ";
		$sql .= "AND a.phone ='%s' ";
		$sql = sprintf($sql, $this->userName, $this->email, $this->phone);

		if ($result = $this->mysqli->query($sql)) {
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
		if ($this->errorCode == 0) {
			$token = $this->getTokenAndUpdateUser($this->mysqli);
			
			if ($this->errorCode == 0 || true) {
				$this->sendEmail($token);
			}
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
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	public function getTokenAndUpdateUser($mysqli) {
		$modified = time() + (15 * 60);
		$data = array($this->userName, $this->phone, $this->email, $modified);
		$token = UserManagement::getResetToken($data, $this->salt);
		
		$sql = "UPDATE users set modified=FROM_UNIXTIME(%d) ";
		$sql .= "WHERE email='%s' ";
		$sql = sprintf($sql, $modified, $this->email);
		
		if (!$mysqli->query($sql)) {
			$this->errorCode = 1;
		}

		return $token;
	}
	
	public function sendEmail($token) {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
		
		$to = $this->email;
		$from = "marian@epitomecl.com";
		$module = strtolower($this->getClassName($this));
		$subject = "YOUR SIGNUP REQUEST TOKEN";
		$url = $this->siteURL()."api/?module=$module&token=$token";
		$link = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
		$body = "<!DOCTYPE html>";
		$body .= "<html>";
		$body .= "<head><meta charset='charset=utf-8'>";
		$body .= "<title>$subject</title></head>";
		$body .= "<body>";
		$body .= "<h4>SIGNUP request instruction!</h4>";
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