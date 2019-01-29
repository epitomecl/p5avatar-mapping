<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, designer can update current category (name of avatar). 
* Hashtags (comma separated) are describing the attributes for searching.
**/
class Category implements JsonSerializable{
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
	* @param int $id The id of category
	* @param string $name The name
	* @param string $hashtag The hashtag as comma separated list
	*/	
	public function doPost($id, $name, $hashtag) {
		$mysqli = $this->mysqli;
		
		$sql = "UPDATE category SET name='%s' WHERE id=%d";
		$sql = sprintf($sql, $name, $id);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$tokens = explode(trim($hashtag));
		
		$sql = "DELETE FROM hashtag WHERE categoryId=%d";
		$sql = sprintf($sql, $id);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		foreach ($tokens as $item) {
			$sql = "INSERT INTO hashtag SET categoryId=%d, hashtag='%s'";
			$sql = sprintf($sql, $id, $name);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}			
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
}