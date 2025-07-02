<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewModuleHelper.php
// @date: 20250306 09:17:46
namespace IGK\System\Controllers\Helper;

use Exception;
use IGK\Controllers\ApplicationModuleController;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
use IGK\System\Exceptions\EnvironmentArrayException;

/**
* use to manage controller in current view 
* @package IGK\System\Controllers\Helper
* @author C.A.D. BONDJE DOUE
*/
class ViewModuleHelper{
    private $m_modules;
    /**
     * .ctr
     * @param array &$modules 
     * @return void 
     */
    public function __construct(array & $modules ){
        $this->m_modules = $modules;
    }

    public function __debugInfo()
    {
        return [];
    }
    /**
     * 
     * @param string $module_name 
     * @return null|ApplicationModuleController 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public function require(string $module_name){
        return  igk_require_module($module_name);
    }
    /**
     * contains module
     * @param string $module_name 
     * @return bool 
     */
    public function contains(string $module_name){
        return key_exists($module_name, $this->m_modules);
    }
    /**
     * retrieve the module registrated name by name
     * @param string $module_name 
     * @return mixed 
     * @throws Exception 
     */
    public function get(string $module_name){
        return igk_getv($this->m_modules, $module_name);
    }
}