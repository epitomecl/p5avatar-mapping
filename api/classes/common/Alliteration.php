<?php 

namespace common;

class Alliteration {
    protected $_adjectives;
    protected $_nouns;

    public function __construct() {
        $this->_adjectives = file(__DIR__ . '/../../data/adjectives.txt', FILE_IGNORE_NEW_LINES);
        $this->_nouns = file(__DIR__ . '/../../data/nouns.txt', FILE_IGNORE_NEW_LINES);
    }

    public function getName() {
        $adjective = $this->getRandomWord($this->_adjectives);
        $noun = $this->getRandomWord($this->_nouns);

        return ucwords("{$adjective} {$noun}");
    }

	public function getTopTen() {
		$titles = array();
		
		for ($i = 0; $i < 10; $i++) {
			array_push($titles, $this->getName());
		}
		
		return $titles;
	}
	
    protected function getRandomWord(array $words) {
        return $words[array_rand($words)];
    }
}