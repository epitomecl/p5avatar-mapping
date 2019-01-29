<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

class Designer implements JsonSerializable{
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
	* @param int $id The id of user
	* @param string $name The stage name
	*/	
	public function doPost($id, $name) {
		$mysqli = $this->mysqli;
		
		$sql = "UPDATE profile SET name='%s' WHERE userId=%d";
		$sql = sprintf($sql, $name, $id);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
}