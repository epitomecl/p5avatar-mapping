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
			'userId' => userId
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param string $login The email or username set as login
	* @param string $password The password
	*/
	public function doPost($login, $password) {
		$mysqli = $this->mysqli;
		$userId = getUser($mysqli, $login, $password);
		$sessionId = session_Id();
		
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
		
		$this->userId = $userId;
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);	
	}

	private function getUser($mysqli, $login, $password) {
		$sql = "SELECT userId FROM user WHERE login = '%s' AND password='%s'";
		$sql = sprintf($sql, $login, $password);

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