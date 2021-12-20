<?php
// @file: IGKViewMode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKViewMode extends IGKObject{
    const ADMINISTRATOR=6;
    const VISITOR=1;
    const WEBMASTER=2;
    ///<summary></summary>
    public static function GetSystemViewMode(){
        $m=igk_app()->getViewMode();
        $t=array();
        foreach(igk_get_class_constants(__CLASS__) as $k=>$v){
            if(($m& $v) == $v)
                $t[]=$k;
        }
        return implode(",", array_filter($t));
    }

     ///<summary></summary>
    ///<param name="view"></param>
    /**
    * 
    * @param mixed $view
    */
    public static function IsSupportViewMode($view){
        return ((igk_app()->getViewMode() & $view) == $view);
    }
    ///<summary></summary>
    ///<param name="mode"></param>
    /**
     * 
     * @param mixed $mode 
     * @return bool 
     */
    public static function IsViewMode($mode){
        return self::IsSupportViewMode($mode);
    }
    ///<summary></summary>
    /**
     * 
     * @return bool 
     */
    public static function IsWebMaster(){
        return self::IsSupportViewMode(IGKViewMode::WEBMASTER);
    }
}
