<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CompileTestController.php
// @date: 20221013 14:52:41
// @desc: 
namespace IGK\Tests\System\Compilers;
use IGK\Tests\Controllers\TestController;

/**
* auto generate doc.
* @package IGK\Tests\System\Compilers
*/
class CompileTestController extends TestController
{
    var $entryDir;

    /**
    * auto generate doc.
    */
    public function getArticlesDir()
    {
        return $this->entryDir . "/Articles";
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function getDeclaredDir(): string
    {
        return $this->entryDir;
    }

    /**
    * auto generate doc.
    * @param null|string $m
    * @return string
    */
    public function getAppUri(?string $m = null): string
    {
        return "testuri://" . $m;
    }

    /**
    * auto generate doc.
    * @return ?string
    */
    protected function getBaseDir():?string{
        return $this->entryDir;
    }
    
}

