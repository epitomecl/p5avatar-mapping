<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;
use \JsonSerializable as JsonSerializable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../common/PHPMailer/src/Exception.php';
require_once __DIR__.'/../common/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../common/PHPMailer/src/SMTP.php';

/**
* User goes through the payment process.
* For instance he shopping the file ids 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be build after paying.
* Over the booking process selected files by this user will be marked now as owned by user. 
* These files are not selectable for an furter preview or booking process.
* GET gives an overview about pending processes.
* POST starts payment process.
* PUT confirm payment process.
* DELETE deleted the current booking data in case of abort by user.
* 
*/
class Payment {
	private $mysqli;
	private $smtpConfig;
	
	public function jsonSerialize() {
		return array(
			'success' => true
        );
    }
	
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

		$bookingIds = array();
		$sql = "SELECT id FROM booking WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($bookingIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$data = array();
		foreach ($bookingIds as $index => $bookingId) {		
			$sql = "SELECT booking_file.id, booking_file.fileId, fee, currency FROM booking_file ";
			$sql .= "LEFT JOIN booking ON (booking.id = booking_file.bookingId) ";		
			$sql .= "LEFT JOIN file ON (file.id = booking_file.fileId) ";
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
			$sql .= "WHERE booking.userId=%d AND booking_file.bookingId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $bookingId);
			if ($result = $mysqli->query($sql)) {
				if ($row = $result->fetch_assoc()) {
					array_push($data, $row);
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		}
		// todo something with data
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	* @param int $bookingId The id of booking
	* @param string $address The address of a wallet		
	*/		
	public function doPut($userId, $bookingId, $address) {
		$mysqli = $this->mysqli;		
		
		$ids = array();
		$sql = "SELECT booking_file.id, booking_file.fileId, fee, currency FROM booking_file ";
		$sql .= "LEFT JOIN booking ON (booking.id = booking_file.bookingId) ";		
		$sql .= "LEFT JOIN file ON (file.id = booking_file.fileId) ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE booking.userId=%d AND booking_file.bookingId=%d ";	
		$sql .= "ORDER BY layer.position;";
		$sql = sprintf($sql, $userId, $bookingId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				array_push($ids, intval($row["fileId"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}		
		$this->removeBooking($mysqli, $userId, $bookingId);

		$builder = new AvatarBuilder();
		$image = $builder->getAvatarImageSource($mysqli, $ids);
		$path = realpath(dirname(__FILE__).'/../../images/avatars')."/";
		$fileName = sprintf("%s.%s", md5($userId.time()), "png");

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		
		imagepng($image, $path.$fileName);
		imagedestroy($image);

		if (file_exists($path.$fileName)) {
			$avatarId = 0;
			$sql = "INSERT INTO avatar SET userId=%d, address='%s', filename='%s', modified=NOW()";
			$sql = sprintf($sql, $userId, $address, $fileName);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			} else {
				$avatarId = $mysqli->insert_id;
			}			
			
			foreach ($ids as $index => $fileId) {
				if ($fileId > 0 && $avatarId > 0) {
					$sql = "INSERT INTO avatar_file SET avatarId=%d, fileId=%d, modified=NOW();";
					$sql = sprintf($sql, $avatarId, $fileId);
					if ($mysqli->query($sql) === false) {
						throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
					}
				}
			}
			
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	* @param int $bookingId The id of booking
	*/		
	public function doDelete($userId, $bookingId) {
		$mysqli = $this->mysqli;	
		
		$this->removeBooking($mysqli, $userId, $bookingId);
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}

	/**
	* something describes this method
	*
	* @param int $userId The id of current user	
	*/		
	public function doGet($userId) {
		$mysqli = $this->mysqli;	
		
		$bookingIds = array();
		$sql = "SELECT id FROM booking WHERE userId=%d;";		
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($bookingIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$price = array();
		foreach ($bookingIds as $index => $bookingId) {		
			$sql = "SELECT booking_file.id, booking_file.fileId, fee, currency FROM booking_file ";
			$sql .= "LEFT JOIN booking ON (booking.id = booking_file.bookingId) ";		
			$sql .= "LEFT JOIN file ON (file.id = booking_file.fileId) ";
			$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
			$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
			$sql .= "WHERE booking.userId=%d AND booking_file.bookingId=%d ";	
			$sql .= "ORDER BY layer.position;";
			$sql = sprintf($sql, $userId, $bookingId);
			if ($result = $mysqli->query($sql)) {
				if ($row = $result->fetch_assoc()) {
					$currency = trim($row["currency"]);
					$fee = $row["fee"];
					
					if (array_key_exists($currency, $price)) {
						$price[$currency] = $price[$currency] + $fee;
					} else {
						$price[$currency] = $fee;						
					}
				}
			} else {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}
		}
		
		
		$obj = new \stdClass();
		$obj->counter = count($bookingIds);
		$obj->price = $this->getObjectPrice($price);
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);		
	}
	
	private function sendEmail($email, $address) {
		$config = $this->smtpConfig;
		$address = (strlen($address) > 0) ? $address : "&#128165;&#128165;&#128165;&#128165;";
		$to = $email;
		$from = "marian@epitomecl.com";
		$module = strtolower("AVATAR");
		$subject = "YOUR AVATAR IS BONDED";
		$url = $this->siteURL()."api/?module=$module&address=$address";
		$link = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
		$body = "<!DOCTYPE html>";
		$body .= "<html>";
		$body .= "<head><meta charset='charset=utf-8'>";
		$body .= "<title>$subject</title></head>";
		$body .= "<body>";
		$body .= "<h4>Thank you for using AVARKEY!</h4>";
		$body .= "<p>If you want to change bonded address, then go into your account and adjust the address again.</p>";
		$body .= "<p>Follow this link above and fetch every time your personal avatar.</p>";
		$body .= "<p>Bonded address: $address</p>";
		$body .= "<p>External link: <a href='".$link."' target='_blank'>".$address."</a></p>";
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

			//Recipients
			$mail->setFrom($from, 'Avarkey');
			$mail->addAddress($to);

			//Content
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $this->cleanup(array("</title>", "</h4>", "</p>"), $body);

			$mail->send();
			$this->errorMsg = 'Message has been sent.';
			$this->errorCode = 0;
		} catch (Exception $e) {
			$this->errorMsg = 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
			$this->errorCode = 96;
			
			throw new Exception($e->getMessage(), 406);
		}
	}

	private function removeBooking($mysqli, $userId, $bookingId) {
		$bookingIds = array();
		
		$sql = "SELECT id FROM booking WHERE userId=%d;";	
		$sql = sprintf($sql, $userId);
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($bookingIds, intval($row["id"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM booking_file WHERE bookingId IN (%s) AND bookingId=%d;";
		$sql = sprintf($sql, implode(",", $bookingIds), $bookingId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
		
		$sql = "DELETE FROM booking WHERE userId=%d AND id=%d;";
		$sql = sprintf($sql, $userId, $bookingId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $sql.$mysqli->error), 507);
		}
	}
	
	private function getObjectPrice($price) {
		$data = array();
		
		foreach ($price as $key => $value) {
			$obj = new \stdClass();
			$obj->currency = $key;
			$obj->fee = $value;
			
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