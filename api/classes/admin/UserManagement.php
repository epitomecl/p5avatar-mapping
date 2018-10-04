<?php

namespace admin;

class UserManagement {
	public static function getUserToken($user) {
		$userEmail = base64_encode($user->getUserEmail());
		$userName = base64_encode($user->getUserName());
		$modified = base64_encode($user->getModified());
		$salt = str_pad($user->getSalt(), 32);
		
		return hash_hmac("sha256", $userEmail.$userName.$modified, $salt);
	}
	
	public static function getResetToken($array, $salt) {
		$salt = str_pad($salt, 32);
		$data = "";
		
		foreach($array as $value) {
			$data .= base64_encode($value);
		}
		
		return hash_hmac("sha256", $data, $salt);
	}
	
	public static function generateRandomString($nbLetters){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$max = strlen($chars) - 1;
		$randString = "";

		for($i=0; $i < $nbLetters; $i++){
			$randChar = $chars[mt_rand(0, $max)];
			$randString .= $randChar;
		}

		return $randString;
	}
	
	public static function validateUserToken($mysqli, $user) {
		$validHash = false;
		$validRemaining = false;
		
		$sql = "SELECT user_name, salt, modified, expired, DATEDIFF(expired, NOW()) as remaining ";
		$sql .= "FROM users ";
		$sql .= "WHERE user_id ='%s' ";
		$sql = sprintf($sql, $user->getUserId());
		$result = $mysqli->query($sql);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$user->setUserName($row["user_name"]);
				$user->setSalt($row["salt"]);
				$user->setModified(strtotime($row["modified"]));
				$user->setExpired(strtotime($row["expired"]));
				$user->setErrorCode(0);

				$validRemaining = intval($row["remaining"]) > 0;
			}
			$result->free();
		}

		$validHash = hash_equals($user->getUserToken(), UserManagement::getUserToken($user));
		
		if (!$validHash || !$validRemaining) {
			$user->setUserToken("");
			$user->setErrorCode(5);
		}
	
		return $user;
	}
	
	public static function hasAccess($mysqli, $user) {
		if (!$mysqli->connect_errno && $user->hasUserToken()) {			
			return UserManagement::validateUserToken($mysqli, $user)->hasUserToken();
		}

		return false;
	}	
}
