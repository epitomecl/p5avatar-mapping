<?php 

namespace admin;

class FileUpload {
	
	public function __construct() {
		
	}
	
	private function reArrayFiles($postFiles, $name) {
		$files = array();
		
		if(!empty($postFiles[$name]['tmp_name'])) {
			if(count($postFiles[$name]['tmp_name']) > 1) {
				for($i = 0; $i < count($postFiles[$name]['tmp_name']); $i++) {
					if(!empty($postFiles[$name]['tmp_name']) && is_uploaded_file($postFiles[$name]['tmp_name'][$i])) {
						# we're dealing with multiple uploads
						$handle = array();
						$handle['name']	 = $postFiles[$name]['name'][$i];
						$handle['size']	 = $postFiles[$name]['size'][$i];
						$handle['type']	 = $postFiles[$name]['type'][$i];
						$handle['tmp_name'] = $postFiles[$name]['tmp_name'][$i];
						array_push($files, $handle);
					}
				}
			} else {
				if(!empty($postFiles[$name]['tmp_name']) && is_uploaded_file($postFiles[$name]['tmp_name'])) {
					# we're handling a single upload
					$handle = array();
					$handle['name']	 = $postFiles[$name]['name'];
					$handle['size']	 = $postFiles[$name]['size'];
					$handle['type']	 = $postFiles[$name]['type'];
					$handle['tmp_name'] = $postFiles[$name]['tmp_name'];
					array_push($files, $handle);
				}
			}
		}

		return $files;
	}
	
	public function execute($postFiles, $path, $layerId, $divId, $ids) {
		$uploads = reArrayFiles($postFiles, "file");

		$files = array();	

		// remove unlinked files
		foreach ($ids as $id) {
			$pkId = intval($id);
			$sql = sprintf("SELECT filename, original FROM layer WHERE id=%d", $pkId);
			$result = sql_query($sql);
			while ($row = sql_fetch_array($result)){
				$filename = $row["filename"];
				$original = $row["original"];
				
				if (file_exists($path.$filename)) {
					unlink($path.$filename);
				}
				if (!file_exists($path.$filename)) {
					$sql = sprintf("DELETE FROM layer WHERE id=%d", $pkId);
					sql_query($sql);
					
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
		foreach ($files as $key => $file) {
			$filename = $file->filename;
			$original = $file->original;
			$pkId = $file->pkId;
			$sql = sprintf("INSERT INTO layer SET filename='%s', original='%s', atclNo=%d, mb_id='%s'", $filename, $original, $atclNo, $mb_id);
				
			if (empty($pkId)) {
				$result = sql_query($sql);

				if ($result === TRUE) {
					$obj->pkId = sql_insert_id();
					$obj->restore = true;
					$obj->assigned = true;
				}
			}
		}

		echo json_encode($files, JSON_UNESCAPED_UNICODE );
	}
}