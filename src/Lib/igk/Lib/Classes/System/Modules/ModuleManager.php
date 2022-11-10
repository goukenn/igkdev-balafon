<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ModuleManager.php
// @date: 20220829 09:41:42
// @desc: 

namespace IGK\System\Modules;

use IGK\Controllers\ApplicationModuleController;

/**
 * manager module
 * 
 */
class ModuleManager{
    /**
     * @var array
     */
    private $m_modules;
    /**
     * 
     * @var ModuleInitializer
     */
    private $m_init;

    public function __construct()
    {
        $this->m_modules =  & igk_environment()->require_modules();
        $this->m_init = $this->_createModuleInitializer();
    }
    /**
     * reset the loaded module and return previous backup
     * @return array 
     */
    public function reset(){
        $bck = array_combine(array_keys($this->m_modules), array_values($this->m_modules));
        $this->m_modules = [];
        $this->m_init->reset();
        return $bck;
    }
    public function restore(array $tab){
        $this->m_modules = $tab;
        foreach ($tab as $value) {
            if ($value instanceof ApplicationModuleController)
            {
                $path = $value->getName();
                $this->m_init->register($path, $value);
            }
        }
    }
    /**
     * get reference to modules list
     * @return array 
     */
    public function & get(){
        return $this->m_modules;
    }
    public function count(){
        return igk_count($this->m_modules);
    }
    /**
     * return initialized modules 
     * @return ModuleInitializer 
     */
    public function init(){
        if (is_null($this->m_init)){
            die("initializer not created");
        }
        return $this->m_init;
    }
    /**
     * create module inistializer
     * @return ModuleInitializer 
     */
    protected function _createModuleInitializer(){
        return new ModuleInitializer;
    }
}
