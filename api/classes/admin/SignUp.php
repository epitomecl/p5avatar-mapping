<?php

namespace admin;

class SignUp {
	public function __construct() {
		
	}
	
	public function execute($identity, $eMail, $roleId) {
		$obj = new \stdClass;
		$obj->success = true;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}
}