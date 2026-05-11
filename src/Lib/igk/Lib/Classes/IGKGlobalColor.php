<?php
// @file: IGKGlobalColor.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Igkglobal color.
*/
final class IGKGlobalColor{
    /**
    * Property: colors.
    * @var mixed
    */
    private $m_COLORS;
    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
    * .ctr
    * @return mixed
    */
    private function __construct(){
        $this->m_COLORS=array();
    }
    /**
    * Returns.
    * @param mixed $clname
    */
    public function Get($clname){
        return igk_getv($this->m_COLORS, $clname);
    }
    /**
    * Returns Instance.
    */
    public static function getInstance(){
        if(self::$sm_instance === null){
            self::$sm_instance=new IGKGlobalColor();
        }
        return self::$sm_instance;
    }
    /**
    * Returns true if Global Color.
    * @param mixed $clname
    */
    public static function IsGlobalColor($clname){
        $i=self::getInstance();
        return isset($i->m_COLORS[$clname]);
    }
    /**
    * auto generate doc.
    * @param string $clname
    * @param string $value
    * @return void
    */
    public static function SetGlobalColor(string $clname, string $value){
        $i=self::getInstance();
        $i->m_COLORS[$clname]=$value;
    }
    /**
    * Returns Globals.
    */
    public function getGlobals(){
        return $this->m_COLORS;
    }
}