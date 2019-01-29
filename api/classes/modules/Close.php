<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, user session will be closed.
* All user profile data will be deleted. 
* Created avatars are keep on system.
*/
class Close  implements JsonSerializable {
	private $mysqli;
	
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
	* @param string $userId The id of current user
	*/	
	public function doPost($userId) {
		$sql = "DELETE FROM profile WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}		
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
}