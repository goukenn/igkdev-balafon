<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleBaseTestCase.php
// @date: 20220803 13:48:54
// @desc: 
 
namespace IGK\Tests\Controllers;

use IGK\Controllers\ApplicationModuleController;
use IGK\Helper\ApplicationModuleHelper;
use IGK\Tests\BaseTestCase;

/**
 * base module base test case
 * @package IGK\Tests\Controllers
 */
abstract class ModuleBaseTestCase extends BaseTestCase{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $controller;

    /**
    * auto generate doc.
    * @return void
    */
    public static function setUpBeforeClass(): void{
        // gk_require_module(__NAMESPACE__); 
    }

    /**
    * auto generate doc.
    * @return void
    */
    public function setUp():void{
        parent::setUp();
        if ($c = igk_getv($_ENV, "IGK_TEST_MODULE")){
            $this->controller = igk_getctrl($c);
        }else{
            $this->controller = $this->getModule() ?? igk_die("module not found ".static::class);
            // $this->controller->auto_load_register();
            // $this->controller->register_autoload();
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
 