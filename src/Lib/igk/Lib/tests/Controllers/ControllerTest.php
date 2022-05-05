<?php


namespace IGK\Test\Lib\Classes\System\Html;

use IGK\Tests\Controllers\TestApplicationController;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase{
    public function test_css_sys(){

        $p = TestApplicationController::ctrl()->getPrimaryCssFile();
        $this->assertEquals( $p ,
        ".__/Styles/default.pcss", 
        "test file not found");        
    }

}