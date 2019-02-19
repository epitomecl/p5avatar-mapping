<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/** 
* If user session is alive, user can update current layer name. 
* The position determind the order of each image layer. 
* Lower position is similar to the bottom and higher position is related near to top. 
* PUT insert a new layer. POST update a layer. GET gives an json structure about requested layer.
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
	* @param int $layerId The id of layer
	* @param string $name The name
	* @param int $position The position
	*/	
	public function doPost($layerId, $name, $position) {
		$mysqli = $this->mysqli;
		$name = strip_tags(stripcslashes(trim($name)));
		
		if (strlen($name) > 0) {
			$sql = "UPDATE layer SET name='%s', position=%d WHERE id=%d";
			$sql = sprintf($sql, $name, $position, $layerId);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
			if ($mysqli->affected_rows == 0 || $layerId == 0) {
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
	* @param int $canvasId The id of canvas	
	* @param string $name The name without spaces
	* @param int $position The position
	*/	
	public function doPut($canvasId, $name, $position) {
		$mysqli = $this->mysqli;
		$name = strip_tags(stripcslashes(trim($name)));
		
		if ($canvasId > 0 && strlen($name) > 0) {
			$sql = "INSERT INTO layer SET canvasId=%d, name='%s', position=%d, modified=NOW()";
			$sql = sprintf($sql, $canvasId, $name, $position);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this),  'Not Acceptable'), 406);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}	
	
	/**
	* something describes this method
	*
	* @param int $layerId The id of layer
	*/	
	public function doGet($layerId) {
		$mysqli = $this->mysqli;
		$data = NULL;
		
		$sql = "SELECT id, name FROM layer WHERE id=%d;";
		$sql = sprintf($sql, $layerId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$data = new \stdClass;
				$data->id = $row["id"];
				$data->name = trim($row["name"]);
				$data->fileIds = array();
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		$sql = "SELECT file.id FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE file.layerId=%d;";
		$sql = sprintf($sql, $layerId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data->fileIds, intval($row["id"]));
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