<?php
// @file: IGKGlobalColor.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKGlobalColor{
    private $COLORS;
    private static $sm_instance;
    ///<summary></summary>
    private function __construct(){
        $this->COLORS=array();
    }
    ///<summary></summary>
    ///<param name="clname"></param>
    public function Get($clname){
        return igk_getv($this->COLORS, $clname);
    }
    ///<summary></summary>
    public static function getInstance(){
        if(self::$sm_instance === null){
            self::$sm_instance=new IGKGlobalColor();
        }
        return self::$sm_instance;
    }
    ///<summary></summary>
    ///<param name="clname"></param>
    public static function IsGlobalColor($clname){
        $i=self::getInstance();
        return isset($i->COLORS[$clname]);
    }
    ///<summary></summary>
    ///<param name="clname"></param>
    ///<param name="value"></param>
    public static function SetGlobalColor($clname, $value){
        $i=self::getInstance();
        $i->COLORS[$clname]=$value;
    }
}
