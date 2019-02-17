<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, designer can update price and currency for current layer.
*/
class Price implements JsonSerializable{
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
	* @param int $fileId The fileId for current price
	* @param double $price The price as number
	* @param string $currency The currency as shortcut
	*/	
	public function doPost($fileId, $price, $currency) {
		$mysqli = $this->mysqli;
		
		$sql = "UPDATE price SET price='%s', currency='%s' WHERE fileId=%d";
		$sql = sprintf($sql, $price, $currency, $fileId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		if ($mysqli->affected_rows == 0 || $fileId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
}