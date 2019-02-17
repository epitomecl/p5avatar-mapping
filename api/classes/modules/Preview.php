<?php 
namespace modules;

use \common\AvatarBuilder as AvatarBuilder;

/**
* Build preview of avatar based on selected files. 
* The user selected from each layer one file with its file id.
* For instance 2, 22, 35, 56, 68, 77.
* Based on order of layer the avatar will be build.
* Avatar in use will be marked.
* 
*/
class Preview {
	private $mysqli;
	
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}
	
	/**
	* something describes this method
	*
	* @param array $fileIds The id as array or comma separated list
	*/		
	public function doPost($fileIds) {
		$mysqli = $this->mysqli;		
		$builder = new AvatarBuilder();
		$ids = array();
		
		// check all variants of given ids as array or comma separated list or both
		if (is_array($fileIds)) {
			foreach ($fileIds as $index => $id) {
				$ids = array_merge($ids, explode(",", $id));
			}
		} else {
			$ids = array_merge($ids, explode(",", $fileIds));
		}
		
		// remove empty items
		$ids = array_filter($ids);
		
		if (count($ids) == 0) {
			array_push($ids, 0);
		}

		$obj = $builder->previewAvatar($mysqli, $ids);

		echo json_encode($obj, JSON_UNESCAPED_UNICODE);
	}
}