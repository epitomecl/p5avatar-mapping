<?php

namespace admin;

class UserManagementLogin {
	private $user;
	
	public function __construct($userEmail, $userPass, $userName, $userPhone) {
		$this->init($userEmail, $userPass, $userName, $userPhone);
	}
	
	public function init($userEmail, $userPass, $userName, $userPhone) {
		$this->user = new Userdata();
		$this->user->setUserEmail($userEmail);
		$this->user->setUserPass($userPass);
		$this->user->setUserName($userName);
		$this->user->setUserPhone($userPhone);
	}
	
	public function execute() {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/include/db.mysql.ini");
		$mysqli = new mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);

		$mysqli->set_charset("utf8");
				
		if (!$mysqli->connect_errno) {
			$this->setEncodedUserPass($mysqli);

			if (empty($this->user->getUserName())) {
				$this->loginUser($mysqli);
			} else {
				$this->addUser($mysqli);
			}
			
			$mysqli->close();
		}

		echo json_encode($this->user, JSON_UNESCAPED_UNICODE);
	}
	
	private function loginUser($mysqli) {
		$validUserPass = false;
		$validRemaining = false;
		
		$sql = "SELECT user_id, user_name, password, salt, email, ";
		$sql .= "modified, expired, DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.email ='%s' ";
		$sql = sprintf($sql, $this->user->getUserEmail());

		$result = $mysqli->query($sql);
		
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$this->user->setUserId($row["user_id"]);
				$this->user->setUserName($row["user_name"]);
				$this->user->setSalt($row["salt"]);
				$this->user->setModified(strtotime($row["modified"]));
				$this->user->setExpired(strtotime($row["expired"]));
				$this->user->setErrorCode(0);
				$validUserPass = $this->user->isUserPassValid($row["password"]);
				$validRemaining = intval($row["remaining"]) > 0;
			}
			$result->free();
		}
		
		if ($validUserPass && $validRemaining) {
			$this->user->setUserToken(UserManagement::getUserToken($this->user));
		} else {
			$this->user->setUserToken("");
			
			if (!$validRemaining) {
				$this->user->setErrorCode(16);
			}
			if (!$validUserPass) {
				$this->user->setErrorCode(8);
			}
		}
	}
	
	private function addUser($mysqli) {
		$userEmail = $this->user->getUserEmail();
		$sql = "select * from users where email='%s'";
		$sql = sprintf($sql, $userEmail);
		$result = $mysqli->query($sql);
		
		if ($result->num_rows > 0) {
			$this->user->setErrorCode(255);
			return;
		}

		$userPass = $this->user->getUserPass();
		$salt = $this->user->getSalt();
		$userName = $this->user->getUserName();
		$email = $this->user->getUserEmail();
		$phone = $this->user->getUserPhone();
		$modified = time();
		$datetime = date('Y-m-d H:i:s', time());
		$expired = strtotime($datetime . " + 365 day");
		$sql = "INSERT INTO users(email, password, user_name, phone, salt, modified, expired) ";		
		$sql .= "VALUES('$email', '$userPass', '$userName', '$phone', '$salt', FROM_UNIXTIME($modified), FROM_UNIXTIME($expired))";	

		if (!$mysqli->query($sql)) {
			$this->user->setErrorCode(1);		
		}
	}
	
	private function setEncodedUserPass($mysqli){
		$userEmail = $this->user->getUserEmail();
		$password = $this->user->getUserPass();
		$salt = UserManagement::generateRandomString(32);
			
		if (strlen($password) > 0) {
			$sql = "select salt from user where email='%s'";
			$sql = sprintf($sql, $userEmail);
			$result = $mysqli->query($sql);
				
			if ($result->num_rows > 0) {
				if ($row = $result->fetch_assoc()) {
					$salt = $row["salt"];
				}
				$result->free();
			}

			$password = hash_hmac("sha256", $password, $salt);
			$this->user->setSalt($salt);
			$this->user->setUserPass($password);
		}
	}
}