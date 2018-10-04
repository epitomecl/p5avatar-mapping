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
    //    $d = sqrt(($x1 - $x2) * ($x1 - $x2) + ($y1 - $y2) * ($y1 - $y2)); // Euclidian
		$d = abs($x1 - $x2) + abs($y1 - $y2); // Manhattan
    //  $d = Math.pow(Math.pow(Math.abs($x1 - $x2), p) + Math.pow(Math.abs(y1 - y2), p), (1 / p)); // Minkovski
        
		return $d;
    }

	function hexToRgb($hex, $alpha = false) {
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
	
	public function setWalletAddress($walletAddress) {
		$hash = hash('sha256', $walletAddress);			
		$width = 256;
		$height = 256;

		$im = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($im, 255, 255, 255);
		$red = imagecolorallocate($im, 255, 0, 0);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagefill($im, 0, 0, $white);

		$cells = 16;
        $px = array();
        $py = array();
        $color = array();
		$map = array("#99FFCC", "#CCCC99", "#CCCCCC", "#CCCCFF", 
					"#CCFF99", "#CCFFCC", "#CCFFFF", "#FFCC99", 
					"#FFCCCC", "#FFCCFF", "#FFFF99", "#FFFFCC",
					"#2897B7", "#D9D9D9", "#EFEFEF", "#FF7575"
				);
				
		// prepare param set
        for ($i = 0; $i < $cells; $i++) {
            $px[$i] = hexdec(substr($hash, $i * 4, 2));
            $py[$i] = hexdec(substr($hash, ($i * 4) + 2, 2));
            $c = $this->hexToRgb($map[$i]);
			$color[$i] = imagecolorallocate($im, $c["r"], $c["g"], $c["b"]);
        }
		
		// prepare voronoi map
        for ($x = 0; $x < $width; $x++) {
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
		
		// draw travel route
		for ($i = 0; $i < $cells; $i++) {
			if ($i + 1 < $cells) {
				imageline($im, $px[$i], $py[$i], $px[$i + 1], $py[$i + 1], $red);
			}
		}
		
		// draw start and travel points
        for ($i = 0; $i < $cells; $i++) {
			if ($i == 0) {
				imagefilledellipse($im, $px[$i] - 4, $py[$i] - 4, 8, 8, $red);
			} else {
				imagefilledellipse($im, $px[$i] - 3, $py[$i] - 3, 6, 6, $black);
			}
        }

		ob_start();
		imagepng($im);
		$data = ob_get_contents();
		ob_end_clean(); 
		
		
		$this->walletAddress = $walletAddress;
		$this->hashData = $hash;
		$this->imageData = sprintf("data:image/png;base64,%s", base64_encode($data));
	}
}

	