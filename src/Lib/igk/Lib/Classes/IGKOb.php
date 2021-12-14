<?php
// @file: IGKOb.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKOb{
    ///<summary></summary>
    public static function CleanAndStart(){
        while(ob_get_level() > 0){
            ob_end_clean();
        }
        ob_start();
    }
    ///<summary></summary>
    public static function Clear(){
        ob_end_clean();
    }
    ///<summary></summary>
    public static function Content(){
        return ob_get_contents();
    }
    ///<summary></summary>
    public static function Start(){
        ob_start();
    }
}
