<?php

namespace admin;

class ApiKey {
	public function __construct() {
		
	}
	
	public function execute($firstName, $lastName, $eMail, $message) {
		$obj = new \stdClass;
		$obj->success = true;
		
		echo json_encode($obj, JSON_UNESCAPED_UNICODE);			
	}
	
	private function send($eMail, $firstName, $lastName, $link) {
		$from = '<s681562@gmail.com>';
		$to = $eMail;
		$subject = 'Hi!';
		$body = "Hi,\n\nHow are you?";

		$headers = array(
			'From' => $from,
			'To' => $to,
			'Subject' => $subject
		);

		$smtp = Mail::factory('smtp', array(
			'host' => 'ssl://smtp.gmail.com',
			'port' => '465',
			'auth' => true,
			'username' => 'johndoe@gmail.com',
			'password' => 'passwordxxx'
		));

		$mail = $smtp->send($to, $headers, $body);

		if (PEAR::isError($mail)) {
			echo('<p>' . $mail->getMessage() . '</p>');
		} else {
			echo('<p>Message successfully sent!</p>');
		}		
	}
}