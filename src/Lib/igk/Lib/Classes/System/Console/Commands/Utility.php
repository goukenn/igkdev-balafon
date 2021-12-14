<?php
// @author : C.A.D. BONDJE DOUE
// @desc: command utility
// 
namespace igk\System\Console\Commands;

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
}