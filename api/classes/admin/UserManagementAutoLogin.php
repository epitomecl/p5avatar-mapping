<?php 

namespace admin;

class UserManagementAutoLogin {
	private $user;
	
	public function __construct($userToken="", $userId="") {
		$this->init($userToken, $userId);
	}
	
	public function init($userToken, $userId) {
		$this->user = new Userdata();
		$this->user->setUserId($userId);
		$this->user->setUserToken($userToken);
	}
	
	public function execute() {
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/include/db.mysql.ini");
		$mysqli = new mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);

		$mysqli->set_charset("utf8");
		
		if (!$mysqli->connect_errno && $this->user->hasUserToken()) {
			$this->user = UserManagement::validateUserToken($mysqli, $this->user);
			$mysqli->close();
		}
		
		echo json_encode($this->user, JSON_UNESCAPED_UNICODE);
	}
}