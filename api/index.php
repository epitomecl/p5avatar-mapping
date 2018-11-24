<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("classes/Autoloader.php");

use admin\RequestPasswordReset as RequestPasswordReset;
use admin\SubmitPasswordReset as SubmitPasswordReset;
use admin\TestFormular as TestFormular;
use admin\UserManagementAutoLogin as UserManagementAutoLogin;
use admin\UserManagementLogin as UserManagementLogin;
use admin\UserManagementLogout as UserManagementLogout;
use avatar\AvatarGenerator as AvatarGenerator;
use avatar\Currency as Currency;
use avatar\Alliteration as Alliteration;
use admin\FileUpload as FileUpload;

session_start();

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
		(new AvatarGenerator($ssId = session_Id(), $walletAddress))->execute();
		break;
}

switch(strtoupper($module)) {
	case "ROLE":
		$ssId = strip_tags(stripslashes(trim(getParam($_POST ,"ssId"))));
		$obj = new \stdClass;
		$obj->ssId = session_Id();
		$roleId = strip_tags(stripslashes(trim(getParam($_POST ,"roleId"))));
		$obj->role = array("NULL", "ADMIN", "USER", "DESIGNER", "SUPERVISOR");
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
		break;
	case "AVATAR":
		$walletAddress = strip_tags(stripslashes(trim(getParam($_POST, "walletAddress"))));
		$designerId = strip_tags(stripslashes(trim(getParam($_POST ,"designerId"))));	
		$avatarId = strip_tags(stripslashes(trim(getParam($_POST ,"avatarId"))));		
		$ssId = strip_tags(stripslashes(trim(getParam($_POST ,"ssId"))));
		(new AvatarGenerator($ssId, $walletAddress))->execute();
		break;		
	case "LOGIN":
		$identity = strip_tags(stripslashes(trim(getParam($_POST, "identity"))));
		$passwort = strip_tags(stripslashes(trim(getParam($_POST ,"password"))));
		$roleId = strip_tags(stripslashes(trim(getParam($_POST ,"roleId"))));		
		$obj = new \stdClass;
		$obj->ssId = session_Id();
		$obj->roleId = $roleId;
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
		break;
	case "LOGOUT":
		$ssId = strip_tags(stripslashes(trim(getParam($_POST ,"ssId"))));
		$obj = new \stdClass;
		$obj->success = true;
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);	
		break;
	case "CLOSE":
		$ssId = strip_tags(stripslashes(trim(getParam($_POST ,"ssId"))));
		$obj = new \stdClass;
		$obj->success = true;
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);	
		break;	
	case "CREATE":
		$sId = strip_tags(stripslashes(trim(getParam($_POST ,"ssId"))));
		$obj = new \stdClass;
		$obj->ssId = session_Id();
		$obj->stageName = (new Alliteration())->getName();
		$obj->avatarName = (new Alliteration())->getName();
		$obj->layerName = array("background", "body", "fur", "eyes", "mouth", "accessorie");
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);	
		break;
	case "LAYER":
		$designerId = strip_tags(stripslashes(trim(getParam($_POST ,"designerId"))));	
		$avatarId = strip_tags(stripslashes(trim(getParam($_POST ,"avatarId"))));	
		$layerId = strip_tags(stripslashes(trim(getParam($_POST ,"layerId"))));	
		$ssId = strip_tags(stripslashes(trim(getParam($_POST ,"ssId"))));
		$obj = new \stdClass;
		$obj->ssId = session_Id();
		$obj->designerId = $designerId;
		$obj->avatarId = $avatarId;
		$obj->layerId = $layerId;
		$obj->counter = 1;
		$obj->success= true;
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);	
		break;
	case "UPLOAD":
		$path = __DIR__.'/file/upload/';
		$layerId = intval(getParam($_POST, "layerId"));
		$divId = getParam($_POST, "divId");
		$ids = getParam($_POST, "unlink");
		(new FileUpload())->execute($_FILES, $path, $layerId, $divId, $ids);
		break;
	case "CURRENCY":
		echo json_encode((new Currency())->getTopTen(), JSON_UNESCAPED_UNICODE);
		break;
	case "TITLE":
		echo json_encode((new Alliteration())->getTopTen(), JSON_UNESCAPED_UNICODE);
		break;
	default:
		(new TestFormular())->execute($ssId = session_Id(), $module);
		break;		
}
