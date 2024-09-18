<?php
// @file: IGKOb.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
 * helper buffer operation 
 * @package 
 */
final class IGKOb{
    ///<summary></summary>
    public static function CleanAndStart(){
        while(ob_get_level() > 0){
            ob_end_clean();
        }
        ob_start();
    }
    ///<summary></summary>
    /**
     * clear only the last buffer
     */
    public static function Clear(){
        if(ob_get_level() > 0){
            ob_end_clean();
        } 
    }
    ///<summary></summary>
    /**
     * get the only level
     * @return string|false 
     */
    public static function Content(){
        return ob_get_contents();
    }
    ///<summary></summary>
    /**
     * start new obj data 
     */
    public static function Start(){
        ob_start();
    }
}
