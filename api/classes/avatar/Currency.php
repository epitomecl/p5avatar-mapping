<?php

namespace avatar;

class Currency {
	
	public function __construct() {
	}
	
	private function fetchData() {
		$html = "";
		$url = 'https://en.wikipedia.org/wiki/List_of_cryptocurrencies';
		
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

	public function getTopTen() {
		$currency = array();
		$supported = array("EOS","ETH");
		$html = $this->fetchData();
	
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
}