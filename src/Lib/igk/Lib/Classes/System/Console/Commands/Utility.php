<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Utility.php
// @date: 20220803 13:48:57
// @desc: 

// @author : C.A.D. BONDJE DOUE
// @desc: command utility
// 
namespace igk\System\Console\Commands;

use Closure;
use IGK\System\Console\Logger;

///<summary>command utility</summary>
/**
 * command utility 
 * @package igk\System\Console\Commands
 */
abstract class Utility{
    /**
     * touch and override 
     * @param string $content 
     * @param bool $override 
     * @return Closure 
     */
    public static function TouchFileCallback($content= "", bool $override= true){
        return function ($file)use($content, $override){
            return igk_io_w2file($file, $content, $override);
        };
    }
    /**
     * bind files 
     * @param mixed $command 
     * @param mixed $bind 
     * @param bool $is_force 
     * @return void 
     */
    public static function BindFiles($command, $bind, $is_force=false){
        foreach($bind as $n=>$c){
            if ($is_force || !file_exists($n)){
                $c($n, $command);
                Logger::info("generate : ".$n);
            }
        }
    }
}