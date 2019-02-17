<?php 
namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* Preparation for building an own avatar based on selected files. 
* The user selected from each layer one file with its file id.
* For instance 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be build after payment.
* Over the booking process selected files will be hold. These files
* are reserved and not selectable for an furter booking process.
* 
*/
class Booking {
	private $mysqli;
	
	public function jsonSerialize() {
		return array(
			'success' => true
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param string $userId The id of current user	
	* @param array $fileIds The id as array or comma separated list
	*/		
	public function doPost($userId, $fileIds) {
		$mysqli = $this->mysqli;		
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}

		// cleanup file list owned by others
		$fileIds = array();
		$sql = "SELECT id, ownerId FROM file WHERE ownerId = 0 AND id IN (%d);";		
		$sql = sprintf($sql, implode(",", $ids));
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				array_push($fileIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		$ids = unserialize(serialize($fileIds));
		
		$found = 0;
		$sql = "SELECT id, fileId, modified FROM user_booking WHERE fileId IN (%d);";		
		$sql = sprintf($sql, implode(",", $ids));
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$found = 1;
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if ($found == 1) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Acceptable'), 406);
		}
		
		if ($userId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
			
		if (count($ids) > 0) {
			if ($mysqli->query("LOCK TABLES user_booking WRITE;") === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
			foreach ($ids as $index => $fileId) {
				$sql = "INSERT INTO user_booking SET userId='%s', fileId='%s%', modified=NOW()";
				$sql = sprintf($sql, $userId, $fileId);
				if ($mysqli->query($sql) === false) {
					throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
				}		
			}
			if ($mysqli->query("UNLOCK TABLES;") === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
}