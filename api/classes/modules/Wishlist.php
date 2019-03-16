<?php 
namespace modules;

use \Exception as Exception;

/**
* Fill up wishlist of favorite avatar based on selected files. 
* The user selected an avatar and from each layer one file with its file id will automatically collected.
* For instance 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be available as preview.
* 
*/
class Wishlist {
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

		$searchId = 0;
		$sql = "SELECT id AS userId FROM user WHERE id=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$searchId = intval($row["userId"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if ($searchId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}
		
		if (count($ids) > 0) {
			$wishlistId = 0;
			$sql = "INSERT INTO wishlist SET userId=%d, modified=NOW()";
			$sql = sprintf($sql, $userId);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			} else {
				$wishlistId = $mysqli->insert_id;
			}			
			
			foreach ($ids as $index => $fileId) {
				if ($fileId > 0 && $wishlistId > 0) {
					$sql = "INSERT INTO wishlist_file SET wishlistId=%d, fileId=%d, modified=NOW()";
					$sql = sprintf($sql, $wishlistId, $fileId);
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
		
		$wishlistIds = array();
		$sql = "SELECT id FROM wishlist WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($wishlistIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		foreach ($wishlistIds as $index => $wishlistId) {
			$fileIds = array();
			$sql = "SELECT wf.fileId, layer.position FROM wishlist_file wf ";
			$sql .= "LEFT JOIN wishlist ON (wishlist.id = wf.wishlistId) ";
			$sql .= "LEFT JOIN file ON (file.id = wf.fileId) ";			
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "WHERE wishlist.userId=%d AND wf.wishlistId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $wishlistId);

			if ($result = $mysqli->query($sql)) {
				while ($row = $result->fetch_assoc()) {
					array_push($fileIds, intval($row["fileId"]));
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
			}
			
			$obj = new \stdClass();
			$obj->id = $wishlistId;
			$obj->fileIds = $fileIds;
			array_push($data, $obj);
		}		
		
		$obj = new \stdClass();
		$obj->wishlist = $data;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user		
	* @param int $wishlistId The id of wishlist item
	*/	
	public function doDelete($userId, $wishlistId) {
		$mysqli = $this->mysqli;
		$wishlistIds = array();
		
		$sql = "SELECT id FROM wishlist WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($wishlistIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM wishlist_file WHERE wishlistId IN (%s) AND wishlistId=%d;";
		$sql = sprintf($sql, implode(",", $wishlistIds), $wishlistId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM wishlist WHERE userId=%d AND id=%d;";
		$sql = sprintf($sql, $userId, $wishlistId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
				
		$this->doGet($userId);
	}
}