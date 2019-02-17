<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, user can update current category (called as name of avatar). 
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
	* @param int $categoryId The id of category
	* @param string $name The name
	* @param string $hashtag The hashtag as comma separated list
	*/	
	public function doPost($categoryId, $name, $hashtag) {
		$mysqli = $this->mysqli;
		$path = dirname(__FILE__).'/../../images/presets/';		
		$name = strip_tags(stripcslashes(trim($name)));
		$hashtag = strip_tags(stripcslashes(trim($hashtag)));
		$categoryName = getCategoryName($mysqli, $categoryId);

		// rename old category path into new category path
		if (strlen($categoryName) > 0 && !file_exists($path.$categoryName)) {
			if (rename($path.$categoryName, $path.$name)) {
				$sql = "UPDATE category SET name='%s' WHERE id=%d";
				$sql = sprintf($sql, $name, $categoryId);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}
			}
		}
		
		// delete old hashtags
		$sql = "DELETE FROM hashtag WHERE categoryId=%d";
		$sql = sprintf($sql, $categoryId);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		// insert new hashtags
		$tokens = explode(",", trim($hashtag));		
		foreach ($tokens as $item) {
			$sql = "INSERT INTO hashtag SET categoryId=%d, hashtag='%s'";
			$sql = sprintf($sql, $categoryId, $mysqli->real_escape_string($item));
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}			
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	private function getCategoryName($mysqli, $categoryId) {
		$value = "";
		$sql = "SELECT CONCAT(category.name,'_',category.id,'/') AS categoryName WHERE id=%d";
		$sql = sprintf($sql, $categoryId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$value = trim($row["categoryName"]);
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		return $value;
	}	
}