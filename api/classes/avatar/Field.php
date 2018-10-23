<?php

namespace avatar;

class Field {
	private $xPos;
	private $yPos;
	private $tile;
	private $color;
	
	public function __construct($tile, $color) {
		$this->tile = $tile;
		$this->color = $color;
	}
	
	public function setXY($xPos, $yPos) {
		$this->xPos = $xPos;
		$this->yPos = $yPos;
	}
	
	public function getXpos() {
		return $this->xPos;
	}
	
	public function getYpos() {
		return $this->yPos;
	}
	
	public function getColor() {
		return $this->color;
	}
	
	public function getTile() {
		return $this->tile;
	}
}