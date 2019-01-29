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
	* @param int $layer The id for current layer
	* @param double $price The price as number
	* @param string $currency The currency as shortcut
	*/	
	public function doPost($layer, $price, $currency) {
		$mysqli = $this->mysqli;
		
		$sql = "UPDATE layer SET price='%s', currency='%s' WHERE id=%d";
		$sql = sprintf($sql, $price, $currency, $layer);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
}