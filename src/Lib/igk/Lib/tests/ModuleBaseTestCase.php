<?php

namespace IGK\Tests;

/**
* auto generate doc.
* @package IGK\Tests
*/
abstract class ModuleBaseTestCase extends BaseTestCase{

    /**
    * auto generate doc.
    * @return void
    */
    public static function setUpBeforeClass(): void
    {
        $path = igk_get_module(__DIR__);
       $mod =  igk_require_module(igk\webpack::class);
       $mod->register_autoload();
    }
}