<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;

/**
* Build avatar based on default avatar. 
* Avatar in use will be marked.
*/
class Avatar {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param string $address The address of a wallet		
	*/		
	public function doPost($address) {
		$obj = new AvatarBuilder();

		$obj->setAddress($address);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
}