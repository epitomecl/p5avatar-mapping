<?php

namespace avatar;

use \JsonSerializable as JsonSerializable;

class Avatar implements JsonSerializable {
	
	public function jsonSerialize()
	{
		return array(
			'ssid' => $this->ssID,
			'walletAddress' => $this->walletAddress,
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
	private $walletAddress;
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
		$this->walletAddress = "";
	}
	
	public function setSSID($ssID){
		$this->ssID = trim($ssID);
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
		$width = 256;
		$height = 256;

		$im = imagecreatetruecolor($width, $height);
		$snow = imagecolorallocate($im, 255, 250, 250);
		$white = imagecolorallocate($im, 255, 255, 255);
		$red = imagecolorallocate($im, 255, 0, 0);
		$black = imagecolorallocate($im, 0, 0, 0);
		$blue = imagecolorallocate($im, 0, 0, 192);
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
		
		$colorset = array("#e6194B", "#3cb44b", "#ffe119", "#4363d8", "#f58231", 
						"#911eb4", "#42d4f4", "#f032e6", "#bfef45", 
						"#fabebe", "#469990", "#e6beff", "#9A6324",
						"#fffac8", "#800000", "#aaffc3", "#808000",
						"#ffd8b1", "#000075", "#a9a9a9");
		
		
		
		
		
		$map = array(
			"40007777",
			"00788888",
			"00782008",
			"00788888",
			"00007888",
			"00007810",
			"00007777",
			"00000000"
			);
		
		$map = array(
			"40000000",
			"00077777",
			"00700888",
			"00788880",
			"00788880",
			"00788888",
			"00077777",
			"00000000"
			);

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
		
		$fields = array();
		$index = 0;
		$yPos = 0;
		for ($row = 0; $row < count($map); $row++) {
			$line = $map[$row];
			$xPos = 0;
			for ($column = 0; $column < strlen($line); $column++) {
				$tile = intval(substr($line, $column, 1));
				
				if ($tile > 0 ) {

					$hex = substr($hash, $index, 2);
					$dec = hexdec($hex);
					$color = $dec & 0xF;
					$xIndex = $dec >> 6;
					$yIndex = ($dec >> 4) & 3;
					$c = $this->hexToRgb($colorset[$color]);
					$color = imagecolorallocate($im, $c["r"], $c["g"], $c["b"]);
					$field = new Field($tile, $color);
					
					switch ($tile) {
						case 8:
						case 7:
							$field->setXY($xPos + $xIndex * 4 + 2, $yPos + $yIndex * 8 + 4);
							//$field->setXY($xPos, $yPos);
							break;
						case 4:
							$field->setXY($xPos, $yPos);
							break;
						case 2:
							$field->setXY($xPos, $yPos);
							break;
						case 1:
							$field->setXY($xPos, $yPos);
							break;
					}
					//echo sprintf("index: %d, xPos:%d, yPos:%d", count($fields), $field->getXpos(), $field->getYpos());
					//echo "<br>";
					array_push($fields, $field);
					
					$index += 2;
				}
				$xPos += 16;
			}

			$yPos += 32;
		}
		
		// prepare voronoi map
/*         for ($x = 0; $x < $width / 2; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $n = 0;
                for ($i = 0; $i < $cells; $i++) {
                    if ($this->distance($px[$i], $x, $py[$i], $y) < $this->distance($px[$n], $x, $py[$n], $y)) {
                        $n = $i;
                    }
                }
                imagesetpixel ($im , $x , $y , $color[$n]);
            }
        } */
		
        //for ($x = 0; $x < $width / 2; $x++) {
        //    for ($y = 0; $y < $height; $y++) {
        //        $n = 0;
        //        for ($i = 0; $i < count($fields); $i++) {
		//			if ($fields[$i]->getTile() == 8 || $fields[$i]->getTile() == 7) {
		//				if ($this->distance($fields[$i]->getXpos(), $x, $fields[$i]->getYpos(), $y) < $this->distance($fields[$n]->getXpos(), $x, $fields[$n]->getYpos(), $y)) {
		//					$n = $i;
		//				}
		//			}
        //        }
        //        imagesetpixel ($im , $x , $y, $fields[$n]->getColor());
        //    }
        //}		
		
		// draw travel points
        for ($i = 0; $i < count($fields); $i++) {
			if ($fields[$i]->getTile() == 7) {
				$xPos = $fields[$i]->getXpos();
				$yPos = $fields[$i]->getYpos();
				$color = imagecolorat($im, $xPos, $yPos); 
				$rgb = $this->hexToRgb($this->colorInverse($color));
				$c = imagecolorallocate($im, $rgb['r'], $rgb['g'], $rgb['b']);
			
				// imagefilledellipse($im, $xPos, $yPos, 2, 2, $c);
			}
        }

		$data = array();
		
		for ($i = 0; $i < count($fields); $i++) {
			if ($fields[$i]->getTile() == 7) {
				array_push($data, $fields[$i]);
				
				if (count($data) == 5) {
					$data = array_reverse($data);
				}
			}
        }
		
		$points = array();
		for ($i = 0; $i < count($data); $i++) {
			$xPos = $data[$i]->getXpos();
			$yPos = $data[$i]->getYpos();
			
			if ($i < 5) {
				$yPos -= 6;
			} else if ($i > count($data) - 5) {
				$yPos += 6;
			} else {
				$xPos -= 6;
			}
			array_push($points, $xPos);
			array_push($points, $yPos);
		}
		array_push($points, 128);
		array_push($points, $data[count($data) - 1]->getYpos());
		array_push($points, 128);
		array_push($points, $data[0]->getYpos());
		
		//imagepolygon($im, $points, count($points) / 2, $blue);
		


		// eyebrow.draw($x, $y, $byte);
		// eye.draw($x, $y, 48, 32, $byte);
		// iris.draw($x, $y, 24, $byte, $byte, $byte);
		// eyebrow.draw($x, $y, $byte);
		// eye.draw($x, $y, 48, 32, $byte);
		// iris.draw($x, $y, 24, $byte, $byte, $byte);
		
		// nose.draw($x, $y, $byte);
		// ear.draw($x, $y, $byte, $byte);
		// forehead.draw($x, $y, $byte);
		// cheekbone.draw($x, $y, $byte);
		// cheek.draw($x, $y, $byte);
		// chin.draw($x, $y, $byte);
		
		//create masking
		//$mask = imagecreatetruecolor($width/2, $height);
		//$transparent = imagecolorallocate($mask, 255, 0, 0);
		//imagecolortransparent($mask, $transparent);
		//imagefilledpolygon($mask, $points, count($points)/2, $transparent);
		//$red = imagecolorallocate($mask, 0, 0, 0);
		//imagecopymerge($im, $mask, 0, 0, 0, 0, $width/2, $height, 100);
		//imagecolortransparent($im, $red);
		//imagefill($im, 0, 0, $red);			
		
		
		$index = 0;
		$yPos = 0;
		for ($row = 0; $row < count($map); $row++) {
			$line = $map[$row];
			$xPos = 0;
			for ($column = 0; $column < strlen($line); $column++) {
				$tile = intval(substr($line, $column, 1));
				
					$hex = substr($hash, $index, 2);
					$dec = hexdec($hex);
					$color = $dec & 0xF;
					$xIndex = $dec >> 6;
					$yIndex = ($dec >> 4) & 3;
					
				if ($tile == 8 || $tile == 7) {
					switch ($tile) {
						case 8:
							//imagerectangle ($im, $xPos, $yPos, $xPos + 15, $yPos + 31, $white );
							//$field->setXY($xPos + $xIndex * 4 + 2, $yPos + $yIndex * 8 + 4);
							//$field->setXY($xPos, $yPos);
							break;
						case 4:
							//$field->setXY($xPos, $yPos);
							break;
						case 2:
							//$field->setXY($xPos, $yPos);
							break;
						case 1:
							//$field->setXY($xPos, $yPos);
							break;
					}
					
					//ImageString($im, 5, $xPos + 2, $yPos, dechex($color), $white);

					$index += 2;
				} else {
					//imagefilledrectangle ($im, $xPos, $yPos, $xPos + 15, $yPos + 31, $snow );
					if ($tile > 0) {
						//ImageString($im, 5, $xPos + 2, $yPos, dechex($color), $blue);
					}
				}
				$xPos += 16;
			}

			$yPos += 32;
		}		

		
		// image mirrowed
		//$tmp = imagecreatetruecolor($width / 2, $height);
		//imagecopy($tmp, $im, 0, 0, 0, 0, $width / 2, $height);
		//imageflip($tmp, IMG_FLIP_HORIZONTAL);
		//imagecopy($im, $tmp, $width / 2, 0, 0, 0, $width / 2, $height);

		$prefix = "";
		$object = 
		$this->defaultCat($hash, $prefix);
		$im = $object->avatar;
		
		//$background = imagecreatefrompng(__DIR__."/../../img/background.png");
		//imageSaveAlpha($background, true);
		//imagecopy($background, $im, 0, 0, 0, 0, $width, $height);
		//imagecopy($im, $background, 0, 0, 0, 0, $width, $height);
		
		

		
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
		$this->walletAddress = $walletAddress;
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
	
	public function prepare($hash, $im, $colorset) {
		$fields = array();
		$data = file_get_contents(__DIR__."/head.txt");
		$lines = explode(PHP_EOL, $data);
		$yPos = 0;
		$index = 0;
		foreach ($lines as $foo => $line){
			$xPos = 0;
			for ($column = 0; $column < strlen($line); $column++) {
				$tile = intval(substr($line, $column, 1));
				
				if ($tile > 0 && $index < 64) {
					$hex = substr($hash, $index, 1);
					$dec = hexdec($hex);
					$color = $dec & 0xF;
					$xyPos = $dec >> 4;
					$c = $this->hexToRgb($colorset[$color]);
					$color = imagecolorallocate($im, $c["r"], $c["g"], $c["b"]);
					$field = new Field($tile, $color);
					//echo $hex . " " . decbin($dec) . " ".$xyPos."<br>";
					switch ($tile) {
						case 1:
							$field->setXY($xPos, $yPos - $xyPos);
							break;
						case 2:
							$field->setXY($xPos + $xyPos, $yPos - $xyPos);
							break;						
						case 3:
							$field->setXY($xPos + $xyPos, $yPos);
							break;						
						case 4:
							$field->setXY($xPos + $xyPos, $yPos + $xyPos);
							break;
						case 5:
							$field->setXY($xPos, $yPos + $xyPos);
							break;
						case 6:
							$field->setXY($xPos - $xyPos, $yPos + $xyPos);
							break;
						case 7:
							$field->setXY($xPos - $xyPos, $yPos);
							break;
						case 8:
							$field->setXY($xPos - $xyPos, $yPos - $xyPos);
							break;
						default:
							$field->setXY($xPos, $yPos);
							break;
					}
					
					array_push($fields, $field);					

					if ($tile < 9) {
						$index++;
					}
				}

				$xPos += 2;
			}
			$yPos += 4;
		}
		
		return $fields;
	}
}

	