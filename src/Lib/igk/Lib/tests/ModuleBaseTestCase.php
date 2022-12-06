<?php

namespace IGK\Tests;


abstract class ModuleBaseTestCase extends BaseTestCase{
    public static function setUpBeforeClass(): void
    {
        $path = igk_get_module(__DIR__);
       $mod =  igk_require_module(igk\webpack::class);
       $mod->register_autoload();
    }
}