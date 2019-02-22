<?php 

namespace modules;

use \Exception as Exception;

/**
* If user session is alive, given file id will changed into file.
* GET deliver the current file into browser. POST deliver requested image as json data structure.
*/
class Image {
	private $mysqli;	
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param int $fileId The fileId for current image (file)
	*/	
	public function doPost($fileId) {
		$mysqli = $this->mysqli;
		$path = realpath(dirname(__FILE__).'/../../images/presets')."/";
		$canvasName = "";
		$fileName = "";
		$width = 0;
		$height = 0;
		$fee;
		$currency = "";
		$sql = "SELECT file.id, file.userId, filename, original, fee, currency, width, height, ";
		$sql .= "CONCAT(canvas.name,'_',canvas.id,'/') AS canvasname FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";		
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE file.id=%d;";
		$sql = sprintf($sql, $fileId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$canvasName = trim($row["canvasname"]);
				$fileName = trim($row["filename"]);
				$width = intval($row["width"]);
				$height = intval($row["height"]);
				$fee = $row["fee"];
				$currency = $row["currency"];
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		$image = $this->getImageFromFile($path.$canvasName, $fileName);
		
		if (!isset($image)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);	
		}
		
		$data = new \stdClass();
		$data->fileId = $fileId;
		$data->fileName = $fileName;
		$data->width = $width;
		$data->height = $height;
		$data->fee = sprintf('%g', $fee);
		$data->currency = $currency;
		$data->imageData = $this->getPngImageData($image);
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	/**
	* something describes this method
	*
	* @param int $fileId The fileId for current price
	*/	
	public function doGet($fileId) {
		$mysqli = $this->mysqli;
		$path = realpath(dirname(__FILE__).'/../../images/presets')."/";
		$canvasName = "";
		$fileName = "";
		$sql = "SELECT file.id, file.userId, filename, original, ";
		$sql .= "CONCAT(canvas.name,'_',canvas.id,'/') AS canvasname FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";		
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE file.id=%d;";
		$sql = sprintf($sql, $fileId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$canvasName = trim($row["canvasname"]);
				$fileName = trim($row["filename"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		$image = $this->getImageFromFile($path.$canvasName, $fileName);
		
		if (!isset($image)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);	
		}
		
		header('Content-Type: image/png');
		imagepng($image);
		imagedestroy($image);
	}
	
	private function getPngImageData($image) {
		$tmp = NULL;
		ob_start();
		imagepng($image);
		$tmp = ob_get_contents();
		ob_end_clean(); 		
		imagedestroy($image);
		
		return sprintf("data:image/png;base64,%s", base64_encode($tmp));		
	}
	
	private function getImageFromFile($path, $fileName) {
		$image = NULL;
		
		if (strlen($fileName) > 0 && file_exists($path.$fileName)) {
			$tmp = explode(".", $fileName);
			$extension = strtolower(end($tmp));
			switch ($extension) {
				case "jpeg":						
				case "jpg":
					$image = imagecreatefromjpeg($path.$fileName);					
					break;
				case "png":
					$image = imagecreatefrompng($path.$fileName);				
					break;	
				case "gif":
					$image = imagecreatefromgif($path.$fileName);				
					break;
			}
		}
		
		return $image;
	}		
}	