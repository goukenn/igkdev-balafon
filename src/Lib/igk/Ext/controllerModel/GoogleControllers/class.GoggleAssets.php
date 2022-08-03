<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.GoggleAssets.php
// @date: 20220803 13:48:59
// @desc: 


/**
 * google asset management
 * @package 
 */
class GoogleAssets{
    public static function Icon($name):callable{
        return function($n)use($name){
            $n->google_icon(strtolower(str_replace(" ", "_", $name)));
        };
    } 
}