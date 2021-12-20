<?php
// @file: IGKParamStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

class IGKParamStorage extends IGKObject implements IIGKParamHostService{
    private $m_params;
    ///<summary></summary>
    public function __construct(){
        $this->m_params=array();
    }
    ///<summary>Parameter storage</summary>
    ///<param name="key"></param>
    ///<param name="default" default="null"></param>
    public function getParam($key, $default=null){
        return igk_getv($this->m_params, $key, $default);
    }
    ///<summary></summary>
    public function getParamKeys(){
        return array_keys($this->m_params);
    }
    ///<summary></summary>
    public function resetParam(){
        $this->m_params=array();
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    public function setParam($key, $value){
        $this->m_params[$key]=$value;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public function unsetParam($key){
        unset($this->m_params[$key]);
    }
}
