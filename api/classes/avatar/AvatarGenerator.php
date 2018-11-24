<?php

namespace avatar;

class AvatarGenerator {
	private $ssID;
	private $walletAddress;
	
	public function __construct($ssID, $walletAddress) {
		$this->ssID = $ssID;
		$this->walletAddress = $walletAddress;
	}
	
	public function execute() {
		$avatar = new Avatar();

		$avatar->setSSID($this->ssID);
		$avatar->setWalletAddress($this->walletAddress);
		
		echo json_encode($avatar, JSON_UNESCAPED_UNICODE);
	}
}
