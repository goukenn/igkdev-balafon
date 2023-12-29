<?php
// @author: C.A.D. BONDJE DOUE
// @filename: view.php
// @date: 20231228 11:27:28
// @desc: views fonction helpers

use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;

if (!function_exists('article')) {
    function article(string $file, $params=null)
    {
       return ViewHelper::Article($file, $params);  
    }
}

if (!function_exists('asset')) {
    function asset(string $file)
    {
       return ViewHelper::CurrentCtrl()->asset($file); 
    }
 }

 if (!function_exists('__')) {
    /**
     * shortcut to core translation . igk_resource_gets
     * @param string $msg 
     * @param mixed $default 
     * @param mixed $params 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    function __(string $msg, $default=null,...$params)
    {
       return igk_resources_gets($msg, $default,...$params); 
    }
 }