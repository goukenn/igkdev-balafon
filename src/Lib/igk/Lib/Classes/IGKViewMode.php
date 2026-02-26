<?php
// @file: IGKViewMode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Igkview mode.
*/
final class IGKViewMode extends IGKObject{

    /**
    * Constant: administrator.
    * @var mixed
    */
    const ADMINISTRATOR=6;

    /**
    * Constant: visitor.
    * @var mixed
    */
    const VISITOR=1;

    /**
    * Constant: webmaster.
    * @var mixed
    */
    const WEBMASTER=2;
    /**
     * 
     * @return string 
     */

    public static function GetSystemViewMode(): string{
        $m=igk_app()->getViewMode();
        $t=array();
        foreach(igk_get_class_constants(__CLASS__) as $k=>$v){
            if(($m& $v) == $v)
                $t[]=$k;
        }
        return implode(",", array_filter($t));
    }
     /**
    * 
    * @param mixed $view
    */

    public static function IsSupportViewMode($view){
        return ((igk_app()->getViewMode() & $view) == $view);
    }
    /**
     * 
     * @param mixed $mode 
     * @return bool 
     */

    public static function IsViewMode($mode): bool{
        return self::IsSupportViewMode($mode);
    }
    /**
     * 
     * @return bool 
     */

    public static function IsWebMaster(): bool{
        return self::IsSupportViewMode(IGKViewMode::WEBMASTER);
    }
}