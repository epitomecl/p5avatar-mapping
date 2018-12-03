<?php

namespace admin;

class HashTag {
	public function __construct() {
		
	}
	
	public function execute($ssId) {
		$data = array("cute, awesome, unique");
		
		echo json_encode($data, JSON_UNESCAPED_UNICODE);			
	}
}