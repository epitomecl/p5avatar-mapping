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
* DEL deleted the current booking data in case of abort by user.
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
	* @param string $userId The id of current user	
	* @param array $fileIds The id as array or comma separated list
	*/		
	public function doPost($userId, $fileIds) {
		$mysqli = $this->mysqli;		
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}
		
		$data = array();
		$sql = "SELECT id, fileId, fee, currency FROM user_booking ";
		$sql .= "LEFT JOIN file ON (file.id = user_booking.fileId) ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE user_booking.fileId IN (%d);";
		$sql .= "AND user_booking.userId=%d;";
		sprintf($sql, implode(",", $ids), $userId);
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		// todo something with data
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param string $userId The id of current user	
	* @param array $fileIds The id as array or comma separated list
	* @param string $address The address of a wallet		
	*/		
	public function doPut($userId, $fileIds, $address) {
		$mysqli = $this->mysqli;		
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}
		
		foreach ($ids as $index => $fileId) {
			$sql = "UPDATE file SET ownerId=%d WHERE fileId=%d;";
			$sql = sprintf($sql, $userId, $fileId, $modified);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}		
		}

		$sql = "DELETE FROM user_booking WHERE userId='%s' AND fileId IN (%s);";
		$sql = sprintf($sql, $userId, implode(",", $ids));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}		

		$builder = new AvatarBuilder();
		$image = $builder->getAvatarImageSource($mysqli, $fileIds);
		$path = realpath(dirname(__FILE__).'/../../images/avatars')."/";
		$fileName = sprintf("%s.%s", md5($userId.time()), "png");

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		
		imagepng($image, $path.$fileName);
		imagedestroy($image);

		if (file_exists($path.$fileName)) {
			$sql = "INSERT INTO user_avatar SET userId=%d, filename='%s', address='%s', modified=NOW();";
			$sql = sprintf($sql, $userId, $fileId, $address);
			if ($mysqli->query($sql) === false) {
				throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
			}			
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	* something describes this method
	*
	* @param string $userId The id of current user	
	* @param array $fileIds The id as array or comma separated list
	*/		
	public function doDel($userId, $fileIds) {
		$mysqli = $this->mysqli;		
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}
		
		$sql = "DELETE FROM user_booking WHERE userId='%s' AND fileId IN (%s);";
		$sql = sprintf($sql, $userId, implode(",", $ids));
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);		
	}

	/**
	* something describes this method
	*
	* @param string $userId The id of current user	
	* @param array $fileIds The id as array or comma separated list
	*/		
	public function doGet($userId, $fileIds) {
		$mysqli = $this->mysqli;		
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}
		
		$data = array();
		$sql = "SELECT id, userId, filename, orginal, ";
		$sql .= "CONCAT(canvas.name,'_',canvas.id,'/') AS canvasName FROM file ";
		$sql .= "LEFT JOIN layer ON (layer.id = file.layerId) ";		
		$sql .= "LEFT JOIN canvas ON (canvas.id = layer.canvasId) ";
		$sql .= "WHERE ownerId=%d AND file.id IN (%d);";		
		$sql = sprintf($sql, $userId, implode(",", $ids));
		if ($result = $mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$data[$row["id"]] = $row;
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		
		$status = array();
		foreach ($ids as $index => $fileId) {
			$obj = new \stdClass();
			$obj->fileId = $fileId;
			$obj->ownerId = 0;
			$obj->pending = 1;
			if (array_key_exists($fileId, $data)) {
				$obj->ownerId = intval($data["onwerId"]);
				$obj->pending = 0;
			}
			array_push($status, $obj);
		}
		
		echo json_encode($status, JSON_UNESCAPED_UNICODE);		
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