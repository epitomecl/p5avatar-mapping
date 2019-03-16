<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;
use \Exception as Exception;

/**
* Build preview of avatar based on selected files. 
* The user selected from each layer one file with its file id.
* For instance 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be build.
* Avatar in use will be marked. 
* GET use canvas id for generating random preview.
* 
*/
class Preview {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param array $fileIds The id as array or comma separated list
	*/		
	public function doPost($fileIds) {
		$mysqli = $this->mysqli;		
		$builder = new AvatarBuilder();
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}

		$canvasId = $this->getCanvasId($mysqli, $ids);
		
		$data = $this->getCanvasData($mysqli, $canvasId) ;
		$width = $data->width;
		$height = $data->height;
		$obj = $builder->previewAvatar($mysqli, $ids, $width, $height);
		$obj->canvas = $data->name;		
		$obj->currency = $data->currency;
		$obj->fee = $this->getFee($mysqli, $ids);
		$obj->fileIds = $ids;
		$obj->hashtags = $this->getHashtags($mysqli, $canvasId);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $canvasId The canvas id
	*/		
	public function doGet($canvasId) {
		$mysqli = $this->mysqli;		
		$builder = new AvatarBuilder();
		$ids = array();
		
		$data = array();
		$sql = "SELECT file.id, file.layerId FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE canvas.id=%d;";
		$sql = sprintf($sql, $canvasId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$id = $row["id"];
				$layerId = $row["layerId"];
				
				if (array_key_exists($layerId, $data)) {
					$tmp = $data[$layerId];
					array_push($tmp, $id);
					$data[$layerId] = $tmp;
				} else {
					$data[$layerId] = array($id);
				}
			}
			
			$keys = array_keys($data);
			foreach ($keys as $index => $key) {
				$tmp = $data[$key];
				array_push($ids, $tmp[array_rand($tmp)]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}
		
		$data = $this->getCanvasData($mysqli, $canvasId) ;
		$width = $data->width;
		$height = $data->height;
		$obj = $builder->previewAvatar($mysqli, $ids, $width, $height);
		$obj->canvas = $data->name;
		$obj->currency = $data->currency;
		$obj->fee = $this->getFee($mysqli, $ids);
		$obj->fileIds = $ids;
		$obj->hashtags = $this->getHashtags($mysqli, $canvasId);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	private function getCanvasData($mysqli, $canvasId) {
		$data = array();
		
		$sql = "SELECT id, name, width, height, currency FROM canvas ";
		$sql .= "WHERE canvas.id=%d;";
		$sql = sprintf($sql, $canvasId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if (empty($data)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);			
		}	
		
		$obj = new \stdClass();
		$obj->name = trim($data[0]["name"]);
		$obj->width = intval($data[0]["width"]);
		$obj->height = intval($data[0]["height"]);
		$obj->currency = trim($data[0]["currency"]);
		
		return $obj;
	}
	
	private function getHashtags($mysqli, $canvasId) {
		$data = array();
		
		$sql = "SELECT id, hashtag FROM hashtag ";
		$sql .= "WHERE canvasId=%d;";
		$sql = sprintf($sql, $canvasId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, trim($row["hashtag"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		return $data;
	}
	
	private function getCanvasId($mysqli, $fileIds) {
		$canvasId = 0;
		
		$sql = "SELECT canvas.id AS canvasId, canvas.name AS canvasName, ";
		$sql .= "layer.name AS layerName, file.id AS fileId, ";
		$sql .= "CONCAT(canvas.name,'_',canvas.id,'/',filename) AS fileName ";
		$sql .= "FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= sprintf("WHERE file.id IN (%s) ", implode(",", $fileIds));
		$sql .= "ORDER BY layer.position ";

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$canvasId = intval($row["canvasId"]);
				
				if ($canvasId > 0) {
					break;
				}
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}		
		
		return $canvasId;
	}
	
	private function getFee($mysqli, $ids) {
		$fee = 0.0;
		$sql = "SELECT SUM(fee) as fee FROM file WHERE id IN (%s);";
		$sql = sprintf($sql, implode(",", $ids));
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$fee = $row["fee"];
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		return sprintf('%g', $fee);
	}
}