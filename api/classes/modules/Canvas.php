<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, user can update current canvas (called as name of avatar). 
* Hashtags (comma separated) are describing the attributes for searching.
**/
class Canvas implements JsonSerializable{
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
	* @param int $canvasId The id of canvas
	* @param string $name The name
	* @param string $hashtag The hashtag as comma separated list
	*/	
	public function doPost($canvasId, $name, $hashtag) {
		$mysqli = $this->mysqli;
		$path = realpath(dirname(__FILE__).'/../../images/presets')."/";		
		$name = strip_tags(stripcslashes(trim($name)));
		$hashtag = strip_tags(stripcslashes(trim($hashtag)));
		$canvasName = getCanvasName($mysqli, $canvasId);
		
		if (empty($name)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Precondition Failed'), 412);
		}
		
		// rename old canvas path into new canvas path
		if (strlen($canvasName) > 0 && file_exists($path.$canvasName)) {
			if (rename($path.$canvasName, $path.$name)) {
				$sql = "UPDATE canvas SET name='%s' WHERE id=%d";
				$sql = sprintf($sql, $name, $canvasId);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}
			}
		}
		
		// delete old hashtags
		$sql = "DELETE FROM hashtag WHERE canvasId=%d";
		$sql = sprintf($sql, $canvasId);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		// insert new hashtags
		$tokens = explode(",", trim($hashtag));		
		foreach ($tokens as $item) {
			$sql = "INSERT INTO hashtag SET canvasId=%d, hashtag='%s'";
			$sql = sprintf($sql, $canvasId, $mysqli->real_escape_string($item));
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}			
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $canvasId The id of canvas
	*/	
	public function doGet($canvasId) {
		$mysqli = $this->mysqli;
		
		$data = NULL;
		$sql = "SELECT userId, name, currency, ";
		$sql .= "CONCAT('{\"width\":',width,',\"height\":',height,'}') AS size ";
		$sql .= "FROM canvas ";
		$sql .= "LEFT JOIN user_canvas ON (user_canvas.canvasId = canvas.id) ";
		$sql .= "WHERE canvas.id=%d;";
		$sql = sprintf($sql, $canvasId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$data = new \stdClass;
				$data->userId = $row["userId"];
				$data->name = trim($row["name"]);
				$data->currency =  trim($row["currency"]);
				$data->size = json_decode(trim($row["size"]));
				$data->layerIds = array();
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}	

		$sql = "SELECT id, name FROM layer ";
		$sql .= "WHERE canvasId=%d ";
		$sql .= "ORDER BY position;";
		$sql = sprintf($sql, $canvasId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data->layerIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if (empty($data) ) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);			
		}
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);		
	}
	
	private function getCanvasName($mysqli, $canvasId) {
		$value = "";
		$sql = "SELECT CONCAT(canvas.name,'_',canvas.id,'/') AS canvasName WHERE id=%d";
		$sql = sprintf($sql, $canvasId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$value = trim($row["canvasName"]);
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		return str_replace(" ", "_", $value);
	}	
}