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
* auto generate doc.
*/
final class IGKGlobalColor{
    private $m_COLORS;
    private static $sm_instance;
    private function __construct(){
        $this->m_COLORS=array();
    }

    /**
    * auto generate doc.
    * @param mixed $clname
    */
    public function Get($clname){
        return igk_getv($this->m_COLORS, $clname);
    }

    /**
    * auto generate doc.
    */
    public static function getInstance(){
        if(self::$sm_instance === null){
            self::$sm_instance=new IGKGlobalColor();
        }
        return self::$sm_instance;
    }

    /**
    * auto generate doc.
    * @param mixed $clname
    */
    public static function IsGlobalColor($clname){
        $i=self::getInstance();
        return isset($i->m_COLORS[$clname]);
    }
    /**
     * 
     * @param string $clname 
     * @param string $value 
     * @return void 
     */
    public static function SetGlobalColor(string $clname, string $value){
        $i=self::getInstance();
        $i->m_COLORS[$clname]=$value;
    }

    /**
    * auto generate doc.
    */
    public function getGlobals(){
        return $this->m_COLORS;
    }
}