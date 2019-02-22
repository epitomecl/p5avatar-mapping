<?php 

namespace modules;

use common\Alliteration as Alliteration;
use \Exception as Exception;

/**
* If user session is alive, a new canvas with a basic set of layers will be created.
* The canvas will hold an random artifical name.
* Each canvas has a selection of layers.
* Layernames as default are background, body, fur, eye, mouth and accessorie.
* Width and height are at least 256 pixel. The standard currency is EOS.
*/
class Create {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;		
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The user id
	* @param int $width The width of canvas (standard is 256)
	* @param int $height The height of canvas (standard is 256)
	* @param string $currency The currency of canvas (Standard is EOS).
	*/		
	public function doPost($userId, $width, $height, $currency) {
		$mysqli = $this->mysqli;
		$path = realpath(dirname(__FILE__).'/../../images/presets')."/";
		$canvas = str_replace(" ", "_", (new Alliteration())->getName());
		$layer = array("background", "body", "fur", "eyes", "mouth", "accessorie");
		$width = ($width < 256) ? 256 : $width;
		$height = ($height < 256) ? 256 : $height;
		$currency = empty($currency) ? "EOS" : strip_tags(stripcslashes(trim($currency)));
		$data = new \stdClass;	
		
		$sql = "INSERT INTO canvas SET name='%s', width=%d, height=%d, currency='%s' modified=NOW()";
		$sql = sprintf($sql, $canvas, $width, $height);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		} else {
			$canvasId = $mysqli->insert_id;

			mkdir($path.$canvas."_".$canvasId, 0755, true);
		
			if (!file_exists($path.$canvas."_".$canvasId)) {
				$sql = "DELETE FROM canvas WHERE id=%d";
				$sql = sprintf($sql, $canvasId);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}
								
				throw new Exception(sprintf("%s, %s", get_class($this), 'Precondition Failed'), 412);
			}
			
			$sql = "INSERT INTO user_canvas SET userId=%d, canvasId=%d, modified=NOW()";
			$sql = sprintf($sql, $userId, $canvasId);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
			
			$data->userId = $userId;
			$data->canvas = $canvas;
			$data->canvasId = $canvasId;
			$data->size = json_decode(sprintf("{\"width\":%d, \"height\":%d}", $width, $height));
			$data->layer = array();
			
			foreach ($layer as $index => $name) {
				$sql = "INSERT INTO layer SET canvasId=%d, name='%s', position=%d, modified=NOW()";
				$sql = sprintf($sql, $canvasId, $name, $index + 1);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				} else {
					$layerId = $mysqli->insert_id;
					$obj = new \stdClass;
					$obj->layerId = $layerId;
					$obj->layerName = $name;
					$obj->position = $index + 1;
					array_push($data->layer, $obj);
				}					
			}
		}
						
		echo json_encode($data, JSON_UNESCAPED_UNICODE);			
	}
}