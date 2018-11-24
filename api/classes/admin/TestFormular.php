<?php

namespace admin;

class TestFormular {
	public function __construct() {
	}
	
	public function execute($ssId, $module) {
		require_once("view/TestFormularView.php"); 
	}
}