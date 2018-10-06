<?php

namespace avatar;

use \JsonSerializable as JsonSerializable;

class Avatar implements JsonSerializable {
	
	public function jsonSerialize()
	{
		return array(
			'walletAddress' => $this->walletAddress,
			'hashData' => $this->hashData,
			'imageData' => $this->imageData,
			'errorCode' => $this->errorCode
		);
    }

	private $walletAddress;
	private $hashData;
	private $imageData;
    private $errorCode;
	
	public function __construct() {
		$this->hashData = "";
		$this->imageData = "";
        $this->errorCode = 0;
		$this->walletAddress = "";
	}
	
	private function distance($x1, $x2, $y1, $y2) {
        $d = 0.0;
		$d = sqrt(($x1 - $x2) * ($x1 - $x2) + ($y1 - $y2) * ($y1 - $y2)); // Euclidian
		// $d = abs($x1 - $x2) + abs($y1 - $y2); // Manhattan
		// $d = pow(abs($x1 - $x2), 3) + pow(abs($y1 - $y2), 3); // Minkovski

		return $d;
    }

	private function hexToRgb($hex, $alpha = false) {
		$hex      = str_replace('#', '', $hex);
		$length   = strlen($hex);
		$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		if ( $alpha ) {
			$rgb['a'] = $alpha;
		}
		return $rgb;
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
	
	private function colorInverse($color){
		$hex = dechex($color);
		$rgb = '';
		for ($x=0;$x<3;$x++){
			$c = 255 - hexdec(substr($hex, (2*$x), 2));
			$c = ($c < 0) ? 0 : dechex($c);
			$rgb .= (strlen($c) < 2) ? '0'.$c : $c;
		}
		return '#'.$rgb;
	}
	
	public function setWalletAddress($walletAddress) {
		$hash = hash('sha256', $walletAddress);			
		$width = 32 * 6;
		$height = 32 * 6;

		$im = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($im, 255, 255, 255);
		$red = imagecolorallocate($im, 255, 0, 0);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagefill($im, 0, 0, $white);

		$cells = 16;
        $px = array();
        $py = array();
        $color = array();
				
		// 8D6D35CC70BA14FBE1DBCA3057791135512F4CC87C97E42200361111F4B45364
		// prepare param set
        for ($i = 0; $i < $cells; $i++) {
			$x = hexdec(substr($hash, $i * 4, 2));
			$xShift = $x >> 3;
			$xAnd = $x & 7;
			$y = hexdec(substr($hash, ($i * 4) + 2, 2));
			$yShift = $y >> 3;
			$yAnd = $y & 7;
			$c = ($xAnd << 3) + $yAnd;
            $px[$i] = $xShift;
            $py[$i] = $yShift;
			$r = $c >> 4;
			$g = ($c & 12) >> 2;
			$b = $c & 3;
			$color[$i] = imagecolorallocate($im, 10 + $r * 60, 10 + $g * 60, 10 + $b * 60);
        }
		
		$i = 0;
		for ($x = 0; $x < 3; $x++) {
			for ($y = 0; $y < 6; $y++) {
				if ($x == 0 && ($y == 0 || $y == 5)) {
					continue;
				}
				$px[$i] = $px[$i] + $x * 32;
				$py[$i] = $py[$i] + $y * 32;
				$i++;
			}
		}
		
		// prepare voronoi map
        for ($x = 0; $x < $width / 2; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $n = 0;
                for ($i = 0; $i < $cells; $i++) {
                    if ($this->distance($px[$i], $x, $py[$i], $y) < $this->distance($px[$n], $x, $py[$n], $y)) {
                        $n = $i;
                    }
                }
                imagesetpixel ($im , $x , $y , $color[$n]);
            }
        }
		
		// draw travel points
        for ($i = 0; $i < $cells; $i++) {
			$color = imagecolorat($im, $px[$i], $py[$i]); 
			$rgb = $this->hexToRgb($this->colorInverse($color));
			$c = imagecolorallocate($im, $rgb['r'], $rgb['g'], $rgb['b']);
			
			if ($i == 5) {
				imagefilledellipse($im, $px[$i], $py[$i] - 2, 18, 12, $color[$i]);
				imagefilledellipse($im, $px[$i], $py[$i], 18, 12, $c);
				imagefilledellipse($im, $px[$i], $py[$i], 3, 3, $color[$i]);			
			} else {
				imagefilledellipse($im, $px[$i], $py[$i], 3, 3, $c);
			}
        }

		$tmp = imagecreate($width / 2, $height);
		imagecopy($tmp, $im, 0, 0, 0, 0, $width / 2, $height);
		imageflip($tmp, IMG_FLIP_HORIZONTAL);
		imagecopy($im, $tmp, $width / 2, 0, 0, 0, $width / 2, $height);
		
		//create masking
		$mask = imagecreatetruecolor($width, $height);
		$transparent = imagecolorallocate($mask, 255, 0, 0);
		imagecolortransparent($mask, $transparent);
		$this->imagefillroundedrect($mask, 0, 0, $width, $height, 10, $transparent);
		$red = imagecolorallocate($mask, 0, 0, 0);
		imagecopymerge($im, $mask, 0, 0, 0, 0, $width, $height, 100);
		imagecolortransparent($im, $red);
		imagefill($im, 0, 0, $red);		
		
		ob_start();
		imagepng($im);
		$data = ob_get_contents();
		ob_end_clean(); 
		
		$this->walletAddress = $walletAddress;
		$this->hashData = $hash;
		$this->imageData = sprintf("data:image/png;base64,%s", base64_encode($data));
	}
}

	