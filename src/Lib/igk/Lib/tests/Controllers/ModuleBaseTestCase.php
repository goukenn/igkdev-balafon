<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ModuleBaseTestCase.php
// @date: 20220803 13:48:54
// @desc: 
 
namespace IGK\Tests\Controllers;

use IGK\Tests\BaseTestCase;

/**
 * base module base test case
 * @package IGK\Tests\Controllers
 */
abstract class ModuleBaseTestCase extends BaseTestCase{
    protected $controller;

    public function __construct(){
        parent::__construct();
        if ($c = igk_getv($_ENV, "IGK_TEST_MODULE")){
            $this->controller = igk_getctrl($c);
        }else{
            $this->controller = $this->getModule();
            $this->controller->auto_load_register();
        }
    }
    /**
     * get module 
     * @return mixed 
     */
    abstract protected function getModule();
}