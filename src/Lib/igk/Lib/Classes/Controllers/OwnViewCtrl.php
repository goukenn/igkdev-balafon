<?php
namespace IGK\Controllers;

use IGKObject;

///<summary>represent a own view controller objet.</summary>
/**
* represent a own view controller objet.
*/
final class OwnViewCtrl extends IGKObject  {
    private $m_ctrls;
    static $sm_instance;
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        $this->m_ctrls=array();
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
    * 
    * @param mixed $ctrl
    */
    public static function Contains($ctrl){
        $i=self::getInstance();
        if($ctrl && ($n=strtolower($ctrl->getName())) && isset($i->m_ctrls[$n])){
            return true;
        }
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function getInstance(){
        if(self::$sm_instance == null){
            self::$sm_instance=new static();
        }
        return self::$sm_instance;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function GetList(){
        $i=self::getInstance();
        return $i->m_ctrls;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="handleevent" default="1"></param>
    /**
    * 
    * @param mixed $ctrl
    * @param mixed $handleevent the default value is 1
    */
    public static function RegViewCtrl($ctrl, $handleevent=1){
        $i=self::getInstance();
        if($i->m_ctrls == null){
            $i->m_ctrls=array();
        }
        $n=strtolower($ctrl->getName());
        if(!isset($i->m_ctrls[$n])){
            $i->m_ctrls[$n]=$n;
            if($handleevent){
                igk_reg_hook(IGK_FORCEVIEW_EVENT, array($ctrl, "View"));
            }
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __serialize(){
        return null;
    }
    ///<summary></summary>
    ///<param name="s"></param>
    /**
    * 
    * @param mixed $s
    */
    public function __unserialize($s){}
}