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
    var $controller;
   /**
    * 
    * @param array $tab 
    * @param int $count 
    * @param mixed $indexes generated random indexes
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
    abstract function run();
}