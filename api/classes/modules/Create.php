<?php 

namespace modules;

use common\Alliteration as Alliteration;
use \Exception as Exception;

class Create {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;		
	}
	
	public function doPost($userId) {
		$obj = new \stdClass;
		$stageName = (new Alliteration())->getName();
		$category = (new Alliteration())->getName();
		$layer = array("background", "body", "fur", "eyes", "mouth", "accessorie");
		
		$sql = "UPDATE profile SET stageName='%s', modified=NOW() WHERE userId=%d";
		$sql = sprintf($sql, $stageName, $userId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		} else {
			$obj->stageName = $stageName;
		}
			
		$sql = "INSERT INTO Category SET name='%s', modified=NOW()";
		$sql = sprintf($sql, $category);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		} else {
			$categoryId = $mysqli->insert_id;
			$obj->category = $category;
			$obj->categoryId = $categoryId;
			$obj->layer = array();
			
			foreach ($layer as $index => $name) {
				$sql = "INSERT INTO layer SET categoryId=%d, name='%s', modified=NOW()";
				$sql = sprintf($sql, $categoryId, $name);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				} else {
					$layerId = $mysqli->insert_id;
					$obj = new \stdClass;
					$obj->layerId = layerId;
					$obj->layerName = $name;
					array_push($obj->layer, $obj);
				}					
			}
		}
						
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}
}