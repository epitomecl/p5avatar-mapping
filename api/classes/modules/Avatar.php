<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;

/**
* Build cute cat avatar based on default avatar by POST, if address don't matched with existing avatar.
* Lookup for existing avatar data owned by someone related on given userId by GET.
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
		$mysqli = $this->mysqli;
		$path = realpath(dirname(__FILE__).'/../../images/avatars')."/";
		$fileName = "";
		$obj = NULL;
		$sql = sprintf("SELECT filename FROM avatar WHERE address = '%s'", $address);
		
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$fileName = trim($row["filename"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		if (empty($fileName) || !file_exists($path.$fileName)) {
			$builder = new AvatarBuilder();
			$obj = $builder->buildAvatar($address);
		} else {
			$image = $this->getImageFromFile($path, $fileName);
			$source = jpg2string($image);

			$obj = new \stdClass;
			$obj->address = $address;
			$obj->imageData = sprintf("data:image/jpeg;base64,%s", base64_encode($source));
		}
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}

	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;		
		$data = array();
		
		$avatarIds = array();
		$sql = "SELECT id, address, filename FROM avatar WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$obj = new \stdClass();
				$obj->id = intval($row["id"]);
				$obj->address = trim($row["address"]);
				$obj->fileName = trim($row["filename"]);
				$obj->fileIds = array();
				
				array_push($data, $obj);
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		foreach ($data as $index => $avatar) {
			$fileIds = array();
			$sql = "SELECT af.fileId, layer.position FROM avatar_file af ";
			$sql .= "LEFT JOIN avatar ON (avatar.id = af.avatarId) ";
			$sql .= "LEFT JOIN file ON (file.id = af.fileId) ";			
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "WHERE avatar.userId=%d AND af.avatarId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $avatar->id);

			if ($result = $mysqli->query($sql)) {
				while ($row = $result->fetch_assoc()) {
					array_push($fileIds, intval($row["fileId"]));
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
			}
			
			$avatar->fileIds = $fileIds;
		}		
		
		$obj = new \stdClass();
		$obj->avatar = $data;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}	

	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	* @param int $avatarId The id of avatar
	*/		
	public function doDelete($userId, $avatarId) {
		$mysqli = $this->mysqli;	
		
		$this->removeAvatar($mysqli, $userId, $avatarId);
		
		$this->doGet($userId);		
	}
	
	private function removeAvatar($mysqli, $userId, $avatarId) {
		$path = realpath(dirname(__FILE__).'/../../images/avatars')."/";
		$files = array();
		
		$sql = "SELECT id, filename FROM avatar WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$id = intval($row["id"]);
				$files[$id] = trim($row["filename"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}

		if (array_key_exists($avatarId, $files)) {
			if (file_exists($path.$files[$avatarId])) {
				if (unlink($path.$files[$avatarId])) {
					$sql = "DELETE FROM avatar_file WHERE avatarId IN (%s) AND avatarId=%d;";
					$sql = sprintf($sql, implode(",", array_keys($files)), $avatarId);
					if ($mysqli->query($sql) === false) {
						throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
					}
					
					$sql = "DELETE FROM avatar WHERE userId=%d AND id=%d;";
					$sql = sprintf($sql, $userId, $avatarId);
					if ($mysqli->query($sql) === false) {
						throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
					}
				}
			}
		}
	}
	
	private function jpg2string($image) {
		ob_start();
		imagejpeg($image, NULL, 75);
		$data = ob_get_contents();
		ob_end_clean(); 
		
		return $data;		
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
		
		return $image;
	}
}