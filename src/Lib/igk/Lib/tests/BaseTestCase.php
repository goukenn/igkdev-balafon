<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BaseTestCase.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase{
    // call before all launching test - and output is consider in return of the output string test.
    protected function setUp():void{ 
        igk_server()->prepareServerInfo();
    }
    protected function CreateController($classname){
        return Utils::CreateController($classname);
    }
    public function wln(){
        ob_start();
        $fc = "igk_wln";
        $args = func_get_args();
        $fc(...$args);
        $w = ob_get_clean();
        fwrite(STDOUT, $w);
    }
}