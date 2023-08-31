<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerTest.php
// @date: 20220803 13:48:54
// @desc: 



namespace IGK\Test\Lib\Classes\System\Html;
 
use IGK\Controllers\RootControllerBase;
use IGK\Tests\Controllers\TestApplicationController;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase{
    public function test_css_sys(){

        $p = TestApplicationController::ctrl()->getPrimaryCssFile();
        $this->assertEquals( $p ,
        ".__/Styles/default.pcss", 
        "test file not found");        
    }
    public function test_is_include_controller(){
        $def = igk_getctrl(igk_configs()->default_controller, false);
        if ($def){
            $this->assertFalse(
                RootControllerBase::IsIncludedController($def),
                "Controller not included"
            );
        }
        else {
            $this->fail("no default controller");
        }

    }
}