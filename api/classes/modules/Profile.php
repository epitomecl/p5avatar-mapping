<?php

namespace modules;

class Profile {
	public function __construct() {
		
	}
	
	public function execute($ssId, $firstName, $lastName, $stageName, $eMail, $imageData) {
		$obj = new \stdClass;
		$obj->success = true;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}
}