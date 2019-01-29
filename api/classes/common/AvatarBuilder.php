<?php 
namespace common;

use \JsonSerializable as JsonSerializable;

class AvatarBuilder implements JsonSerializable {
	
	public function jsonSerialize()
	{
		return array(
			'ssid' => $this->ssID,
			'address' => $this->address,
			'hashData' => $this->hashData,
			'prefix' => $this->prefix,
			'category' => $this->category,
			'parts' => $this->parts,
			'imageData' => $this->imageData,
			'errorCode' => $this->errorCode
		);
    }

	private $prefix;
	private $parts;
	private $category;
	private $address;
	private $hashData;
	private $imageData;
    private $errorCode;
	private $ssID;
	
	public function __construct() {
		$this->ssID = "";
		$this->prefix = "";
		$this->parts = array();
		$this->category = "";
		$this->hashData = "";
		$this->imageData = "";
        $this->errorCode = 0;
		$this->address = "";
	}
	
	public function setSSID($ssID){
		$this->ssID = trim($ssID);
	}
	
	private function imagefillroundedrect($im,$x,$y,$cx,$cy,$rad,$col) {
		// Draw the middle cross shape of the rectangle
		imagefilledrectangle($im,$x,$y+$rad,$cx,$cy-$rad,$col);
		imagefilledrectangle($im,$x+$rad,$y,$cx-$rad,$cy,$col);

		$dia = $rad*2;

		// Now fill in the rounded corners
		imagefilledellipse($im, $x+$rad, $y+$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $x+$rad, $cy-$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $cx-$rad, $cy-$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $cx-$rad, $y+$rad, $rad*2, $dia, $col);
	}
	

	
	public function setAddress($address) {
		$hash = hash('sha256', $address);			
		$width = 256;
		$height = 256;

		$im = imagecreatetruecolor($width, $height);
		$snow = imagecolorallocate($im, 255, 250, 250);
		$white = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $white);

		$prefix = "";
		$object = $this->defaultCat($hash, $prefix);
		$im = $object->avatar;
				
		//create masking
		$mask = imagecreatetruecolor($width, $height);
		
		$white   = imagecolorallocate($mask, 255, 255, 255);
		imagefill($mask, 0, 0, $white);
		
		$transparent = imagecolorallocate($mask, 255, 0, 0);
		imagecolortransparent($mask, $transparent);
		$this->imagefillroundedrect($mask, 0, 0, $width, $height, 10, $transparent);

		imagecopymerge($im, $mask, 0, 0, 0, 0, $width, $height, 100);
		imagecolortransparent($im, $white);
		//imagefill($im, 0, 0, $white);		
		
		ob_start();
		imagepng($im);
		$data = ob_get_contents();
		ob_end_clean(); 
		
		$this->prefix = $prefix;
		$this->parts = $object->parts;
		$this->category = $object->category;
		$this->address = $address;
		$this->hashData = $hash;
		$this->imageData = sprintf("data:image/png;base64,%s", base64_encode($data));
	}
	
	public function defaultCat($seed='', $prefix='') {
    // init random seed
		if($seed) srand( hexdec(substr(md5($prefix.$seed),0,6)) );

		// throw the dice for body parts
		$parts = array(
			'background' => rand(1,16),
			'body' => rand(1,15),
			'fur' => rand(1,8),
			'eyes' => rand(1,15),
			'mouth' => rand(1,10),
			'accessorie' => rand(1,18)
		);

		// create backgound
		$monster = imagecreatetruecolor(256, 256) or die("GD image create failed");
		//$monster = imagecreatefrompng(__DIR__."/../../img/background.png");
		$white   = imagecolorallocate($monster, 255, 255, 255);
		imagefill($monster,0,0,$white);

		// add parts
		foreach($parts as $part => $num){
			$file = dirname(__FILE__).'/../../images/cat/'.$part.'_'.$num.'.png';

			$im = imagecreatefrompng($file);
			if(!$im) die('Failed to load '.$file);
			imageSaveAlpha($im, true);
			imagecopy($monster,$im,0,0,0,0,256,256);
			imagedestroy($im);
		}

		// restore random seed
		if($seed) srand();
		
		$data = new \stdClass;
		$data->category = "cat";
		$data->parts = $parts;
		$data->avatar = $monster;
		
		return $data;
	}
	
	
}

	