<?php
// @author : C.A.D. BONDJE DOUE
// @desc: command utility
// 
namespace igk\System\Console\Commands;

use IGK\System\Console\Logger;

///<summary>command utility</summary>
/**
 * command utility 
 * @package igk\System\Console\Commands
 */
class Utility{
    public static function TouchFileCallback($content= ""){
        return function ($file)use($content){
            return igk_io_w2file($file, $content);
        };
    }
    public static function BindFiles($command, $bind, $is_force=false){
        foreach($bind as $n=>$c){
            if ($is_force || !file_exists($n)){
                $c($n, $command);
                Logger::info("generate : ".$n);
            }
        }
    }
}