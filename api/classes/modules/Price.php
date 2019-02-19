<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, user can update fee for current file.
* GET deliver the current price for a file (fee and currency);
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
	* @param double $fee The fee as number
	*/	
	public function doPost($fileId, $fee) {
		$mysqli = $this->mysqli;
		
		$sql = "UPDATE price SET fee='%s' WHERE fileId=%d";
		$sql = sprintf($sql, $fee, $fileId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		if ($mysqli->affected_rows == 0 || $fileId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $fileId The fileId for current price
	*/	
	public function doGet($fileId) {
		$mysqli = $this->mysqli;
		$data = NULL;
		
		$sql = "SELECT fee, currency FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE file.id=%d;";
		$sql = sprintf($sql, $fileId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$data = new \stdClass;
				$data->fee = $row["fee"];
				$data->currency = trim($row["currency"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		if (empty($data)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}