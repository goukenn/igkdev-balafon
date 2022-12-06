<?php


/**
 * get current module module
 * @param string $dir 
 * @return null|string 
 * @throws IGKException 
 */
function igk_get_current_module_name(string $dir): ?string{
    if (!file_exists($dir)){
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