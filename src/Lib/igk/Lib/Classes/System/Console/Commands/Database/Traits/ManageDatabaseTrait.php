<?php
// @author: C.A.D. BONDJE DOUE
// @file: ManageDatabaseTrait.php
// @date: 20250829 15:51:06
namespace IGK\System\Console\Commands\Database\Traits;

use IGK\System\Console\Logger;

/**
* 
* @package IGK\System\Console\Commands\Database\Traits
* @author C.A.D. BONDJE DOUE
*/
trait ManageDatabaseTrait{
    abstract function getModelClass();

    public function exec($command, ?string $action=null)
	{
		if ($action){
			if (method_exists($this, $fc = '_handle_'.$action)){
				$args = array_slice(func_get_args(), 2);
				array_unshift($args, $command);
				return call_user_func_array([$this, $fc], $args);
			}
		} 
		array_map(function ($a) {
			Logger::print($a->name."\r\t\t\t".$a->url);
		}, $this->getModelClass()::select_all());
	}
    protected function _handle_add($command, ?string $names=null){
		if ($names){
			array_map(function($i){ 
				$c = trim($i);
				$this->getModelClass()::AddIfNotExists($i, 'https://'.$c.'.com');
			}, explode(',', $names));
		}
	}
}