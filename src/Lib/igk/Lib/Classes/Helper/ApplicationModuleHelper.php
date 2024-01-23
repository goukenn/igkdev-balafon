<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleHelper.php
// @date: 20230303 18:15:13
namespace IGK\Helper;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\Helpers
*/
class ApplicationModuleHelper{
    /**
     * get module name form class 
     * @var  $class_name get module name form class 
     */
    public static function GetModuleNameFromTestClass(string $class_name):string{
        $dir = igk_io_collapse_path(dirname(igk_sys_reflect_class($class_name)->getFileName()));
        $dir = igk_str_rm_start($dir, "%modules%/");
        if (false !== ($pos = strpos($dir, "/Lib/Tests"))){
            $dir = substr($dir, 0, $pos);
        }
        $dir = igk_str_rm_last($dir, "/Lib/Tests");
        if (empty($dir)){
            return null;
        }
        return $dir;
    }

    /**
     * import required module 
     * @param array $required_conf 
     * @param BaseController $ctrl 
     * @return void 
     */
    public static function ImportRequiredModule(array $required_conf, BaseController $ctrl){ 
        // + | load build requirement
        array_map(function($n){
            if (empty($n))return;
            igk_require_module($n);
        },array_keys($required_conf));  
    }
}