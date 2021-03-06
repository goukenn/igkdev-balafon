<?php

namespace IGK\System;

use IGKException;

/**
 * manage uri
 * @package IGK\System
 */
class Uri{
    const TEMP_ENV_KEY = "sys://temp_uri";
    /**
     * build a query arg query
     * @param mixed $uri 
     * @param null|array $append_args 
     * @param null|array $exclude_query 
     * @return string 
     * @throws IGKException 
     */
    public static function BuildUri($uri, ?array $append_args=[], ?array $exclude_query = null, $append=true)
    {
        $q = parse_url($uri);
        if ($c = igk_getv($q, "query", null)){
            parse_str($c, $ctab);
            $c = $ctab; 
        } else {
            $c = [];
        }

        if ($append_args){
            $c = array_merge($c, $append_args);
        }
        if ($exclude_query){
            foreach($exclude_query as $k){
                unset($c[$k]);
            }
        }
        $cpath = igk_getv($q, "path");
        $query = http_build_query($c);
        if (!empty($query)){
            $cpath.="?".$query;
            if ($append){
                $cpath.="&";
            }
        }
        else{
            if ($append){
                $cpath.="?";
            }
        }
        return $cpath;
    }

    public static function get(string $name, $default=null){
        return igk_environment()->getArray(self::TEMP_ENV_KEY, $name, $default);
    }
    public static function register(string $name, string $uri ){
        igk_environment()->setArray(self::TEMP_ENV_KEY, $name, $uri);
    }
}