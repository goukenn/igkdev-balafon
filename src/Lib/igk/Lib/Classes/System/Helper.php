<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Helper.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\System;

///<summary> core Helper class </summary>
/**
 * 
 * @package IGK\System
 */
abstract class Helper{
    static $_init; 
    public static function PolyfillTrait($file, ?callable $handling=null){
        $n = basename($file);        
        if (version_compare(PHP_VERSION, "8", ">="))
            $n.=".8.pinc";
        else 
            $n.=".pinc";
        $cf = dirname($file)."/".$n;
        if (file_exists($cf)){
            include($cf);
            return true;
        }
        return false;
    }
    public static function __init(){
        if (self::$_init)
            return;
        self::$_init = true;

        spl_autoload_register(function($n, $g=null){
            $fn = "";
            if (strpos($n, "IGK\\")==0){
                $fn = str_replace("\\", "/", IGK_LIB_DIR."/Lib/Classes/".substr($n, 4));
                if (self::PolyfillTrait($fn)){
                    return 1;
                }
            }
        });
    }
}
Helper::__init();