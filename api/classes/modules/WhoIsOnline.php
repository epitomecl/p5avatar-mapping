<?php 

namespace modules;

use \Exception as Exception;
use \mysqli as mysqli;
use \JsonSerializable as JsonSerializable;

/**
* Storing active session id in database and updating table data. 
* Inform about counter of current online users and used last possible IP address.
*/
class WhoIsOnline implements JsonSerializable {
	private $mysqli;	
	private $ssid;
	private $ip;
	private $minutes;
	private $counter;
	
	public function jsonSerialize() {
		return array(
			'counter' => $this->counter,
			'ip' => $this->ip
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;		
		$this->ssid = session_Id();
		$this->ip = $this->getUserIpAddress();
		$this->minutes = 5;	// period considered active
		$this->counter = 0;
	}
	
	public function doPost() {
		$this->update($this->mysqli);
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}
	
	public function doUpdate() {
		$this->update($this->mysqli);
	}
	
	protected function update($mysqli) {
		if(!empty($this->ssid)) {
			$sql = "DELETE FROM `user_online` WHERE `last_access` < DATE_SUB(NOW(),INTERVAL %d MINUTE)";
			$sql = sprintf($sql, $this->minutes);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
			
			$sql = "INSERT INTO `user_online` (`session_id`, `ip`, `last_access`) VALUES ('%s', '%s', NOW()) ON DUPLICATE KEY UPDATE `last_access` = NOW()";
			$sql = sprintf($sql, $this->ssid, inet_pton($this->ip));
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
	
			$sql = "SELECT COUNT(*) as counter FROM `user_online`";
			if ($result = $mysqli->query($sql)) {
				while ($row = $result->fetch_assoc()) {
					$this->counter = intval($row["counter"]);
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}			
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
	}
	
	protected function getUserIpAddress() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}