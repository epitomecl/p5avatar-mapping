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
use modules\Basket as Basket;
use modules\Canvas as Canvas;
use modules\Canvases as Canvases;
use modules\Close as Close;
use modules\Create as Create;
use modules\Currency as Currency;
use modules\HashTag as HashTag;
use modules\Image as Image;
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
use modules\Wishlist as Wishlist;
use modules\Preview as Preview;

// Allow CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');    
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); 
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

if ($httpMethod == 'POST' && array_key_exists('NGINX', $_POST)) {
    if ($_POST['NGINX'] == 'DELETE') {
        $httpMethod = 'DELETE';
    } else if ($_POST['NGINX'] == 'PUT') {
        $httpMethod = 'PUT';
    }
}

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
				$userId = getParam($_GET, "userId");	
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
			} elseif ($httpMethod == "DELETE") {
				$userId = getParam($_POST, "userId", "int");
				$avatarId = getParam($_POST, "avatarId", "int");				
				$avatar->doDelete($userId, $avatarId);				
			} else {
				$userId = getParam($_GET, "userId", "int");
				$avatar->doGet($userId);
			}
			break;
		case "BASKET":
			$basket = new Basket($mysqli);
			if ($httpMethod == "POST") {
				$userId = getParam($_POST, "userId", "int");
				$fileIds = getParam($_POST, "fileIds", "array"); 
				$basket->doPost($userId, $fileIds);
			} elseif ($httpMethod == "DELETE") {
				$userId = getParam($_POST, "userId", "int");
				$basketId = getParam($_POST, "basketId", "int");
				$basket->doDelete($userId, $basketId);
			} else {
				$userId = getParam($_GET, "userId", "int");
				$basket->doGet($userId);				
			}
			break;			
		case "CANVAS":
			$canvas = new Canvas($mysqli);
			if ($httpMethod == "POST") {
				$canvasId = getParam($_POST, "canvasId");
				$name = getParam($_POST, "name");	
				$hashtag = getParam($_POST, "hashtag");			
				$canvas->doPost($canvasId, $name, $hashtag);
			} else {
				$canvasId = getParam($_GET, "canvasId");
				$canvas->doGet($canvasId);
			}
			break;	
		case "CANVASES":
			$canvas = new Canvases($mysqli);
			if ($httpMethod == "POST") {
				$search = getParam($_POST, "search");
				$order = getParam($_POST, "order");	
				$start = getParam($_POST, "start", "int");
				$offset = getParam($_POST, "offset", "int");				
				$canvas->doPost($search, $order, $start, $offset);
			} else {
				$canvas->doGet();
			}
			break;				
		case "CLOSE":
			$userId = getParam($_POST, "userId", "int");
			(new Close($mysqli))->doGet($userId);
			break;	
		case "CREATE":
			$create = new Create($mysqli);
			if ($httpMethod == "POST") {
				$userId = getParam($_POST, "userId", "int");		
				$width = getParam($_POST, "width", "int");	
				$height = getParam($_POST, "height", "int");
				$currency = getParam($_POST, "currency");
				$create->doPost($userId, $width, $height, $currency);
			}
			break;
		case "CURRENCY":
			$currency = new Currency($mysqli);
			if ($httpMethod == "POST") {
				$canvasId = getParam($_POST, "canvasId");
				$currency = getParam($_POST, "currency"); 
				$currency->doPost($canvasId, $currency);
			} else {
				$currency->doGet();
			}
			break;
		case "HASHTAG":
			$hashtag = new HashTag($mysqli);
			if ($httpMethod == "POST") {		
				$canvasId = getParam($_POST, "canvasId");
				$hashtag->doPost($canvasId);
			} else {
				$hashtag->doGet();
			}
			break;
		case "IMAGE":
			$image = new Image($mysqli);
			if ($httpMethod == "POST") {		
				$fileId = getParam($_POST, "fileId");
				$image->doPost($fileId);
			} else {
				$fileId = getParam($_GET, "fileId");
				$image->doGet($fileId);
			}
			break;			
		case "LAYER":
			$layer = new Layer($mysqli);
			if ($httpMethod == "POST") {
				$layerId = getParam($_POST, "layerId", "int");	
				$name = getParam($_POST, "name");
				$position = getParam($_POST, "position", "int");
				$layer->doPost($layerId, $name, $position);
			} elseif ($httpMethod == "PUT") {
				$canvasId = getParam($_POST, "canvasId", "int");
				$name = getParam($_POST, "name");
				$position = getParam($_POST, "position", "int");
				$layer->doPut($canvasId, $name, $position);				
			} else {
				$layerId = getParam($_GET, "layerId", "int");	
				$layer->doGet($layerId);				
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
			$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/api/include/mail.smtp.ini");
			$payment = new Payment($mysqli, $config);
			if ($httpMethod == "POST") {		
				$userId = getParam($_POST, "userId", "int");
				$payment->doPost($userId);
			} elseif ($httpMethod == "PUT") {
				$userId = getParam($_POST, "userId", "int");
				$basketId = getParam($_POST, "basketId", "int"); 
				$address = getParam($_POST, "address");				
				$payment->doPut($userId, $basketId, $address);				
			} elseif ($httpMethod == "DELETE") {
				$userId = getParam($_POST, "userId", "int");
				$basketId = getParam($_POST, "basketId", "array");
				$payment->doDelete($userId, $basketId);		
			} else {
				$userId = getParam($_GET, "userId", "int");
				$payment->doGet($userId);					
			}
			break;			
		case "PREVIEW":
			$preview = new Preview($mysqli);
			if ($httpMethod == "POST") {
				$fileIds = getParam($_POST, "fileIds", "array"); 
				$preview->doPost($fileIds);
			} else {
				$canvasId = getParam($_GET, "canvasId", "int");
				$preview->doGet($canvasId);
			}
			break;			
		case "PRICE":
			$price = new Price($mysqli);
			if ($httpMethod == "POST") {
				$fileId = getParam($_POST, "fileId", "int"); 
				$fee = doubleval(getParam($_POST, "fee"));  
				$price->doPost($fileId, $fee);
			} else {
				$fileId = getParam($_GET, "fileId", "int"); 
				$price->doGet($fileId);
			}
			break;
		case "PROFILE":
			$profile = new Profile($mysqli);
			if ($httpMethod == "POST") {
				$profileId = intval(getParam($_POST, "profileId")); 
				$firstName = getParam($_POST, "firstName");
				$lastName = getParam($_POST, "lastName");
				$alias = getParam($_POST, "alias");
				$email = getParam($_POST, "email");
				$about = getParam($_POST, "about");				
				$file = isset($_FILES["file"]) ? $_FILES["file"] : array();
				$imageData = getParam($_POST, "imageData");
				$profile->doPost($profileId, $firstName, $lastName, $alias, $email, $about, $file, $imageData);
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
			$upload = new Upload($mysqli);
			if ($httpMethod == "POST") {
				$file = isset($_FILES["file"]) ? $_FILES["file"] : array();
				$cardId = getParam($_POST, "cardId");
				$layerId = getParam($_POST, "layerId", "int");
				$upload->doPost($file, $cardId, $layerId);
			} elseif ($httpMethod == "DELETE") {
				$unlink = getParam($_POST, "unlink", "array");
				$upload->doDelete($unlink);
			}
			break;
		case "USERROLE":
			$userRole = new UserRole($mysqli);
			if ($httpMethod == "POST") {
				$userRole->doPost();
			} else {
				$userId = getParam($_GET, "userId", "int");
				$userRole->doGet($userId);
			}
			break;
		case "WHOISONLINE":
			(new WhoIsOnline($mysqli))->doPost();
			break;
		case "WISHLIST":
			$wishlist = new Wishlist($mysqli);
			if ($httpMethod == "POST") {		
				$userId = getParam($_POST, "userId", "int");
				$fileIds = getParam($_POST, "fileIds", "array"); 
				$wishlist->doPost($userId, $fileIds);
			} elseif ($httpMethod == "DELETE") {
				$userId = getParam($_POST, "userId", "int");
				$wishlistId = getParam($_POST, "wishlistId", "int");					
				$wishlist->doDelete($userId, $wishlistId);		
			} else {
				$userId = getParam($_GET, "userId", "int");
				$wishlist->doGet($userId);					
			}
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
