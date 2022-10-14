<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CompileTestController.php
// @date: 20221013 14:52:41
// @desc: 
namespace IGK\Tests\System\Compilers;
use IGK\Tests\Controllers\TestController;

class CompileTestController extends TestController
{
    var $entryDir;
    public function getArticlesDir()
    {
        return $this->entryDir . "/Articles";
    }
    public function getDeclaredDir(): string
    {
        return $this->entryDir;
    }
    public function getAppUri(?string $m = null): string
    {
        return "testuri://" . $m;
    }
}

