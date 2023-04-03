<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleBaseTestCase.php
// @date: 20220803 13:48:54
// @desc: 
 
namespace IGK\Tests\Controllers;

use IGK\Controllers\ApplicationModuleController;
use IGK\Helpers\ApplicationModuleHelper; 
use IGK\Tests\BaseTestCase;

/**
 * base module base test case
 * @package IGK\Tests\Controllers
 */
abstract class ModuleBaseTestCase extends BaseTestCase{
    protected $controller; 

    public static function setUpBeforeClass(): void{
        // gk_require_module(__NAMESPACE__); 
    } 
    public function __construct(){
        parent::__construct();
        if ($c = igk_getv($_ENV, "IGK_TEST_MODULE")){
            $this->controller = igk_getctrl($c);
        }else{
            $this->controller = $this->getModule() ?? igk_die("module not found ".static::class);
            $this->controller->auto_load_register();
        }
    }
    /**
     * get module 
     * @return mixed 
     */
    protected function getModule(): ?ApplicationModuleController{
        if ($dir = ApplicationModuleHelper::GetModuleNameFromTestClass(static::class)){        
            return  igk_require_module($dir);
        }
        return null;
    }
}
 