<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, the user can update bonded address to his owned avatar (if available).
* GET request list all available owned avatars.
*/
class Address implements JsonSerializable{
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
	* @param int $userId The id of user
	* @param int $avatarId The id of owned avatar
	* @param string $address The address for owned avatar	
	*/	
	public function doPost($userId, $avatarId, $address) {
		$mysqli = $this->mysqli;
		$address = strip_tags(stripcslashes(trim($address)));
		
		$sql = "UPDATE avatar SET address='%s' WHERE userId=%d AND id=%d";
		$sql = sprintf($sql, $address, $userId, $avatarId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of user
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;
		
		$data = array();
		$sql = "SELECT * FROM avatar WHERE userId=%d";
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);		
	}	
}