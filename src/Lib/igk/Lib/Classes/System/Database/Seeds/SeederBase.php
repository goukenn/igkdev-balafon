<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SeederBase.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Database\Seeds;

/**
 * represent the core seeder base class
 * @package IGK\System\Database\Seeds
 */
abstract class SeederBase{
    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;
    /**
    * auto generate doc.
    * @param array $tab
    * @param int $count
    * @param mixed & $indexes
    * @return array
    */
    protected function getRandomValues(array $tab,int $count, & $indexes=null){
		$indexes = array_rand($tab, min(count($tab), $count));
		$values = [];
		foreach($indexes as $i){
			$values[] = $tab[$i];
		} 
		return $values;
	}
    /**
    * Runs.
    */
    abstract function run();
}