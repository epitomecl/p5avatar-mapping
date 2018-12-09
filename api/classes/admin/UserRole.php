<?php

namespace admin;

class UserRole {
	public function __construct() {
		
	}
	
	public function execute($userId, $roleId) {
		$items = array("USER", "DESIGNER", "SUPERVISOR", "DEVELOPER", "ADMIN");
		$roles = array();
		
		for($index = 1; $index <= count($items); $index++) {
			$obj = new \stdClass;
			$obj->roleId = $index;
			$obj->roleName = $items[$index - 1];
			$obj->selected = ($roleId == $index);
			
			array_push($roles, $obj);
		}
		
		echo json_encode($roles, JSON_UNESCAPED_UNICODE);		
	}
}