<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ClassVersionLoaderTraitTest.php
// @date: 20220909 09:38:33
// @desc: 

use IGK\System\Traits\ClassFileVersionLoaderTrait;
use IGK\Test;

use IGK\Tests\BaseTestCase;
use IGK\Tests\System\Compilers\Dummy;

class ClassVersionLoaderTraitTest extends BaseTestCase{
    public function test_load_diff_version(){
        $loader = new DummyClassVersionLoader;
        $loader->registerClass("/temp/dummy.7.3.php", "dummy", "7");
        $loader->registerClass("/temp/dummy.7.4.php", "dummy", "7.4");
        $loader->registerClass("/temp/dummy.php", "dummy", "");
      
        $this->assertEquals(
            "/temp/dummy.7.3.php",
            $loader->getRegisterClass("dummy", "7.3-malachi"),
            "not retrieve files"
        );
        $this->assertEquals(
            "/temp/dummy.php",
            $loader->getRegisterClass("dummy"),
            "not retrieve files"
        );
        $this->assertEquals(
            "/temp/dummy.7.4.php",
            $loader->getRegisterClass("dummy", "7.4-malachi"),
            "not retrieve files"
        );
    }
}

class DummyClassVersionLoader{
    use ClassFileVersionLoaderTrait;
}
