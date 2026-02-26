<?php
// @file: IGKOb.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
/**
 * helper buffer operation 
 * @package 
 */
final class IGKOb{

    /**
    * Clean and start.
    */
    public static function CleanAndStart(){
        while(ob_get_level() > 0){
            ob_end_clean();
        }
        ob_start();
    }
    /**
     * clear only the last buffer
     */

    public static function Clear(){
        if(ob_get_level() > 0){
            ob_end_clean();
        } 
    }
    /**
     * get the only level
     * @return string|false 
     */

    public static function Content(){
        return ob_get_contents();
    }
    /**
     * start new obj data 
     */

    public static function Start(){
        ob_start();
    }
}