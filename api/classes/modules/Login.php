<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* Gives access to backend service, open a session. 
* If user session is alive, session id will renewed. 
* Response id of current user.
*/
class Login implements JsonSerializable {
	private $mysqli;
	private $userId;
	
	public function jsonSerialize() {
		return array(
			'userId' => $this->userId
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
		$this->userId = 0;
	}
	
	/**
	* something describes this method
	*
	* @param string $email The email or username set as login
	* @param string $password The password
	*/
	public function doPost($email, $password) {
		$mysqli = $this->mysqli;
		$sessionId = session_Id();
		$email = strip_tags(stripcslashes(trim($email)));
		$password = strip_tags(stripcslashes(trim($password)));

		if (strlen($password) >= 8 && strlen($email) > 0 && $this->isValidEmail($email)) {
			$userId = $this->getUserId($mysqli, $email, $password);	
		
			if ($userId == 0) {
				throw new Exception(sprintf("%s, %s", get_class($this), "User not exist."), 404);
			} elseif ($this->hasUserLogin($mysqli, $userId)) {
				$sql = "UPDATE user_login SET sessionId='%s', modified=NOW() WHERE userId=%d";
				$sql = sprintf($sql, $sessionId, $userId);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}
			} else {
				$sql = "INSERT INTO user_login (userId, sessionId, modified) VALUES ('%s', '%s', NOW())";
				$sql = sprintf($sql, $userId, $sessionId);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}		
			}
		
			$this->userId = $_SESSION["userId"] = $userId;
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Acceptable'), 406);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);	
	}
	
	private function isValidEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL) 
			&& preg_match('/@.+\./', $email);
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
				
				if (strcmp(hash_hmac("sha256", $password, $salt), $sha256) == 0) {
					$userId = intval($row["userId"]);					
				}
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		return $userId;
	}
	
	private function hasUserLogin($mysqli, $userId) {
		$sql = "SELECT userId FROM user_login WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));

		$found = 0;
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$found = intval($row["userId"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		return $found;
	}
}