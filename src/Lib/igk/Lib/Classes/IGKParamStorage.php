<?php
// @file: IGKParamStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

class IGKParamStorage extends IGKObject implements IIGKParamHostService{
    private $m_params;
    public function __construct(){
        $this->m_params=array();
    }
    public function getParam($key, $default=null){
        return igk_getv($this->m_params, $key, $default);
    }
    public function getParamKeys(){
        return array_keys($this->m_params);
    }
    public function resetParam(){
        $this->m_params=array();
    }
    public function setParam($key, $value){
        $this->m_params[$key]=$value;
    }
    public function unsetParam($key){
        unset($this->m_params[$key]);
    }
}
