<?php

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, profile data will be managed.
* A POST request will update profile data.
* A GET request will respond current profile data.
* An existing profile picture will independently from there original 
* type always as base64 encoded png file image source delivered.
*/
class Profile  implements JsonSerializable{
	private $mysqli;
	
	public function jsonSerialize() {
		return array(
			'success' => true
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	private function reArrayFiles($postFiles) {
		$files = array();
		
		if(!empty($postFiles['tmp_name'])) {
			if(count($postFiles['tmp_name']) > 1) {
				for($i = 0; $i < count($postFiles['tmp_name']); $i++) {
					if(!empty($postFiles['tmp_name']) && is_uploaded_file($postFiles['tmp_name'][$i])) {
						# we're dealing with multiple uploads
						$handle = array();
						$handle['name']	 = $postFiles['name'][$i];
						$handle['size']	 = $postFiles['size'][$i];
						$handle['type']	 = $postFiles['type'][$i];
						$handle['tmp_name'] = $postFiles['tmp_name'][$i];
						array_push($files, $handle);
					}
				}
			} else {
				if(!empty($postFiles['tmp_name']) && is_uploaded_file($postFiles['tmp_name'])) {
					# we're handling a single upload
					$handle = array();
					$handle['name']	 = $postFiles['name'];
					$handle['size']	 = $postFiles['size'];
					$handle['type']	 = $postFiles['type'];
					$handle['tmp_name'] = $postFiles['tmp_name'];
					array_push($files, $handle);
				}
			}
		}

		return $files;
	}
	
	/**
	* something describes this method
	*
	* @param file $picture The post file parameter
	* @param string $base64imagedata The imageData base64
	* @param string $path The file path to image profile folder
	* @param string $original The fileName as last known profile fileName
	*/
	private function storeProfileImage($picture, $base64imagedata, $path, $original) {
		$tmp = explode(".", $original);
		$extension = strtolower(end($tmp));
		
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		
		if (!empty($imageData) && strlen($extension) > 0) {
			$fileName = sprintf("%s.%s", md5($original.time()), $extension);
			$tmp = explode(",", $base64imagedata, 2);
			$imageData = base64_decode(end($tmp));
			$image = imagecreatefromstring($imageData);
			if ($image !== false) {
				switch ($extension) {
					case "jpeg":						
					case "jpg":
						imagejpeg($image, $path.$filename, 75);
						imagedestroy($image);
						array_push($files, $fileName);						
						break;
					case "png":
						imagepng($image, $path.$filepath);
						imagedestroy($image);
						array_push($files, $fileName);					
						break;	
					case "gif":
						imagegif($image, $path.$filename);
						imagedestroy($image);
						array_push($files, $fileName);					
						break;
				}
			}
		} else {
			$uploads = $this->reArrayFiles($picture);
			
			// move uploaded files
			foreach ($uploads as $key => $file) {
				if (intval($file["error"]) == UPLOAD_ERR_OK) {
					$tmp_name = $file["tmp_name"];
					$original = $file["name"];
					$tmp = explode('.', $original);
					$extension = strtolower(end($tmp));
					$fileName = sprintf("%s.%s", md5($original.time()), $extension);
					$moved = move_uploaded_file($tmp_name, $path.$fileName);

					if( $moved ) {
						array_push($files, $fileName);
					}			
				}

				// here only for one profile picture
				break;
			}
		}
		
		return $files;
	}
	
	private function getPngDataFromFile($path, $fileName) {
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
			$image  = imagecreatetruecolor(150, 30);
			$bgc = imagecolorallocate($image, 255, 255, 255);
			$tc  = imagecolorallocate($image, 0, 0, 0);

			imagefilledrectangle($image, 0, 0, 150, 30, $bgc);
			
			if (strlen($fileName) > 0) {
				imagestring($image, 1, 5, 5, 'No file ' . $fileName, $tc);
			} else {
				imagestring($image, 1, 5, 5, 'No photo', $tc);
			}
		}		
		
		$data = NULL;
		ob_start();
		imagepng($image);
		$data = ob_get_contents();
		ob_end_clean(); 		
		imagedestroy($image);
		
		return sprintf("data:image/png;base64,%s", base64_encode($data));
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of user
	* @param string $firstName The first name of user
	* @param string $lastName The last name of user
	* @param string $alias The alias name as designer
	* @param string $email The email of user
	* @param file $file The foto upload of profile image
	* @param string $imageData The imageData by json foto upload
	*/		
	public function doPost($userId, $firstName, $lastName, $alias, $email, $file, $imageData) {
		$mysqli = $this->mysqli;
		$profileId = 0;
		$path = realpath(dirname(__FILE__).'/../../images/profile')."/";		
		
		// in case of changing firstname and lastname deleting old photo
		$fileName = "";
		$sql = sprintf("SELECT id, photo FROM profile WHERE id = %d", $userId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$fileName = trim($row["photo"]);
				$profileId = intval($row["id"]);
				if (is_file($path.$fileName)) {
					unlink($path.$fileName);
				}
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}				
		
		if ($profileId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);			
		}
		
		$files = $this->storeProfileImage($picture, $imageData, $path, $fileName);
			
		$photo = (count($files) > 0) ? reset($files) : "";
		$firstName = $mysqli->real_escape_string($firstName);
		$lastName= $mysqli->real_escape_string($lastName);
		$alias = $mysqli->real_escape_string($alias);
		$email = $mysqli->real_escape_string($email);
		
		$sql = "UPDATE profile SET firstName='%s', lastName='%s', alias='%s', ";
		$sql .= "email='%s', photo='%s' ";
		$sql .= "WHERE userId=%d";
		$sql = sprintf($sql, $firstName, $lastName, $alias, $email, $photo, $userId);
		if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);			
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of user
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;
		$profileId = 0;
		$path = realpath(dirname(__FILE__).'/../../images/profile')."/";		
		$firstName = "";
		$lastName = ""; 
		$alias = ""; 
		$email = "";
		$fileName = "";
		
		$sql = sprintf("SELECT * FROM profile WHERE id = %d", $userId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$profileId = intval($row["id"]);
				$firstName = trim($row["firstName"]); 
				$lastName = trim($row["lastName"]);
				$alias = trim($row["alias"]);
				$email = trim($row["email"]); 
				$fileName = trim($row["photo"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}	
		
		if ($profileId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);			
		}

		$obj = new \stdClass();
		$obj->firstName = $firstName; 
		$obj->lastName = $lastName; 
		$obj->alias = $alias; 
		$obj->email = $email;
		$obj->imageData = $this->getPngDataFromFile($path, $fileName);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}	
}