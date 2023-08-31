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
use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\Logger;

///<summary>command utility</summary>
/**
 * command utility 
 * @package igk\System\Console\Commands
 */
abstract class Utility{
    const OPTIONS_TAB_SPACE = AppCommand::OPTIONS_TAB_SPACE;
    public static function PrintCommand($opts, $color_one=App::AQUA, $color_two = App::GREEN  ){
        foreach($opts as $k=>$v){
            if (empty($v) && (strpos($k, '+')===0)){
                Logger::print(App::Gets($color_one, $k));     
                Logger::print('');
                continue;
            }
            Logger::print( App::Gets($color_two, $k). self::OPTIONS_TAB_SPACE. "{$v}". PHP_EOL); 
        }
    }
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
    public static function MakeBindFiles($command, $bind, $is_force=false){
        foreach($bind as $n=>$c){
            if ($is_force || !file_exists($n)){
                $c($n, $command);
                Logger::info("generate : ".$n);
            }
        }
    }
}