<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleHelper.php
// @date: 20230303 18:15:13
namespace IGK\Helpers;


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
}