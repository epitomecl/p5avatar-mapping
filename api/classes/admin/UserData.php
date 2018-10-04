<?php

namespace admin;

use \JsonSerializable as JsonSerializable;

class UserData implements JsonSerializable {
	
    public function jsonSerialize()
    {
        return array(
             'expired' => $this->expired,
			 'modified' => $this->modified,
			 'userId' => $this->userId,
			 'userName' => $this->userName,
             'userToken' => $this->userToken,
			 'errorCode' => $this->errorCode
        );
    }

	private $expired;
    private $modified;
	private $salt;
    private $userId;
    private $userName;
    private $userToken;
	private $userPhone;
    private $errorCode;
	private $userPass;
	private $userEmail;
	private $remaining;
	
	public function __construct() {
		$this->expired = time();
        $this->modified = time();
        $this->salt = "";
        $this->userId = "";
		$this->userPass = "";
        $this->userName = "";
        $this->userToken = "";
		$this->userPhone = "";
		$this->userEmail = "";
		$this->remaining = 0;
        $this->errorCode = 0;
	}
	
	public function setExpired($expired) {
		$this->expired = $expired;
	}
	
	public function setModified($modified) {
		$this->modified = $modified;
	}
	
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	public function setUserPass($userPass) {
		$this->userPass = $userPass;
	}
	
	public function setUserEmail($userEmail) {
		$this->userEmail = $userEmail;
	}
	
	public function setUserPhone($userPhone) {
		$this->userPhone = $userPhone;
	}
	
	public function setRemaining($remaining) {
		$this->remaining = $remaining;
	}
	
	public function setUserName($userName) {
		$this->userName = $userName;
	}
	
	public function setUserToken($userToken) {
		$this->userToken = $userToken;
	}

	public function setSalt($salt) {
		$this->salt = $salt;
	}
	
	public function setErrorCode($errorCode) {
		$this->errorCode = $errorCode;
	}
	
	public function getErrorCode() {
		return $this->errorCode;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function getUserPass(){
		return $this->userPass;
	}

	public function getUserName(){
		return $this->userName;
	}
	
	public function getUserEmail() {
		return $this->userEmail;
	}
	
	public function getRemaining() {
		return $this->remaining;
	}
	
	public function getUserPhone() {
		return $this->userPhone;
	}
	
	public function getSalt(){
		return $this->salt;
	}
	
	public function getModified() {
		return $this->modified;
	}
	
	public function getUserToken() {
		return $this->userToken;
	}
	
	public function hasUserToken() {
		return strlen($this->userToken) > 0;
	}
	
	public function isUserPassValid($userPass) {
		return (strlen($userPass) > 0) && (strcmp($userPass, $this->userPass) == 0);
	}
}
