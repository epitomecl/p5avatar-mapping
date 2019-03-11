<?php 

namespace modules;

use \Exception as Exception;

/**
* If user session is alive, files will be uploaded and transformed into png.
* User can upload multiple files. File is the browser file object. 
* LayerId directed to the layer name for combination of layer name and current number of layers as file name.
* Unlink is the file id for deleting unused file.
* If file is unlinked, it is not longer assigned.
* CardId stands for an elm id inside layout for holding uploaded image.
*/
class Upload {
	private $mysqli;
	private $path;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
		$this->path = realpath(dirname(__FILE__).'/../../images/presets')."/";
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
						$handle['error'] = $postFiles['error'][$i];
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
					$handle['error'] = $postFiles['error'];					
					$handle['tmp_name'] = $postFiles['tmp_name'];
					array_push($files, $handle);
				}
			}
		}

		return $files;
	}
	
	private function getCanvasName($mysqli, $layerId) {
		$value = "";
		$sql = "SELECT CONCAT(canvas.name,'_',canvas.id,'/') AS canvasName FROM layer ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE layer.id=%d";
		$sql = sprintf($sql, $layerId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$value = trim($row["canvasName"]);
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}

		return $value;
	}
	
	/**
	* something describes this method
	*
	* @param file $file The uploaded files (single or multiple)
	* @param string $cardId The html element id of current card (image holder)	
	* @param int $layerId The id of current layer
	*/		
	public function doPost($file, $cardId, $layerId) {
		$mysqli = $this->mysqli;
		$uploads = $this->reArrayFiles($file);
		$path = $this->path;
		$files = array();
		
		$canvasName = $this->getCanvasName($mysqli, $layerId);
		
		if (strlen($canvasName) == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Precondition Failed'), 412);
		}
		
		$userId = $this->getUser($mysqli, $layerId, session_Id());
		
		if ($userId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), "User not exist."), 404);
		}
		
		if (!file_exists($path.$canvasName)) {
			mkdir($path.$canvasName, 0777, true);
		}
		
		// move uploaded files
		foreach ($uploads as $key => $file) {
			if (intval($file["error"]) == UPLOAD_ERR_OK) {
				$tmp_name = $file["tmp_name"];
				$original = $file["name"];
				$tmp = explode('.', $original);
				$extension = strtolower(end($tmp));
				$fileName = sprintf("%s.%s", md5($original.time()), $extension);
				$moved = move_uploaded_file($tmp_name, $path.$canvasName.$fileName);

				if ($moved) {
					$image = $this->getImageFromFile($path.$canvasName, $fileName);
					
					if (isset($image)) {
						if (unlink($path.$canvasName.$fileName)) {
							$fileName = sprintf("%s.%s", md5($original.time()), "png");
							imagepng($image, $path.$canvasName.$fileName);
							imagedestroy($image);
						
							$obj = new \stdClass();
							$obj->cardId = $cardId;
							$obj->fileId = 0;
							$obj->assigned = false;
							$obj->fileName = $fileName;
							$obj->original = $original;
							array_push($files, $obj);
						}
					}
				}			
			}	
		}
		
		// insert uploaded files
		foreach ($files as $key => $obj) {
			$fileName = $obj->fileName;
			$original = $obj->original;
			$fileId = $obj->fileId;
				
			if (empty($fileId)) {
				$sql = sprintf("INSERT INTO file SET filename='%s', original='%s', layerId=%d, userId=%d", $fileName, $original, $layerId, $userId);					
				if ($result = $mysqli->query($sql) === TRUE) {
					$obj->fileId = $mysqli->insert_id;
					$obj->assigned = true;
				} else {
					throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
				}
			}
		}
			
		echo json_encode($files, JSON_UNESCAPED_UNICODE );
	}
	
	/**
	* something describes this method
	*
	* @param array $unlink All file ids marked for deleting
	*/		
	public function doDelete($unlink) {
		$mysqli = $this->mysqli;
		$path = $this->path;
		$data = array();
		$files = array();	
		
		// remove unlinked files
		foreach ($unlink as $id) {
			$fileId = intval($id);
			$sql = "SELECT file.id, CONCAT(canvas.name,'_',canvas.id,'/') AS canvasName, file.original, file.filename ";
			$sql .= "FROM file ";
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
			$sql .= sprintf("WHERE file.id=%d ", $fileId);
			$sql .= "ORDER BY layer.position;";
			
			if ($result = $mysqli->query($sql)) {
				while ($row = $result->fetch_array()){
					array_push($data, $row);
				}
				$result->free();
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
			}
		}
		
		foreach ($data as $index => $row) {
			$fileId = intval($row["id"]);
			$canvasName = $row["canvasName"];
			$fileName = $row["filename"];
			$original = $row["original"];
			
			if (file_exists($path.$canvasName.$fileName)) {
				unlink($path.$canvasName.$fileName);

				if (!file_exists($path.$fileName)) {
					$sql = sprintf("DELETE FROM file WHERE id=%d", $fileId);
					if ($mysqli->query($sql) === false) {
						throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
					} else {
						$obj = new \stdClass();
						$obj->fileId = $fileId;
						$obj->assigned = false;
						$obj->fileName = $fileName;
						$obj->original = $original;
						array_push($files, $obj);	
					}							
				}
			}			
		}
			
		echo json_encode($files, JSON_UNESCAPED_UNICODE );
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
	
	private function getUser($mysqli, $layerId, $sessionId) {
		$sql = "SELECT userId FROM file WHERE layerId = %d";
		$sql = sprintf($sql, $layerId);

		$found = 0;
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$found = intval($row["userId"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		if ($found == 0) {
			$sql = "SELECT userId FROM user_login WHERE sessionId = '%s'";
			$sql = sprintf($sql, $sessionId);

			if ($result = $mysqli->query($sql)) {
				if ($row = $result->fetch_assoc()) {
					$found = intval($row["userId"]);
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
			}
		}
		
		return $found;
	}	
}