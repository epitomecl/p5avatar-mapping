<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/** 
* If user session is alive, user can update current layer name.
* The position determind the order of each image layer. 
* Lower position is similar to the bottom and higher position is related near to top.
*/
class Layer  implements JsonSerializable{
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
	* @param int $categoryId The id of category
	* @param string $name The name
	* @param int $position The position
	*/	
	public function doPost($categoryId, $name, $position) {
		$mysqli = $this->mysqli;
		$name = strip_tags(stripcslashes(trim($name)));
		
		if (strlen($name) > 0) {
			$sql = "UPDATE layer SET name='%s', position=%d WHERE categoryId=%d";
			$sql = sprintf($sql, $name, $position, $id);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
			if ($mysqli->affected_rows == 0 || $categoryId == 0) {
				throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this),  'Not Acceptable'), 406);
		}
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
	
	/**
	* something describes this method
	*
	* @param int $categoryId The id of categor	
	* @param string $name The name without spaces
	* @param int $position The position
	*/	
	public function doPut($categoryId, $name, $position) {
		$mysqli = $this->mysqli;
		$name = strip_tags(stripcslashes(trim($name)));
		
		if ($categoryId > 0 && strlen($name) > 0) {
			$sql = "INSERT INTO layer SET categoryId=%d, name='%s', position=%d, modified=NOW()";
			$sql = sprintf($sql, $categoryId, $name, $position);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this),  'Not Acceptable'), 406);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}	
}