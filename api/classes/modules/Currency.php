<?php

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, a list currency will be responded.
* All currency properties hold an "supported" attribute.
* Intern shortname (3 chars) of crypto currency will be used. 
* POST update current currency of canvas.
* GET prepared information set about all useful crypto currencies (wikipedia).
*/
class Currency implements JsonSerializable{
	private $mysqli;
	private $url;
	
	public function jsonSerialize() {
		return array(
			'success' => true
        );
    }
	
	public function __construct() {
		$this->url = 'https://en.wikipedia.org/wiki/List_of_cryptocurrencies';
	}
		
	/**
	* something describes this method
	*
	* @param int $canvasId The id of canvas	
	* @param string $currency The currency as shortcut
	*/		
	public function doPost($canvasId, $currency) {
		$mysqli = $this->mysqli;
		
		$sql = "UPDATE canvas SET currency='%s' WHERE id=%d";
		$sql = sprintf($sql, $price, $currency, $fileId);
		if ($mysqli->query($sql) === false) {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}
		if ($mysqli->affected_rows == 0 || $fileId == 0) {
			throw new Exception(sprintf("%s, %s", get_class($this), 'Not Found'), 404);
		}		
	}
	
	public function doGet() {
		echo json_encode($this->getList($this->url), JSON_UNESCAPED_UNICODE);
	}
	
	private function getList($url) {
		$currency = array();
		$supported = array("EOS","ETH");
		$html = $this->fetchHtml($url);
	
		// Prevent HTML errors from displaying
		libxml_use_internal_errors(true); 
		
		$dom = new \DOMDocument();
		$dom->validateOnParse = true;
		
		if (strlen($html) > 0) {
			$dom->loadHTML($html);
		}
		
		$tables = $dom->getElementsByTagName("table");

		foreach ($tables as $table) {
			//wikitable sortable jquery-tablesorter
			if (in_array("wikitable", explode(" ", trim($table->getAttribute("class"))))) {
				$rows = $table->getElementsByTagName("tr");
				foreach ($rows as $row) {
					$cells = $row->getElementsByTagName('td');
					if ($cells->length > 0) {
						$obj = new \stdClass;
						$cancel = 0;

						foreach ($cells as $index => $cell) {
							$nodeValue = trim($cell->nodeValue);
							$nodeValue = preg_replace('/\[\d+\]/', '', $nodeValue);

							switch ($index) {
								case 0:
									$release = intval($nodeValue);
									if ($release == 0) {
										$cancel = 1;
									} else {
										$obj->release = $release;
									}
									break;
								case 1:
									if (strcmp(strtolower($nodeValue), "inactive") == 0) {
										$cancel = 1;
									}
									break;
								case 2:
									$obj->currency = $nodeValue;
									break;
								case 3:
									$obj->symbol = explode(",", str_replace(" ", "", $nodeValue));
									$obj->supported = false;
									foreach ($supported as $key) {
										if (in_array($key, $obj->symbol)) {
											$obj->supported = true;
											break;
										}
									}
									break;
								case 8:
									$obj->notes = sprintf("%s.", trim($nodeValue, "."));
									break;
								default:
									continue;
									break;
							}

							if ($cancel == 1) {
								break;
							}
						}
						
						if (!$cancel) {
							array_push($currency, $obj);
						}
					}
				}		
			}
		}
		
		return $currency;
	}
	
	private function fetchHtml($url) {
		$html = "";
		
		if ($this->isCurl()) {
			if ($handle = curl_init($url)) {
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				$html = curl_exec($handle);	
				curl_close($handle);
			}
		} else {
			$html = file_get_contents($url);
		}
		
		return $html;
	}
	
	private function isCurl(){
		return function_exists('curl_version');
	}
}