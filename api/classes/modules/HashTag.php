<?php

namespace modules;

use \JsonSerializable as JsonSerializable;
use \Exception as Exception;

/**
* If user session is alive, a common set of hashtags, 
* favors by other designer, will responded.
*/
class HashTag implements JsonSerializable{
	private $mysqli;
	private $hashtags;
	
	public function jsonSerialize() {
		return array(
			'hashtags' => $this->hashtags
        );
    }
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;		
		$this->hashtags = array("cute, awesome, unique");
	}
	
	/**
	* something describes this method
	*
	* @param int $canvasId The id of canvas
	*/	
	public function doPost($canvasId) {
		$mysqli = $this->mysqli;
		
		$data = array();
		$sql = "SELECT DISTINCT hashtag FROM hashtag WHERE canvasId=%d";
		$sql = sprintf($sql, $canvasId);
		
		if ($result = $mysqli->query($sql)) {
			while ($row = $result->fetch_assoc()) {
				array_push($data, trim($row["hashtag"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}			
		
		$this->hashtags = $data;
					
		echo json_encode($this, JSON_UNESCAPED_UNICODE);			
	}
	
	public function doGet() {
		$mysqli = $this->mysqli;
		
		$sql = "SELECT DISTINCT hashtag FROM hashtag";
		if ($result = $mysqli->query($sql)) {
			$this->hashtags = array();
			while ($row = $result->fetch_assoc()) {
				array_push($this->hashtags, trim($row["hashtag"]));
			}
		} else {
			throw new Exception(sprintf("%s, %s", get_class($this), $mysqli->error), 507);
		}			
		
		echo json_encode($this, JSON_UNESCAPED_UNICODE);			
	}	
}