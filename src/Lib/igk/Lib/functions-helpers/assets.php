<?php

// @author: C.A.D. BONDJE DOUE
// @filename: assets.php
// @date: 20230119 08:33:17
// @desc: store assets helper utility

use IGK\System\IO\Path;

if (!function_exists('igk_get_asset_uri')){
    function igk_get_asset_uri($dir, $file){
        $q = parse_url($file);         
        $path = $q['path']   ;
        $file = Path::Combine($dir, $path);
        if ($query = igk_getv($q, 'query')){
            $query = '?'.$query;
        } 
        return $file.$query;
    }
}
if (!function_exists('igk_load_temp_style_asset')){
    function igk_load_temp_style_asset($doc, $ctrl, $assets){
         
        $ctrl::resolveAssets(["/"]);        
        $dir = $ctrl->getAssetsDir();
        foreach($assets as $f){
            if (empty($f))continue;
            $uri = igk_get_asset_uri($dir, $f);          
            $doc->addTempStyle($uri); 
        }
    }
}
if (!function_exists('igk_load_temp_script_asset')){
    function igk_load_temp_script_asset($doc, $ctrl, $assets){
        $ctrl::resolveAssets(["/"]);
        $dir = $ctrl->getAssetsDir();
        foreach($assets as $f){
            if (empty($f))continue;
            $uri = igk_get_asset_uri($dir, $f);          
            $doc->addTempScript($uri)->activate('defer');  
        }
    }
}
