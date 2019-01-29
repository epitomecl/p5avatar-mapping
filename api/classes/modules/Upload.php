<?php 

namespace modules;

/**
* If user session is alive, files will be uploaded and transformed into png. 
*/
class Upload {
	private $mysqli;
	private $path;
	
	public function __construct($mysqli, $path) {
		$this->mysqli = $mysqli;
		$this->path = $path;
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
	* @param file $file The uploaded files (single or multiple)
	* @param string $layerId The id of current layer
	* @param string $divId The id of current div element holding the image
	* @param array $unlink The checkboxes with all ids marked for deleting
	*/		
	public function doPost($file, $layerId, $divId, $unlink) {
		$mysqli = $this->mysqli;
		$uploads = $this->reArrayFiles($file);
		$path = $this->path;
		$files = array();	

		// remove unlinked files
		foreach ($unlink as $id) {
			$fileId = intval($id);
			$sql = sprintf("SELECT filename, original FROM file WHERE id=%d", $fileId);
			$result = $mysqli->query($sql);
			while ($row = $result->fetch_array()){
				$filename = $row["filename"];
				$original = $row["original"];
				
				if (file_exists($path.$filename)) {
					unlink($path.$filename);
				}
				if (!file_exists($path.$filename)) {
					$sql = sprintf("DELETE FROM file WHERE id=%d", $fileId);
					$mysqli->query($sql);
					
					$obj = new stdclass;
					$obj->divId = $div;
					$obj->fileId = $fileId;
					$obj->assigned = false;
					$obj->filename = $filename;
					$obj->original = $original;
					array_push($files, $obj);			
				}
			}
		}

		// move uploaded files
		foreach ($uploads as $key => $file) {
			if (intval($file["error"]) == UPLOAD_ERR_OK) {
				$tmp_name = $file["tmp_name"];
				$original = $file["name"];
				$file_ext = strtolower(end(explode('.', $original)));
				$filename = sprintf("%d_%s.%s", time(), md5($original), $file_ext);
				$moved = move_uploaded_file($tmp_name, $path.$filename);

				if( $moved ) {
					$obj = new stdclass;
					$obj->divId = $divId;
					$obj->fileId = 0;
					$obj->assigned = false;
					$obj->filename = $filename;
					$obj->moved = true;
					$obj->original = $original;
					array_push($files, $obj);
				}			
			}	
		}

		$userId = getUser($mysqli, $layerId, session_Id());
		
		if ($userId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), "User not exist."), 404);
		} else {
			// insert uploaded files
			foreach ($files as $key => $obj) {
				$filename = $obj->filename;
				$original = $obj->original;
				$fileId = $obj->fileId;
				$sql = sprintf("INSERT INTO file SET filename='%s', original='%s', layerId=%d, userId=%d", $filename, $original, $layerId, $userId);
					
				if (empty($fileId)) {
					$result = $mysqli->query($sql);

					if ($result === TRUE) {
						$obj->fileId = $mysqli->insert_id;
						$obj->restore = true;
						$obj->assigned = true;
					}
				}
			}
		}
			
		echo json_encode($files, JSON_UNESCAPED_UNICODE );
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
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if ($found == 0) {
			$sql = "SELECT userId FROM user_login WHERE sessionId = '%s'";
			$sql = sprintf($sql, $sessionId);

			if ($result = $mysqli->query($sql)) {
				if ($row = $result->fetch_assoc()) {
					$found = intval($row["userId"]);
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		}
		
		return $found;
	}	
}