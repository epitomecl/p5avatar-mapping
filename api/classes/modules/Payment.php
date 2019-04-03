<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../common/PHPMailer/src/Exception.php';
require_once __DIR__.'/../common/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../common/PHPMailer/src/SMTP.php';

/**
* User goes through the payment process.
* For instance he shopping the file ids 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be build after paying.
* Over the basket process selected files by this user will be marked now as owned by user. 
* These files are not selectable for an furter preview or basket process.
* GET gives an overview about pending processes.
* POST starts payment process.
* PUT confirm payment process.
* DELETE deleted the current basket data in case of abort by user.
* 
*/
class Payment {
	private $mysqli;
	private $smtpConfig;
	
	/**
	* something describes this method
	*/	
	public function __construct($mysqli, $smtpConfig) {
		$this->mysqli = $mysqli;
		$this->smtpConfig = $smtpConfig;
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	*/		
	public function doPost($userId) {
		$mysqli = $this->mysqli;		

		$basketIds = array();
		$sql = "SELECT id FROM basket WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($basketIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$data = array();
		foreach ($basketIds as $index => $basketId) {		
			$sql = "SELECT basket_file.id, basket_file.fileId, fee, currency FROM basket_file ";
			$sql .= "LEFT JOIN basket ON (basket.id = basket_file.basketId) ";		
			$sql .= "LEFT JOIN file ON (file.id = basket_file.fileId) ";
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
			$sql .= "WHERE basket.userId=%d AND basket_file.basketId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $basketId);
			if ($result = $mysqli->query($sql)) {
				if ($row = $result->fetch_assoc()) {
					array_push($data, $row);
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		}
		// todo something with data
		
		$this->doGet($userId);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	* @param int $basketId The id of basket
	* @param string $address The address of a wallet		
	*/		
	public function doPut($userId, $basketId, $address) {
		$mysqli = $this->mysqli;		
		
		$ids = array();
		$sql = "SELECT basket_file.id, basket_file.fileId, fee, currency FROM basket_file ";
		$sql .= "LEFT JOIN basket ON (basket.id = basket_file.basketId) ";		
		$sql .= "LEFT JOIN file ON (file.id = basket_file.fileId) ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE basket.userId=%d AND basket_file.basketId=%d ";	
		$sql .= "ORDER BY layer.position;";
		$sql = sprintf($sql, $userId, $basketId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($ids, intval($row["fileId"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}		
		
		if (count($ids) == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Acceptable'.$basketId), 406);
		}
		
		$data = $this->getCanvasData($mysqli, $basketId) ;
		$width = $data->width;
		$height = $data->height;
		
		$builder = new AvatarBuilder();
		$image = $builder->getAvatarImageSource($mysqli, $ids, $width, $height);
		$path = realpath(dirname(__FILE__).'/../../images/avatars')."/";
		$fileName = sprintf("%s.%s", md5($userId.time()), "jpg");

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		
		imagejpeg($image, $path.$fileName, 75);
		imagedestroy($image);

		if (file_exists($path.$fileName)) {
			$avatarId = 0;
			$sql = "INSERT INTO avatar SET userId=%d, address='%s', filename='%s', modified=NOW()";
			$sql = sprintf($sql, $userId, $address, $fileName);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
			} else {
				$avatarId = $mysqli->insert_id;
			}			
			
			foreach ($ids as $index => $fileId) {
				if ($fileId > 0 && $avatarId > 0) {
					$sql = "INSERT INTO avatar_file SET avatarId=%d, fileId=%d, modified=NOW();";
					$sql = sprintf($sql, $avatarId, $fileId);
					if ($mysqli->query($sql) === false) {
						throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
					}
				}
			}
			$this->removeBasket($mysqli, $userId, $basketId);
			
			if ($this->nothing2buy($mysqli, $userId)) {
				$email = $this->getEmail($mysqli, $userId);
				if (!empty($email) && strlen($email) > 0) {
					$this->sendEmail($email, $userId);
				}
			}
		}
		
		$this->doGet($userId);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	* @param int $basketId The id of basket
	*/		
	public function doDelete($userId, $basketId) {
		$mysqli = $this->mysqli;	
		
		$this->removeBasket($mysqli, $userId, $basketId);
		
		$this->doGet($userId);		
	}

	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;	
		
		$basketIds = array();
		$sql = "SELECT id FROM basket WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($basketIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$items = array();
		foreach ($basketIds as $index => $basketId) {		
			$sql = "SELECT basket_file.id, basket_file.fileId, fee, currency, ";
			$sql .= "canvas.name as memo, profile.address ";
			$sql .= "FROM basket_file ";
			$sql .= "LEFT JOIN basket ON (basket.id = basket_file.basketId) ";		
			$sql .= "LEFT JOIN file ON (file.id = basket_file.fileId) ";
			$sql .= "LEFT JOIN profile ON (file.userId = profile.userId) ";		
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
			$sql .= "WHERE basket.userId=%d AND basket_file.basketId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $basketId);
			if ($result = $mysqli->query($sql)) {
				$item = NULL;
				if ($row = $result->fetch_assoc()) {
					$currency = trim($row["currency"]);
					$fee = $row["fee"];
					$amount = sprintf("%s %s", rtrim($fee, "0"), $currency);
					$memo = trim($row["memo"]);
					$address = trim($row["address"]);
					$item = new \stdClass();
					$item->basketId = $basketId;
					$item->currency = $currency;
					$item->fee = $fee;
					$item->address = $address;
					$item->amount = $amount;
					$item->memo = $memo;
					array_push($items, $item);
				}
				while ($row = $result->fetch_assoc()) {
					if (isset($item)) {
						$item->fee += $row["fee"];
						$fee = $item->fee;
						$currency = $item->currency;
						$amount = sprintf("%s %s", rtrim($fee, "0"), $currency);
						$item->amount = $amount;
					}
				}		
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		}
		
		// array of several currencies and their total fees
		$price = array();
		foreach ($items as $item) {
			$currency = $item->currency;
			$fee = $item->fee;
			if (array_key_exists($currency, $price)) {
				$price[$currency] = $price[$currency] + $fee;
			} else {
				$price[$currency] = $fee;						
			}
		}
		
		$obj = new \stdClass();
		$obj->counter = count($basketIds);
		$obj->items = $items;
		$obj->price = $this->getObjectPrice($price);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);		
	}
	
	private function sendEmail($email, $userId) {
		$config = $this->smtpConfig;
		$to = $email;
		$from = "marian@epitomecl.com";
		$module = strtolower("AVATAR");
		$subject = "YOUR AVATAR IS HERE";
		$url = $this->siteURL();
		$link = $url.sprintf("api.avarkey.com/avarkey/%s.php?userId=%d", $module, $userId);
		$body = "<!DOCTYPE html>";
		$body .= "<html>";
		$body .= "<head><meta charset='charset=utf-8'>";
		$body .= "<title>$subject</title></head>";
		$body .= "<body>";
		$body .= "<h4>Thank you for using AVARKEY!</h4>";
		$body .= "<p>If you want to change bonded address, then go into your account and adjust the address again.</p>";
		$body .= "<p>Follow this link above and adjust personal avatar address.</p>";
		$body .= "<p>External link: <a href='".$link."' target='_blank'>".$link."</a></p>";
		$body .= "<p>Browser link: ".$link."</p>";
		$body .= "<p>Best regards.</p>";
		$body .= "<p>Avarkey</p>";
		$body .= "</body>";
		$body .= "</html>";
	
		$mail = new PHPMailer(true);            // Passing `true` enables exceptions
		try {
			//Server settings
			$mail->SMTPDebug = 0;               // 2 : Enable verbose debug output
			$mail->isSMTP();                    // Set mailer to use SMTP
			$mail->Host = $config['HOST'];  	// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;             // Enable SMTP authentication
			$mail->Username = $config['USER'];  // SMTP username
			$mail->Password = $config['PASS'];  // SMTP password
			$mail->SMTPSecure = 'tls';          // Enable TLS encryption, `ssl` also accepted
			$mail->Port = $config['PORT'];      // TCP port to connect to

			$mail->CharSet = 'UTF-8';
			$mail->Encoding = 'base64';
			
			//Recipients
			$mail->setFrom($from, 'Avarkey');
			$mail->addAddress($to);

			//Content
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $this->cleanup(array("</title>", "</h4>", "</p>"), $body);

			$mail->send();
		} catch (Exception $e) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo), 406);
		}
	}

	private function nothing2buy($mysqli, $userId) {
		$basketIds = array();
		
		$sql = "SELECT id FROM basket WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($basketIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		return count($basketIds) == 0;
	}
	
	private function getEmail($mysqli, $userId) {
		$email = "";
		
		$sql = "SELECT email FROM profile WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				$email = trim($row["email"]);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		return $email;
	}
	
	private function removeBasket($mysqli, $userId, $basketId) {
		$basketIds = array();
		
		$sql = "SELECT id FROM basket WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($basketIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM basket_file WHERE basketId IN (%s) AND basketId=%d;";
		$sql = sprintf($sql, implode(",", $basketIds), $basketId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM basket WHERE userId=%d AND id=%d;";
		$sql = sprintf($sql, $userId, $basketId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
	}
	
	private function getCanvasData($mysqli, $basketId) {
		$data = array();
		
		$sql = "SELECT canvas.name, width, height, currency FROM basket ";
		$sql .= "LEFT JOIN basket_file ON (basket_file.basketId = basket.id) ";
		$sql .= "LEFT JOIN file ON (file.id = basket_file.fileId) ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "lEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE basket.id=%d;";
		$sql = sprintf($sql, $basketId);

		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		if (empty($data)) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);			
		}	
		
		$obj = new \stdClass();
		$obj->name = trim($data[0]["name"]);
		$obj->width = intval($data[0]["width"]);
		$obj->height = intval($data[0]["height"]);
		$obj->currency = trim($data[0]["currency"]);
		
		return $obj;
	}
	
	private function getObjectPrice($price) {
		$data = array();
		
		foreach ($price as $key => $value) {
			$obj = new \stdClass();
			$obj->currency = $key;
			$obj->fee = rtrim($value, "0");
			
			array_push($data, $obj);
		}
		
		return $data;
	}
	
	private function cleanup($labels, $body) {
		foreach ($labels as $index => $search) {
			$body = str_replace($search, PHP_EOL, $body);
		}
		
		return strip_tags($body);
	}
	
	private function siteURL() {
		$protocol = (!empty($_SERVER['HTTPS']) && 
					$_SERVER['HTTPS'] !== 'off' || 
					$_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'].'/';
		
		return $protocol.$domainName;
	}
}