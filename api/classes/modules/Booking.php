<?php 
namespace modules;

use \Exception as Exception;

/**
* Preparation for building an own avatar based on selected files. 
* The user selected from each layer one file with its file id.
* For instance 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be build after payment.
* Over the booking process selected files will be hold. 
* These files are reserved.
* 
*/
class Booking {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
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

		// cleanup file list owned by others and previous payment process
		// $fileIds = array();
		// $sql = "SELECT id, ownerId FROM file WHERE ownerId = 0 AND id IN (%d);";		
		// $sql = sprintf($sql, implode(",", $ids));
		// if ($result = $mysqli->query($sql)) {
			// while ($row = $result->fetch_assoc()) {
				// array_push($fileIds, intval($row["id"]));
			// }
		// } else {
			// throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		// }
		// $ids = unserialize(serialize($fileIds));
		
		// $found = 0;
		// $sql = "SELECT COUNT(*) AS counter FROM user_booking WHERE fileId IN (%d);";		
		// $sql = sprintf($sql, implode(",", $ids));
		// if ($result = $mysqli->query($sql)) {
			// if ($row = $result->fetch_assoc()) {
				// $found = intval($row["counter"]);
			// }
		// } else {
			// throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		// }
		
		// if ($found == count($ids)) {
			// throw new Exception(sprintf("%s, %s", get_class($this), $sql.'Not Acceptable'), 406);
		// }
		
		$searchId = 0;
		$sql = "SELECT id AS userId FROM user WHERE id=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$searchId = intval($row["userId"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		if ($searchId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
			
		if (count($ids) > 0) {
			$bookingId = 0;
			$sql = "INSERT INTO booking SET userId=%d, modified=NOW()";
			$sql = sprintf($sql, $userId);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			} else {
				$bookingId = $mysqli->insert_id;
			}			
			
			foreach ($ids as $index => $fileId) {
				if ($fileId > 0 && $bookingId > 0) {
					$sql = "INSERT INTO booking_file SET bookingId=%d, fileId=%d, modified=NOW()";
					$sql = sprintf($sql, $bookingId, $fileId);
					if ($mysqli->query($sql) === false) {
						throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
					}
				}
			}
		}
		
		$this->doGet($userId);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;		
		$data = array();
		
		$bookingIds = array();
		$sql = "SELECT id FROM booking WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($bookingIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		foreach ($bookingIds as $index => $bookingId) {
			$fileIds = array();
			$sql = "SELECT bf.fileId, layer.position FROM booking_file bf ";
			$sql .= "LEFT JOIN booking ON (booking.id = bf.bookingId) ";			
			$sql .= "LEFT JOIN file ON (file.id = bf.fileId) ";			
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "WHERE booking.userId=%d AND bf.bookingId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $bookingId);

			if ($result = $mysqli->query($sql)) {
				while ($row = $result->fetch_assoc()) {
					array_push($fileIds, intval($row["fileId"]));
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
			}
			
			$obj = new \stdClass();
			$obj->id = $bookingId;
			$obj->fileIds = $fileIds;
			array_push($data, $obj);
		}		
		
		$obj = new \stdClass();
		$obj->booking = $data;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user		
	* @param int $bookingId The id of booking item
	*/	
	public function doDelete($userId, $bookingId) {
		$mysqli = $this->mysqli;
		$bookingIds = array();
		
		$sql = "SELECT id FROM booking WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($bookingIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM booking_file WHERE bookingId IN (%s) AND bookingId=%d;";
		$sql = sprintf($sql, implode(",", $bookingIds), $bookingId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM booking WHERE userId=%d AND id=%d;";
		$sql = sprintf($sql, $userId, $bookingId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$this->doGet($userId);
	}	
}