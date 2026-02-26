<?php

namespace IGK\Tests;

/**
* Module base test case.
* @package IGK\Tests
*/
abstract class ModuleBaseTestCase extends BaseTestCase{

    /**
    * Sets up shared resources before all tests.
    * @return void
    */
    public static function setUpBeforeClass(): void
    {
        $path = igk_get_module(__DIR__);
       $mod =  igk_require_module(igk\webpack::class);
       $mod->register_autoload();
    }
}