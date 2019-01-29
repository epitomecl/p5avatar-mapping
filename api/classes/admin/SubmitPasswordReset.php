<?php 

namespace admin;

use \mysqli as mysqli;
use \JsonSerializable as JsonSerializable;

class SubmitPasswordReset implements JsonSerializable{
	private $userName;
	private $userToken;
	private $userPass;
	private $errorCode;
	private $callback;
	
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
		$this->callback = "http://13.209.194.1:3000/landing";
	}
	
	public function execute() {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/db.mysql.ini");
		$mysqli = new mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);

		$mysqli->set_charset("utf8");
		
		$sql = "SELECT user_id, user_name, email, phone, modified, ";
		$sql .= "DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users a ";
		$sql .= "WHERE a.user_name ='%s' ";
		$sql = sprintf($sql, $this->userName);

		$user = new UserData();
		
		if ($result = $mysqli->query($sql)) {
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
		} else {
			$this->errorCode = 1;
		}
		if ($this->errorCode == 0) {
			$this->updatePassword($mysqli, $user);
		}

		$mysqli->close();
		
		if (empty($this->callback)) {
			echo json_encode($this, JSON_UNESCAPED_UNICODE);
		} else {
			$html = $this->fetchHtml($this->callback);
			$url = substr($this->callback, 0,  strrpos($this->callback, "/") + 1);
			$html = str_replace("<head>", sprintf("<head><base href=\"%s\">", $url), $html);
			$html = str_replace("Beta", json_encode($this, JSON_UNESCAPED_UNICODE), $html);
			
			echo $html;
		}
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
	
	private function fetchHtml($url) {
		$html = "";
		
		if ($this->isCurl()) {
			if ($handle = curl_init($url)) {
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				$html = curl_exec($handle);	
				curl_close($handle);
			}
		} else {
			$html = file_get_contents($url);
		}
		
		return $html;
	}

	private function isCurl(){
		return function_exists('curl_version');
	}	
}