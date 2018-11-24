<?php 

namespace admin;

class FileUpload {
	
	public function __construct() {
		
	}
	
	private function reArrayFiles($postFiles, $key) {
		$files = array();
		
		if(!empty($postFiles[$key]['tmp_name'])) {
			if(count($postFiles[$key]['tmp_name']) > 1) {
				for($i = 0; $i < count($postFiles[$key]['tmp_name']); $i++) {
					if(!empty($postFiles[$key]['tmp_name']) && is_uploaded_file($postFiles[$key]['tmp_name'][$i])) {
						# we're dealing with multiple uploads
						$handle = array();
						$handle['name']	 = $postFiles[$key]['name'][$i];
						$handle['size']	 = $postFiles[$key]['size'][$i];
						$handle['type']	 = $postFiles[$key]['type'][$i];
						$handle['tmp_name'] = $postFiles[$key]['tmp_name'][$i];
						array_push($files, $handle);
					}
				}
			} else {
				if(!empty($postFiles[$key]['tmp_name']) && is_uploaded_file($postFiles[$key]['tmp_name'])) {
					# we're handling a single upload
					$handle = array();
					$handle['name']	 = $postFiles[$key]['name'];
					$handle['size']	 = $postFiles[$key]['size'];
					$handle['type']	 = $postFiles[$key]['type'];
					$handle['tmp_name'] = $postFiles[$key]['tmp_name'];
					array_push($files, $handle);
				}
			}
		}

		return $files;
	}
	
	public function execute($postFiles, $key, $path, $layerId, $divId, $ids) {
		$uploads = $this->reArrayFiles($postFiles, $key);

		$files = array();	

		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api//include/db.mysql.ini");
		$mysqli = new \mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);
				
		if (!$mysqli->connect_errno) {
			$mysqli->set_charset("utf8");
			
			// remove unlinked files
			foreach ($ids as $id) {
				$pkId = intval($id);
				$sql = sprintf("SELECT filename, original FROM layer WHERE id=%d", $pkId);
				$result = $mysqli->query($sql);
				while ($row = $result->fetch_array()){
					$filename = $row["filename"];
					$original = $row["original"];
					
					if (file_exists($path.$filename)) {
						unlink($path.$filename);
					}
					if (!file_exists($path.$filename)) {
						$sql = sprintf("DELETE FROM layer WHERE id=%d", $pkId);
						$mysqli->query($sql);
						
						$obj = new stdclass;
						$obj->divId = $divId;
						$obj->pkId = $pkId;
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
						$obj->pkId = 0;
						$obj->assigned = false;
						$obj->filename = $filename;
						$obj->moved = true;
						$obj->original = $original;
						array_push($files, $obj);
					}			
				}	
			}

			// insert uploaded files
			foreach ($files as $key => $obj) {
				$filename = $obj->filename;
				$original = $obj->original;
				$pkId = $obj->pkId;
				$sql = sprintf("INSERT INTO layer SET filename='%s', original='%s', atclNo=%d, mb_id='%s'", $filename, $original, $atclNo, $mb_id);
					
				if (empty($pkId)) {
					$result = $mysqli->query($sql);

					if ($result === TRUE) {
						$obj->pkId = $mysqli->insert_id;
						$obj->restore = true;
						$obj->assigned = true;
					}
				}
			}
			
			$mysqli->close();
		}
		
		echo json_encode($files, JSON_UNESCAPED_UNICODE );
	}
}