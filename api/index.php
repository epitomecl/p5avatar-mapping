<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("classes/Autoloader.php");

use \Exception as Exception;

use admin\TestFormular as TestFormular;

use modules\Address as Address;
use modules\Alias as Alias;
use modules\ApiKey as ApiKey;
use modules\Avatar as Avatar;
use modules\Booking as Booking;
use modules\Category as Category;
use modules\Close as Close;
use modules\Create as Create;
use modules\Currency as Currency;
use modules\HashTag as HashTag;
use modules\Layer as Layer;
use modules\Login as Login;
use modules\Logout as Logout;
use modules\Password as Password;
use modules\Payment as Payment;
use modules\Price as Price;
use modules\Profile as Profile;
use modules\SignUp as SignUp;
use modules\Title as Title;
use modules\Upload as Upload;
use modules\UserRole as UserRole;
use modules\WhoIsOnline as WhoIsOnline;
use modules\Preview as Preview;

// Allow CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');    
    header("Access-Control-Allow-Methods: GET, POST, PUT, DEL, OPTIONS"); 
}   
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Headers: *");
}

header("Content-Type: text/html; charset=utf-8");

session_start();

function getParam($array, $param, $label = '') {
	if (array_key_exists($param, $array)) {
		
		if (strcmp($label, "array") == 0) {
			return $array[$param];
		} elseif (strcmp($label, "int") == 0) {
			return intval(trim($array[$param]));
		} elseif (strcmp($label, "double") == 0) {
			return doubleval(trim($array[$param]));
		} else {
			return strip_tags(stripslashes(trim($array[$param])));
		}
	}

	return null;
}

$module = getParam($_POST, "module");
$httpMethod = $_SERVER["REQUEST_METHOD"];

if (empty($module)) {
	$module = getParam($_GET, "module");
}

$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/db.mysql.ini");
$mysqli = new mysqli($config['HOST'], $config['USER'], $config['PASS'], $config['NAME']);

try {
	if ($mysqli->connect_error) {
		throw new Exception("Cannot connect to the database: ".$mysqli->connect_errno, 503);
	}
	$mysqli->set_charset("utf8");
	
	(new WhoIsOnline($mysqli))->doUpdate();	
	
	switch(strtoupper($module)) {
		case "ADDRESS":
			$address = new Address($mysqli);
			if ($httpMethod == "POST") {
				$userId = getParam($_POST, "userId");	
				$avatarId = getParam($_POST, "avatarId");	
				$address = getParam($_POST, "address");					
				$address->doPost($userId, $avatarId, $address);
			} else {
				$userId = getParam($_POST, "userId");	
				$address->doGet($userId);
			}
			break;		
		case "ALIAS":
			$userId = getParam($_POST, "userId");	
			$alias = getParam($_POST, "alias");	
			(new Alias($mysqli))->doPost($userId, $alias);
			break;		
		case "APIKEY":
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$apikey = new ApiKey($mysqli, $config);
			if ($httpMethod == "POST") {
				$email = getParam($_POST, "email");		
				$password = getParam($_POST, "password");
				$password2 = getParam($_POST, "password2");
				$dataProtection = getParam($_POST, "dataProtection", "int");
				$termsOfService = getParam($_POST, "termsOfService", "int");
				$apikey->doPost($email, $password, $password2, $dataProtection, $termsOfService);
			} else {
				$token = getParam($_GET, "token");	
				$apikey->doGet($token);
			}
			break;
		case "AVATAR":
			$avatar = new Avatar($mysqli);
			if ($httpMethod == "POST") {
				$address = getParam($_POST, "address");
				$avatar->doPost($address);
			} else {
				$address = getParam($_POST, "address");
				$avatar->doGet($address);
			}
			break;
		case "BOOKING":
			$userId = getParam($_POST, "userId");
			$fileIds = getParam($_POST, "fileIds", "array"); 
			(new Booking($mysqli))->doPost($userId, $fileIds);
			break;			
		case "CATEGORY":
			$categoryId = getParam($_POST, "categoryId");
			$name = getParam($_POST, "name");	
			$hashtag = getParam($_POST, "hashtag");			
			(new Category($mysqli))->doPost($categoryId, $name, $hashtag);
			break;			
		case "CLOSE":
			$userId = getParam($_POST, "userId");
			(new Close($mysqli))->doGet($userId);
			break;	
		case "CREATE":
			$userId = getParam($_POST, "userId");		
			(new Create($mysqli))->doPost($userId);
			break;
		case "CURRENCY":
			(new Currency())->doPost();
			break;
		case "HASHTAG":
			$hashtag = new HashTag($mysqli);
			if ($httpMethod == "POST") {		
				$categoryId = getParam($_POST, "categoryId");
				$hashtag->doPost($categoryId);
			} else {
				$hashtag->doGet();
			}
			break;
		case "LAYER":
			$layer = new Layer($mysqli);
			if ($httpMethod == "POST") {
				$categoryId = getParam($_POST, "categoryId", "int");	
				$name = getParam($_POST, "name");
				$position = getParam($_POST, "position", "int");
				$layer->doPost($categoryId, $name, $position);
			} elseif ($httpMethod == "PUT") {
				$categoryId = getParam($_POST, "categoryId", "int");
				$name = getParam($_POST, "name");
				$position = getParam($_POST, "position", "int");
				$layer->doPut($categoryId, $name, $position);				
			}
			break;
		case "LOGIN":
			$email = getParam($_POST, "email");
			$password = getParam($_POST, "password");
			(new Login($mysqli))->doPost($email, $password);
			break;
		case "LOGOUT":
			$userId = getParam($_POST, "userId", "int");
			(new Logout($mysqli))->doPost($userId);
			break;
		case "PASSWORD":
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$password = new Password($mysqli, $config);
			if ($httpMethod == "POST") {
				$email = getParam($_POST, "email");		
				$phone = getParam($_POST, "phone");
			} elseif ($httpMethod == "PUT") {
				$token = getParam($_POST, "token");		
				$password = getParam($_POST, "password");		
				$password2 = getParam($_POST, "password2");
				$password->doPut($token, $password, $password2);
			} else {
				$token = getParam($_GET, "token");	
				$password->doGet($token);
			}
			break;
		case "PAYMENT":
			$payment = new Payment($mysqli, $config);
			if ($httpMethod == "POST") {		
				$userId = getParam($_POST, "userId", "int");
				$fileIds = getParam($_POST, "fileIds", "array"); 
				$payment->doPost($userId, $fileIds);
			} elseif ($httpMethod == "PUT") {
				$userId = getParam($_POST, "userId", "int");
				$fileIds = getParam($_POST, "fileIds", "array"); 
				$payment->doPut($userId, $fileIds);				
			}  elseif ($httpMethod == "DEL") {
				$userId = getParam($_POST, "userId", "int");
				$fileIds = getParam($_POST, "fileIds", "array");
				$payment->doDel($userId, $fileIds);		
			} else {
				$userId = getParam($_POST, "userId", "int");
				$fileIds = getParam($_POST, "fileIds", "array");
				$payment->doGet($userId, $fileIds);					
			}
			break;			
		case "PREVIEW":
			$fileIds = getParam($_POST, "fileIds", "array"); 
			(new Preview($mysqli))->doPost($fileIds);
			break;			
		case "PRICE":
			$priceId = intval(getParam($_POST, "priceId", "int")); 
			$price = doubleval(getParam($_POST, "price"));  
			$currency = getParam($_POST, "currency"); 
			(new Price($mysqli))->doPost($priceId, $price, $currency);
			break;
		case "PROFILE":
			$profile = new Profile($mysqli);
			if ($httpMethod == "POST") {
				$userId = intval(getParam($_POST, "userId")); 
				$firstName = getParam($_POST, "firstName");
				$lastName = getParam($_POST, "lastName");
				$alias = getParam($_POST, "alias");
				$email = getParam($_POST, "email");
				$file = $_FILES["file"];
				$imageData = getParam($_POST, "imageData");
				$profile->doPost($userId, $firstName, $lastName, $alias, $email, $file, $imageData);
			} else {
				$userId = intval(getParam($_GET, "userId")); 
				$profile->doGet($userId);				
			}
			break;
		case "SIGNUP":
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$signUp = new SignUp($mysqli, $config);
			if ($httpMethod == "POST") {
				$email = getParam($_POST, "email");		
				$password = getParam($_POST, "password");
				$password2 = getParam($_POST, "password2");
				$dataProtection = getParam($_POST, "dataProtection", "int");
				$termsOfService = getParam($_POST, "termsOfService", "int");
				$signUp->doPost($email, $password, $password2, $dataProtection, $termsOfService);
			} else {
				$token = getParam($_GET, "token");	
				$signUp->doGet($token);
			}
			break;
		case "TITLE":
			(new Title())->doPost();
			break;
		case "UPLOAD":
			$file = $_FILES["file"];
			$layerId = intval(getParam($_POST, "layerId"));
			$divId = getParam($_POST, "divId");
			$unlink = getParam($_POST, "unlink", "array");
			(new FileUpload($mysqli, $path))->doPost($file, $layerId, $divId, $unlink);
			break;
		case "USERROLE":
			$userRole = new UserRole($mysqli);
			if ($httpMethod == "POST") {
				$userRole->doPost();
			} else {
				$userId = getParam($_POST, "userId");
				$userRole->doGet($userId);
			}
			break;
		case "WHOISONLINE":
			(new WhoIsOnline($mysqli))->doPost();
			break;
		default:
			(new TestFormular())->execute($module);
			break;		
	}
} catch (Exception $e) {
	$msg = $e->getMessage();
	$code = $e->getCode();
	http_response_code(($code == 0) ? 400 : $code);
	echo sprintf("Exception occurred in: %s", $msg);
} finally {
	$mysqli->close();
}
