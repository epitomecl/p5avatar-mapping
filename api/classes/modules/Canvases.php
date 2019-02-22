<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/** 
* If user session is alive, start filtering for available canvases. 
* Searching for name, supports pagination, order by modified or simply returns current Top Five.
* Returns a list with names, expected currencies, sizes, file counter and canvas ids.
*/
class Canvases {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	public function doGet() {
		$mysqli = $this->mysqli;
		$data = array();
		
		$sql = "SELECT canvas.id, canvas.name, canvas.currency, user_canvas.userId, ";
		$sql .= "IF(X.counter IS NULL, 0, X.counter) AS counter ";
		$sql .= "FROM canvas LEFT JOIN user_canvas ON (user_canvas.canvasId = canvas.id) ";
		$sql .= "LEFT JOIN (SELECT layer.canvasId, COUNT(*) AS counter FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) GROUP BY layer.canvasId) X ";
		$sql .= "ON (X.canvasId = canvas.id) ";
		$sql .= "ORDER BY counter DESC, canvas.modified DESC ";
		$sql .= "LIMIT 0,5;";	
	
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$row["name"] = ucfirst($row["name"]);
				
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		if (empty($data)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
				
		echo json_encode($data, JSON_UNESCAPED_UNICODE);		
	}
	
	/**
	* something describes this method
	*
	* @param string $search The search term
	* @param string $order The order asc or desc
	* @param int $start The start number
	* @param int $offset The offset	counter
	*/	
	public function doPost($search, $order, $start, $offset) {
		$mysqli = $this->mysqli;
		$search = strip_tags(stripcslashes(trim($search)));
		$order = strtoupper(substr(strip_tags(stripcslashes(trim($order))), 0, 4));
		$data = array();
		
		if (!in_array($order, array("ASC", "DESC"))) {
			$order = "DESC";
		}
		
		$sql = "SELECT canvas.id, canvas.name, canvas.currency, user_canvas.userId, ";
		$sql .= "IF(X.counter IS NULL, 0, X.counter) AS counter ";
		$sql .= "FROM canvas LEFT JOIN user_canvas ON (user_canvas.canvasId = canvas.id) ";
		$sql .= "LEFT JOIN (SELECT layer.canvasId, COUNT(*) AS counter FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) GROUP BY layer.canvasId) X ";
		$sql .= "ON (X.canvasId = canvas.id) ";	
		$sql .= "WHERE name LIKE '%%%s%%' ";
		$sql .= "ORDER BY counter DESC, canvas.modified %s ";
		$sql .= "LIMIT %d,%d;";
		$sql = sprintf($sql, $search, $order, $start, $offset);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$row["name"] = ucfirst($row["name"]);
				
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error." ".$sql), 507);
		}

		echo json_encode($data, JSON_UNESCAPED_UNICODE);		
	}
}