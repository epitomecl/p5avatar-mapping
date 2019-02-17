<?php 

namespace modules;

use common\Alliteration as Alliteration;
use \Exception as Exception;

/**
* If user session is alive, a new category with a basic set of layers will be created.
* The category will hold an random artifical name.
* Each category has a selection of layers.
* Layernames as default are background, body, fur, eye, mouth and accessorie.
*/
class Create {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;		
	}
	
	public function doPost($userId) {
		$mysqli = $this->mysqli;
		$path = dirname(__FILE__).'/../../images/presets/';
		$category = (new Alliteration())->getName();
		$layer = array("background", "body", "fur", "eyes", "mouth", "accessorie");
	
		mkdir($path.$category, 0777, true);
		
		if (!file_exists($path.$category)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Precondition Failed'), 412);
		}
		
		$obj = new \stdClass;		
		$sql = "INSERT INTO Category SET name='%s', modified=NOW()";
		$sql = sprintf($sql, $category);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		} else {
			$categoryId = $mysqli->insert_id;
			
			$sql = "INSERT INTO user_category SET userId=%d, categoryId=%d, modified=NOW()";
			$sql = sprintf($sql, $userId, $categoryId);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
			
			$obj->userId = $userId;
			$obj->category = $category;
			$obj->categoryId = $categoryId;
			$obj->layer = array();
			
			foreach ($layer as $index => $name) {
				$sql = "INSERT INTO layer SET categoryId=%d, name='%s', position=%d, modified=NOW()";
				$sql = sprintf($sql, $categoryId, $name, $index + 1);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				} else {
					$layerId = $mysqli->insert_id;
					$obj = new \stdClass;
					$obj->layerId = layerId;
					$obj->layerName = $name;
					$obj->position = $index + 1;
					array_push($obj->layer, $obj);
				}					
			}
		}
						
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}
}