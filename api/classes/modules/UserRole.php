<?php

namespace modules;

use \Exception as Exception;

/**
* For user profile we need a role selection. Possible roles are given by backend service. 
* If user session is alive, current roleId is responsed. 
* The roleId is specified as index of given array. 
* Possible roles are (1 : "USER", 2 : "DESIGNER", 3 : "SUPERVISOR", 4: "DEVELOPER", 5 : "ADMIN").
* A user can act in different roles.
*/
class UserRole {
	private $mysqli;	
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;	
	}
	
	public function doPost() {
		$mysqli = $this->mysqli;
		
		$data = array();
		$sql = "SELECT * FROM role";
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$obj = new \stdClass();
				$obj->id = intval($row["id"]);
				$obj->name = trim($row["name"]);
				array_push($data, $obj);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);		
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of user
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;
		
		$data = array();
		$sql = "SELECT role.id, role.name FROM user_role ";
		$sql .= "LEFT JOIN role ON (user_role.roleId = role.id) ";
		$sql .= "WHERE user_role.userId=%d";
		$sql = sprintf($sql, intval($userId));

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$obj = new \stdClass();
				$obj->id = intval($row["id"]);
				$obj->name = trim($row["name"]);
				array_push($data, $obj);
			}
			
			if (count($data) == 0) {
				throw new Exception(sprintf("%s, %s", get_class($this), "User not exist."), 404);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);		
	}
}