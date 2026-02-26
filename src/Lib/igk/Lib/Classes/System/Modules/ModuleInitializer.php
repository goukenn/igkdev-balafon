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

    /**
    * Property: modules.
    * @var mixed
    */
    protected $m_modules = [];

    /**
    * Resets.
    */
    public function reset(){
        $this->m_modules = [];
    }

    /**
    * Returns.
    * @param string $path
    */
    public function get(string $path){
        return igk_getv($this->m_modules, $this->_get_key($path));
    }

    /**
    * Registers.
    * @param mixed $path
    * @param mixed $module
    */
    public function register($path, $module){
        $this->m_modules[$this->_get_key($path)] = $module;
    }

    /**
    * Get key.
    * @param string $path
    */
    protected function _get_key(string $path){
        return "sys://modules/" . strtolower(str_replace("/", ".", igk_uri($path)));
    }
}