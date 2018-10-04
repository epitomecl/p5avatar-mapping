<?php

namespace admin;

class UserManagementLogout {
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
		
		if (UserManagement::hasAccess($mysqli, $this->user)) {
			$userToken = $this->user->getUserToken();

			if (!empty($userToken)) {
				$this->user->setUserToken("");
			}
			
			$mysqli->close();
		}
		
		echo json_encode($this->user, JSON_UNESCAPED_UNICODE);
	}
}