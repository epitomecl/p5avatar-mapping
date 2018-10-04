<?php 

namespace admin;

use \JsonSerializable as JsonSerializable;

class SubmitPasswordReset implements JsonSerializable{
	private $userName;
	private $userToken;
	private $userPass;
	private $errorCode;
	
	public function jsonSerialize() {
        return array(
             'errorCode' => $this->errorCode
        );
    }
	
	public function __construct($userToken, $userName, $userPass) {
		$this->userToken = $userToken;
		$this->userName = $userName;		
		$this->userPass = $userPass;
		$this->errorCode = 0;
	}
	
	public function execute() {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/include/db.mysql.ini");
		$mysqli = new mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);

		$mysqli->set_charset("utf8");
		
		$sql = "SELECT user_id, user_name, email, phone, modified, ";
		$sql .= "DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.user_name ='%s' ";
		$sql = sprintf($sql, $this->userName);

		$user = new UserData();
		$result = $mysqli->query($sql);
		
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$user->setUserToken($this->userToken);
				$user->setUserPass($this->userPass);
				$user->setUserId($row["user_id"]);
				$user->setUserName($row["user_name"]);
				$user->setUserEmail($row["email"]);
				$user->setUserPhone($row["phone"]);
				$user->setModified(strtotime($row["modified"]));
				$user->setSalt($row["salt"]);
				
				if ($user->getModified() < time()) {
					$this->errorCode = 32;
				} elseif (intval($row["remaining"]) < 0) {
					$this->errorCode = 255;
				} elseif ($this->hasValidToken($user)) {
					$this->errorCode = 16;
				}
			}
			$result->free();
		} else {
			$this->errorCode = 64;
		}
		
		if ($this->errorCode == 0) {
			$this->updatePassword($mysqli, $user);
		}

		$mysqli->close();
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	public function hasValidToken($user) {
		$userId = $user->getUserId();
		$phone = $user->getUserPhone();
		$email = $user->getUserEmail();
		$modified = $user->getModified();
		$salt = $user->getSalt();
		$data = array($this->userId, $phone, $email, $modified);
		$token = UserManagement::getResetToken($data, $salt);
				
		return strcmp($token, $user->getUserToken());
	}
	
	public function updatePassword($mysqli, $user) {
		$user->setModified(time());
		$user->setSalt(UserManagement::generateRandomString(32));
		$user->setUserToken(UserManagement::getUserToken($user));
		
		$userId = $user->getUserId();
		$salt = $user->getSalt();
		$modified = $user->getModified();
		$password = hash_hmac("sha256", $user->getUserPass(), $salt);
		$sql = "UPDATE users SET ";
		$sql .= "password='%s', salt='%s', modified=FROM_UNIXTIME(%d) ";
		$sql .= "WHERE user_name='%s' ";
		$sql = sprintf($sql, $password, $salt, $modified, $userName);

		if (!$mysqli->query($sql)) {
			$this->errorCode = 1;
		}
	}
}