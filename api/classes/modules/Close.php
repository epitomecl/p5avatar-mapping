<?php 

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, user session will be closed.
* All user profile data will be deleted. 
* Used avatars created by profile owner are keeped on system.
*/
class Close  implements JsonSerializable {
	private $mysqli;
	
	public function jsonSerialize() {
		return array(
			'userId' => userId
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param string $userId The id of current user
	*/	
	public function doPost($userId) {
		$path = dirname(__FILE__).'/../../images/profile/';		
		$fileName = "";
		$sql = "SELECT photo FROM profile WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));

		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$fileName = intval($row["photo"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		if (strlen($fileName) > 0 && file_exists($path.$fileName)) {
			unlink($path.$fileName);
		}
		
		$folders = getCategoryNames($mysqli, $userId);
		foreach ($folders as $index => $folder) {
			if (file_exists($path.$folder)) {
				array_map('unlink', glob($path.$folder."*.*"));
				rmdir($path.$folder);				
			}
		}
		
		$sql = "DELETE FROM user_roles WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$sql = "DELETE FROM user_category WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$sql = "DELETE FROM user_login WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$sql = "DELETE FROM profile WHERE userId = %d";
		$sql = sprintf($sql, intval($userId));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}		
		
		$sql = "DELETE FROM user WHERE id = %d";
		$sql = sprintf($sql, intval($userId));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$this->deleteSession();
			
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
	
	private function getCategoryNames($mysqli, $userId) {
		$folders = array();
		$sql = "SELECT CONCAT(category.name,'_',category.id,'/') AS categoryName FROM category ";
		$sql .= "LEFT JOIN user_category ON (user_category.categoryId = category.id) ";
		$sql .= "WHERE user_category.userId=%d";		
		$sql = sprintf($sql, $userId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($folders, trim($row["categoryName"]));
			}
			$result->free();
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}

		return $folders;
	}	
}