<?php
// @author: C.A.D. BONDJE DOUE
// @filename: UriHelper.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGKException;

abstract class UriHelper{
    const URI_SYS_REPLACE = "base|base_config";
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
        return igk_io_baseuri() . $port . "/!@{$type}//{$ctrl->getName()}/{$u}";
        //return igk_io_baseuri() . $port . "/{$ctrl->getName()}/{$u}";
    }   
    public static function UriSysReplace(string $uri){
        $v_regex = "/%(?P<name>(".self::URI_SYS_REPLACE."))%/i";
        return preg_replace_callback( $v_regex, function($m){
            switch($m["name"]){
                case "base":
                return igk_io_baseuri();
                case "base_config":
                return igk_io_baseuri()."/Configs/";
            }
            return $m;
        }
        , $uri);
    }
    /**
     * return the query
     * @param string $uri 
     * @return array
     * @throws IGKException 
     */
    public static function GetQueryTab(string $uri){
        $d = parse_url($uri);
        parse_str(igk_getv($d, "query", ""), $d);
        return $d;
    }
}