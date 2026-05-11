<?php
use IGK\Controllers\BaseController;
/**
* controller define in a module
* @param BaseController $ctrl
* @return mixed
*/
function igk_sys_is_module_controller(BaseController $ctrl):bool{
    $dir = $ctrl->getDeclaredDir(); 
    if (get_class($ctrl)=='igk\\pay\\paypal\\paypalpaymentCtrl'){
        igk_dev_wln_e(__FILE__.':'.__LINE__,  "try add .....");
    } 
    return igk_sys_is_path_in_module($dir);
}
/**
* check that a path is in module
* @param string $path real path
* @return mixed
*/
function igk_sys_is_path_in_module(string $path):bool{
    $mod = realpath(igk_get_module_dir());
    return igk_str_startwith($path, $mod);
}
/**
 * get current module
 * @param string $dir directory to search
 * @return null|string 
 * @throws IGKException 
 */
function igk_get_current_module_name(string $dir): ?string{
    if (!igk_io_file_exists($dir)){
        return null;
    }
    $modules = igk_get_modules();
    $mp = array_keys($modules);
    rsort($mp);
    $rm = null;
    $g = igk_get_module_name($dir);
    foreach($mp as $k){
        if (strstr($g, $k)){
            $rm = $k;
            break;
        }
    }
    return $rm;
}