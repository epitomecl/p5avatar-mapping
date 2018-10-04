<?php

namespace avatar;

class AvatarGenerator {
	private $userToken;
	private $userName;
	private $walletAddress;
	
	public function __construct($userToken, $userName, $walletAddress) {
		$this->userToken = $userToken;
		$this->userName = $userName;
		$this->walletAddress = $walletAddress;
	}
	
	public function execute() {
		$avatar = new Avatar();

		$avatar->setWalletAddress($this->walletAddress);
		
		echo json_encode($avatar, JSON_UNESCAPED_UNICODE);
	}
}
