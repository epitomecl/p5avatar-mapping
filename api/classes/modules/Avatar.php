<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;

/**
* Build cute cat avatar based on default avatar by POST.
* Lookup for an existing avatar owned by someone related to given address by GET.
*/
class Avatar {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param string $address The address of a wallet		
	*/		
	public function doPost($address) {
		$builder = new AvatarBuilder();

		$obj = $builder->buildAvatar($address);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param string $address The address of a wallet		
	*/		
	public function doGet($address) {
		$mysqli = $this->mysqli;
		$path = realpath(dirname(__FILE__).'/../../images/avatars')."/";
		$address = strip_tags(stripcslashes(trim($address)));
		$fileName = "";
		$sql = sprintf("SELECT filename FROM user_avatar WHERE address = '%s'", $address);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$fileName = trim($row["filename"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		header('Content-Type: image/png');
		
		$this->sendPngDataFromFile($path, $fileName);
	}
	
	private function sendPngDataFromFile($path, $fileName) {
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
		
		if(!$image) {
			/* create black image */
			$image  = imagecreatetruecolor(256, 256);
			$bgc = imagecolorallocate($image, 255, 255, 255);
			$tc  = imagecolorallocate($image, 0, 0, 0);

			imagefilledrectangle($image, 0, 0, 256, 256, $bgc);
			
			if (strlen($fileName) > 0) {
				imagestring($image, 1, 5, 5, 'No file ' . $fileName, $tc);
			} else {
				imagestring($image, 1, 5, 5, 'No avatar for this address', $tc);
			}
		}		
		
		imagepng($image);
		imagedestroy($image);
	}	
}