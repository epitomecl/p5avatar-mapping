<?php

namespace admin;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

class RequestPasswordReset implements JsonSerializable{
	private $userName;
	private $email;
	private $errorCode;
	private $phone;
	private $salt;
	
	public function jsonSerialize() {
        return array(
             'errorCode' => $this->errorCode
        );
    }
	
	public function __construct($userName, $email, $phone) {
		$this->userName = $userName;
		$this->email = $email;
		$this->phone = $phone;
		$this->salt = "";
		$this->errorCode = 0;
	}
	
	public function execute() {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/include/db.mysql.ini");
		$mysqli = new mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);

		$mysqli->set_charset("utf8");
		
		$sql = "SELECT user_name, email, phone, ";
		$sql .= "DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.user_name ='%s' ";
		$sql .= "AND a.email ='%s' ";
		$sql .= "AND a.phone ='%s' ";
		$sql = sprintf($sql, $this->userName, $this->email, $this->phone);

		$result = $mysqli->query($sql);
		
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
		
		if ($this->errorCode == 0) {
			$this->sendEmail($mysqli);
		}

		$mysqli->close();
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	public function sendEmail($mysqli) {
		$modified = time() + (15 * 60);
		$data = array($this->userName, $this->phone, $this->email, $modified);
		$token = UserManagement::getResetToken($data, $this->salt);
		
		$sql = "UPDATE users set modified=FROM_UNIXTIME(%d) ";
		$sql .= "WHERE email='%s' ";
		$sql = sprintf($sql, $modified, $this->email);
		
		if (!$mysqli->query($sql)) {
			$this->errorCode = 1;
		} else {
			$to = $this->email;
			$subject = "APP PASSWORD RESET TOKEN";
			$from = "master@example.com";
			$headers = "From: " . $from . "\r\n";
			$headers .= "Reply-To: ". $from . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			$module = md5("SubmitPasswordReset");
			$url = $this->siteURL()."api/?module=$module&token=$token";
			$link = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
			$message = "<h4>Password reset instruction!</h4>";
			$message .= "<p>This given token is around 15 minutes alive.</p>";
			$message .= "<p>Copy this token into your app or follow this link above and reset your password.</p>";
			$message .= "<p>Reset-Token: $token</p>";
			$message .= "<p>External link: <a href='".$link."' target='_blank'>".$token."</a>";
			$message .= "<p>Best regards.</p>";
			$message .= "<p>Customer Support Crew</p>";
		
			try{
				mail($to, $subject, $message, $headers);
			} catch (Exception $e) {
				$this->errorCode = 96;
			}
		}
	}
	
	private function siteURL() {
		$protocol = (!empty($_SERVER['HTTPS']) && 
					$_SERVER['HTTPS'] !== 'off' || 
					$_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'].'/';
		
		return $protocol.$domainName;
	}
}