<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("classes/Autoloader.php");

use \Exception as Exception;

use admin\TestFormular as TestFormular;

use modules\ApiKey as ApiKey;
use modules\Avatar as Avatar;
use modules\Category as Category;
use modules\Close as Close;
use modules\Create as Create;
use modules\Currency as Currency;
use modules\Designer as Designer;
use modules\HashTag as HashTag;
use modules\Layer as Layer;
use modules\Login as Login;
use modules\Logout as Logout;
use modules\Password as Password;
use modules\Price as Price;
use modules\Profile as Profile;
use modules\SignUp as SignUp;
use modules\Title as Title;
use modules\Upload as Upload;
use modules\UserRole as UserRole;
use modules\WhoIsOnline as WhoIsOnline;

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
		case "APIKEY":
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$apikey = new ApiKey($mysqli, $config);
			if ($httpMethod == "POST") {
				$email = getParam($_POST, "email");		
				$password = getParam($_POST, "password");
				$password2 = getParam($_POST, "password2");
				$dataProtection = getParam($_POST, "dataProtection");
				$termsOfService = getParam($_POST, "termsOfService");
				$apikey->doPost($email, $password, $password2, $dataProtection, $termsOfService);
			} else {
				$token = getParam($_GET, "token");	
				$apikey->doGet($token);
			}
			break;
		case "AVATAR":
			$address = getParam($_POST, "address");
			$designer = getParam($_POST, "designer");	
			$avatar = getParam($_POST, "avatar");		
			(new Avatar($mysqli))->doPost($address);
			break;
		case "CATEGORY":
			$id = getParam($_POST, "id");
			$name = getParam($_POST, "name");	
			$hashtag = getParam($_POST, "hashtag");			
			(new Category($mysqli))->doPost($id, $name, $hashtag);
			break;			
		case "CLOSE":
			$userId = getParam($_POST, "userId");
			(new Close($mysqli))->doGet($userId);
			break;	
		case "CREATE":
			(new Create($mysqli))->doPost();
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
		case "HASHTAG":
			$hashtag = new HashTag($mysqli);
			if ($httpMethod == "POST") {		
				$id = getParam($_POST, "id");
				$hashtag->doPost($ssId);
			} else {
				$hashtag->doGet();
			}
			break;
		case "LAYER":
			$id = getParam($_POST, "id");	
			$name = getParam($_POST, "name");	
			(new Layer())->doPost($id, $name);
			break;
		case "LOGIN":
			$login = getParam($_POST, "login");
			$password = getParam($_POST, "password");
			(new Login())->doPost($login, $password);
			break;
		case "LOGOUT":
			$userId = getParam($_POST, "userId");
			(new Logout())->doPost($userId);
			break;
		case "PASSWORD":
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$password = new Password($mysqli, $config);
			if ($httpMethod == "POST") {
				$email = getParam($_POST, "email");		
				$phone = getParam($_POST, "phone");
			} else {
				$token = getParam($_GET, "token");	
				$password->doGet($token);
			}
			break;	
		case "PROFILE":
			$ssId = getParam($_POST, "ssId");
			$firstName = getParam($_POST, "firstName");
			$lastName = getParam($_POST, "lastName");
			$stageName = getParam($_POST, "stageName");
			$email = getParam($_POST, "email");
			$imageData = getParam($_POST, "imageData");
			(new Profile())->execute($ssId, $firstName, $lastName, $stageName, $email, $imageData);
			break;
		case "UPLOAD":
			$path = __DIR__.'/file/upload/';
			$layerId = intval(getParam($_POST, "layerId"));
			$divId = getParam($_POST, "divId");
			$unlink = getParam($_POST, "unlink", "array");
			(new FileUpload($mysqli, $path))->doPost($_FILES["file"], $layerId, $divId, $unlink);
			break;
		case "CURRENCY":
			(new Currency())->doPost();
			break;
		case "DESIGNER":
			$id = getParam($_POST, "id");	
			$name = getParam($_POST, "name");	
			(new Designer())->doPost($id, $name);
			break;
		case "PRICE":
			$layer = intval(getParam($_POST, "layer")); 
			$price = doubleval(getParam($_POST, "price"));  
			$currency = getParam($_POST, "currency"); 
			(new Price($mysqli))->doPost($layer, $price, $currency);
			break;
		case "SIGNUP":
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$signUp = new SignUp($mysqli, $config);
			if ($httpMethod == "POST") {
				$email = getParam($_POST, "email");		
				$password = getParam($_POST, "password");
				$password2 = getParam($_POST, "password2");
				$dataProtection = getParam($_POST, "dataProtection");
				$termsOfService = getParam($_POST, "termsOfService");
				$signUp->doPost($email, $password, $password2, $dataProtection, $termsOfService);
			} else {
				$token = getParam($_GET, "token");	
				$signUp->doGet($token);
			}
			break;
		case "TITLE":
			(new Title())->doPost();
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
