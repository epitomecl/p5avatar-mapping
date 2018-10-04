<?php
require_once("classes/Autoloader.php");

use admin\RequestPasswordReset as RequestPasswordReset;
use admin\SubmitPasswordReset as SubmitPasswordReset;
use admin\TestFormular as TestFormular;
use admin\UserManagementAutoLogin as UserManagementAutoLogin;
use admin\UserManagementLogin as UserManagementLogin;
use admin\UserManagementLogout as UserManagementLogout;
use avatar\AvatarGenerator as AvatarGenerator;


function getParam($array, $param) {
	if (array_key_exists($param, $array)) {
		return $array[$param];
	}
	
	return null;
}

$module = strip_tags(stripslashes(trim(getParam($_POST, "module"))));
$userId = strip_tags(stripslashes(trim(getParam($_POST, "userId"))));
$userToken = strip_tags(stripslashes(trim(getParam($_POST, "userToken"))));
$userPass = strip_tags(stripslashes(trim(getParam($_POST, "userPass"))));
$userName = strip_tags(stripslashes(trim(getParam($_POST ,"userName"))));
$userEmail = strip_tags(stripslashes(trim(getParam($_POST, "userEmail"))));
$userPhone = strip_tags(stripslashes(trim(getParam($_POST, "userPhone"))));
$walletAddress = strip_tags(stripslashes(trim(getParam($_POST, "walletAddress"))));

header("Content-Type: text/html; charset=utf-8");

switch($module) {
	case "UserManagementLogin":
		(new UserManagementLogin($userEmail, $userPass, $userName, $userPhone))->execute();
		break;
	case "UserManagementAutoLogin":
		(new UserManagementAutoLogin($userToken, $userId))->execute();
		break;
	case "UserManagementLogout":
		(new UserManagementLogout($userToken, $userId))->execute();
		break;
	case "RequestPasswordReset":
		(new RequestPasswordReset($userName, $userEmail, $userPhone))->execute();
		break;
	case "SubmitPasswordReset":
		(new SubmitPasswordReset($userToken, $userName, $userPass))->execute();
		break;
	case "AvatarGenerator":
		(new AvatarGenerator($userToken, $userName, $walletAddress))->execute();
		break;
	default:
		(new TestFormular())->execute();
}

