<?php

namespace IGK\Helper;

use IGK\Controllers\BaseController;


abstract class UriUtils{
    /**
     * get command action type
     * @param BaseController $ctrl 
     * @param mixed $u 
     * @param string $type 
     * @param mixed $port 
     * @return string 
     */
    public static function GetCmdAction(BaseController $ctrl, $u = null, $type = 'sys', $port = null){
        if ($port)
            $port = ":" . $port;
        return igk_io_baseuri() . $port . "/!@{$type}//{$ctrl->Name}/{$u}";
    }
    /**
     * get system cache uri
     * @return (string|bool)[]  uri and zip flag 
     */
    public static function CacheUri(){
        $o = strtolower(igk_environment()->keyname()).":";
        $uri = $o.igk_server()->REQUEST_URI;
        $zip = igk_server()->accepts(["gzip"]);      
        if ($zip){
            $uri .= "_zip";
        }
        return [$uri, $zip];
    }
}