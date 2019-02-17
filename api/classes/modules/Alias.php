<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, the user can update his profile data 
* with new an alias (as designer name, stage name or pseudonym).
*/
class Alias implements JsonSerializable{
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
	* @param string $alias The alias name as designer
	*/	
	public function doPost($userId, $alias) {
		$mysqli = $this->mysqli;
		
		if ($userId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
		
		$sql = "UPDATE profile SET alias='%s' WHERE userId=%d";
		$sql = sprintf($sql, $alias, $userId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		if ($mysqli->affected_rows == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Modified'), 304);			
		}

		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
}