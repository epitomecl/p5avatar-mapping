<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, user session will be closed.
*/
class Logout {
	private $mysqli;
	
	public function jsonSerialize() {
		return array(
			'success' => true
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param string $userId The id of current user
	*/
	public function doPost($userId) {
		$mysqli = $this->mysqli;
		
		if (!$this->hasUserLogin($mysqli, $userId)) {
			throw new Exception(sprintf("%s, %s", get_class($this), "User not exist."), 404);
		} else {
			$sql = "DELETE FROM user_login WHERE userId = %d";
			$sql = sprintf($sql, intval($userId));
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			} else {
				if ($mysqli->affected_rows == 0) {
					throw new Exception(sprintf("%s, %s", get_class($this), "User not exist."), 404);
				}
			}
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);			
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