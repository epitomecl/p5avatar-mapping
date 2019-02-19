<?php 

namespace modules;

use common\Alliteration as Alliteration;

/**
* If user session is alive, 10 random proposals of an titel, label or description for canvas or layer will be generated. 
*/
class Title {
	
	public function __construct() {
		
	}
	
	public function doPost() {
		echo json_encode((new Alliteration())->getTopTen(), JSON_UNESCAPED_UNICODE);		
	}
}