<?php 
namespace common;

use \Exception as Exception;

class AvatarBuilder {
	
	public function __construct() {
	}
	
	private function imagefillroundedrect($im,$x,$y,$cx,$cy,$rad,$col) {
		// Draw the middle cross shape of the rectangle
		imagefilledrectangle($im,$x,$y+$rad,$cx,$cy-$rad,$col);
		imagefilledrectangle($im,$x+$rad,$y,$cx-$rad,$cy,$col);

		$dia = $rad*2;

		// Now fill in the rounded corners
		imagefilledellipse($im, $x+$rad, $y+$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $x+$rad, $cy-$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $cx-$rad, $cy-$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $cx-$rad, $y+$rad, $rad*2, $dia, $col);
	}
	
	private function prepareBackground($image) {
		$data = NULL;
		$width = intval(imagesx($image));
		$height = intval(imagesy($image));
		//create masking
		$mask = imagecreatetruecolor($width, $height);
		$white   = imagecolorallocate($mask, 255, 255, 255);
		imagefill($mask, 0, 0, $white);
		// transparent rounded backgound
		$transparent = imagecolorallocate($mask, 255, 0, 0);
		imagecolortransparent($mask, $transparent);
		$this->imagefillroundedrect($mask, 0, 0, $width, $height, 10, $transparent);

		imagecopymerge($image, $mask, 0, 0, 0, 0, $width, $height, 100);
		imagecolortransparent($image, $white);
		
		return $this->png2string($image);
	}
	
	private function png2string($image) {
		ob_start();
		imagepng($image);
		$data = ob_get_contents();
		ob_end_clean(); 
		
		return $data;		
	}
	
	private function defaultAvatar($seed='') {
		// init random seed
		if($seed) srand( hexdec(substr(md5($seed),0,6)) );

		// throw the dice for body parts
		$parts = array(
			'background' => rand(1,16),
			'body' => rand(1,15),
			'fur' => rand(1,8),
			'eyes' => rand(1,15),
			'mouth' => rand(1,10),
			'accessorie' => rand(1,18)
		);

		// create backgound
		$avatar = imagecreatetruecolor(256, 256) or die("GD image create failed");
		$white   = imagecolorallocate($avatar, 255, 255, 255);
		imagefill($avatar,0,0,$white);

		// add parts
		foreach($parts as $part => $num){
			$file = dirname(__FILE__).'/../../images/cat/'.$part.'_'.$num.'.png';

			$im = imagecreatefrompng($file);
			if(!$im) die('Failed to load '.$file);
			imageSaveAlpha($im, true);
			imagecopy($avatar,$im,0,0,0,0,256,256);
			imagedestroy($im);
		}
	
		// restore random seed
		if($seed) srand();
		
		$data = new \stdClass;
		$data->canvas = "cat";
		$data->parts = $parts;
		$data->avatar = $avatar;
		
		return $data;
	}

	public function buildAvatar($address) {
		$hash = hash('sha256', $address);			
		$object = $this->defaultAvatar($hash);
		$image = $object->avatar;
		$source = $this->prepareBackground($image);
		
		$data = new \stdClass;
		$data->address = $address;
		$data->sha256 = $hash;
		$data->canvas = $object->canvas;		
		$data->parts = $object->parts;
		$data->imageData = sprintf("data:image/png;base64,%s", base64_encode($source));

		return $data;
	}
	
	public function previewAvatar($mysqli, $fileIds, $width, $height) {
		$data = array();
		
		$sql = "SELECT canvas.name as canvasName, ";
		$sql .= "layer.name as layerName, file.id as fileId, ";
		$sql .= "CONCAT(canvas.name,'_',canvas.id,'/',filename) AS fileName ";
		$sql .= "FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= sprintf("WHERE file.id IN (%s) ", implode(",", $fileIds));
		$sql .= "ORDER BY layer.position ";

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		// create backgound
		$avatar = imagecreatetruecolor($width, $height) or die("GD image create failed");
		$white   = imagecolorallocate($avatar, 255, 255, 255);
		imagefill($avatar,0,0,$white);

		// add parts
		$canvas = "";
		$parts = array();
		foreach($data as $index => $row){
			$canvas = trim($row["canvasName"]);
			$filename = trim($row["fileName"]);
			$layer = trim($row["layerName"]);
			$id =  intval($row["fileId"]);
			$parts[$layer] = $id;
			$file = dirname(__FILE__).'/../../images/presets/'.$filename;

			$im = imagecreatefrompng($file);
			if(!$im) die('Failed to load '.$file);
			imageSaveAlpha($im, true);
			imagecopy($avatar, $im, 0, 0, 0, 0, $width, $height);
			imagedestroy($im);
		}
		 
			
			// $sql = "SELECT fileId FROM basket_file WHERE fileId IN (%d);";		
			// $sql = sprintf($sql, implode(",", $fileIds));
			// if ($result = $mysqli->query($sql)) {
				// while ($row = $result->fetch_assoc()) {
					// $fileId = intval($row["fileId"]);
					// $text = sprintf("N/A %d", $ownerId);
				
					// if (intval($row["ownerId"]) > 0) {
						// imagestring($avatar, 5, 5, 5, $text, $tc);						
						// break;
					// }
				// }
			// } else {
				// throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			// }
		
		//$source = $this->prepareBackground($avatar);
		$source = $this->png2string($avatar);
		
		$data = new \stdClass;
		$data->canvas = ucfirst($canvas);		
		$data->parts = $parts;
		$data->width = $width;
		$data->height = $height;
		$data->imageData = sprintf("data:image/png;base64,%s", base64_encode($source));

		return $data;
	}
	
	public function getAvatarImageSource($mysqli, $fileIds) {
		$data = array();
		
		$sql = "SELECT canvas.name as canvasName, layer.name as layerName, file.id as fileId, ";
		$sql .= "CONCAT(canvas.name,'_',canvas.id,'/',filename) AS fileName ";
		$sql .= "FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= sprintf("WHERE file.id IN (%s) ", implode(",", $fileIds));
		$sql .= "ORDER BY layer.position ";

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		// create backgound
		$avatar = imagecreatetruecolor(256, 256) or die("GD image create failed");
		$white   = imagecolorallocate($avatar, 255, 255, 255);
		imagefill($avatar,0,0,$white);

		foreach($data as $index => $row){
			$filename = trim($row["fileName"]);
			$file = dirname(__FILE__).'/../../images/presets/'.$filename;

			$im = imagecreatefrompng($file);
			if(!$im) die('Failed to load '.$file);
			imageSaveAlpha($im, true);
			imagecopy($avatar,$im,0,0,0,0,256,256);
			imagedestroy($im);
		}
		
		return $avatar;
	}	
}

	