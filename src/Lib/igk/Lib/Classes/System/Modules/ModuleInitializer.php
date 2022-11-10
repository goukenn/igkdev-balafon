<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ModuleInitializer.php
// @date: 20220829 09:55:54
// @desc: 

namespace IGK\System\Modules;

/**
 * initializer modules
 * @package IGK\System\Modules
 */
class ModuleInitializer{
    protected $m_modules = [];

    public function reset(){
        $this->m_modules = [];
    }
    public function get(string $path){
        return igk_getv($this->m_modules, $this->_get_key($path));
    }
    public function register($path, $module){
        $this->m_modules[$this->_get_key($path)] = $module;
    }
    protected function _get_key(string $path){
        return "sys://modules/" . strtolower(str_replace("/", ".", igk_uri($path)));
    }
}
