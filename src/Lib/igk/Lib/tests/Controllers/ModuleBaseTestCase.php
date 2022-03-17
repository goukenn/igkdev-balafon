<?php 
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
        }
    }
}