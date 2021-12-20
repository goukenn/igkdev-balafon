<?php
// @file: IGKCtrlInfo.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
 * controller's basic information
 * @package 
 */
final class IGKCtrlInfo extends IGKObject{
    private $m_SupportMultiple, $m_addNew, $m_childs, $m_name, $m_type, $m_typeCreated;
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="type"></param>
    public function __construct($name, $type){
        $this->m_childs=array();
        $this->m_name=$name;
        $this->m_type=$type;
        $this->m_addNew=true;
        $this->_initInfo();
    }
    ///<summary></summary>
    private function _initInfo(){
        foreach(get_declared_classes() as $v){
            if(igk_reflection_class_extends($v, $this->Type) && !igk_reflection_class_isabstract($v)){
                $this->m_childs[]=$v;
            }
        }
        if(method_exists($this->Type, "SupportMultiple")){
            $this->m_SupportMultiple=call_user_func_array(array($this->Type, "SupportMultiple"), array());
            $this->m_addNew=$this->m_SupportMultiple || (count($this->m_childs) < 1);
        }
    }
    ///<summary></summary>
    public function getCanAddNew(){
        return $this->m_addNew;
    }
    ///<summary></summary>
    public function getCreated(){
        return $this->m_typeCreated;
    }
    ///<summary></summary>
    public function getName(){
        return $this->m_name;
    }
    ///<summary></summary>
    public function getType(){
        return $this->m_type;
    }
}
