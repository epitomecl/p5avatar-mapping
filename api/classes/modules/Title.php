<?php 

namespace modules;

use common\Alliteration as Alliteration;

class Title {
	
	public function __construct() {
		
	}
	
	public function doPost() {
		echo json_encode((new Alliteration())->getTopTen(), JSON_UNESCAPED_UNICODE);		
	}
}