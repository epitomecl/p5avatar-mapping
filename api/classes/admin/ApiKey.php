<?php

namespace admin;

class ApiKey {
	public function __construct() {
		
	}
	
	public function execute($firstName, $lastName, $eMail, $message) {
		$obj = new \stdClass;
		$obj->success = true;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}
}