<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CompileTestController.php
// @date: 20221013 14:52:41
// @desc: 
namespace IGK\Tests\System\Compilers;
use IGK\Tests\Controllers\TestController;

/**
* Compile test controller.
* @package IGK\Tests\System\Compilers
*/
class CompileTestController extends TestController
{

    /**
    * Path to entry dir.
    * @var mixed
    */
    var $entryDir;

    /**
    * Returns Articles Dir.
    */

    public function getArticlesDir()
    {
        return $this->entryDir . "/Articles";
    }

    /**
    * Returns Declared Dir.
    * @return string
    */

    public function getDeclaredDir(): string
    {
        return $this->entryDir;
    }

    /**
    * Returns App Uri.
    * @param null|string $m
    * @return string
    */

    public function getAppUri(?string $m = null): string
    {
        return "testuri://" . $m;
    }

    /**
    * Returns Base Dir.
    * @return ?string
    */

    protected function getBaseDir():?string{
        return $this->entryDir;
    }
    
}

