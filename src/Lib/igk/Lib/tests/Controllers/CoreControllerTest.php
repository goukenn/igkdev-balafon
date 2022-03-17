<?php

namespace IGK\Tests\Controllers;

use IGK\Helper\IO;

class CoreControllerTest extends ControllerBaseTestCase
{
    public function __construct()
    {
        $g = TestController::ctrl();
        parent::__construct($g);
    }
    public function setup(): void
    {
        $sdir = sys_get_temp_dir()."/testController";
        IO::CreateDir($sdir);
        $this->controller::setEnvParam("DeclaredDir", $sdir); 
    }
    public function test_get_view_file_name()
    {

        $this->assertEquals(
            $this->controller->declaredDir . "/Views/default.phtml",
            $this->controller->getViewFile("default", 0)
        );
        $p = [];
        $this->assertEquals(
            $this->controller->declaredDir . "/Views/default.phtml",
            $this->controller->getViewFile("home", 1, $p)
        );
        $this->assertEquals(
            ["home"],
            $p
        );
       
    }
    public function test_view_args(){    
        $p = [];
        $def = $this->controller->declaredDir . "/Views/default.phtml";
        if (file_exists($def))
            @unlink($def); 
        $this->assertEquals(
            $this->controller->declaredDir . "/Views/default.phtml",
            $this->controller->getViewFile("default/one/base/ok/", 1, $p)
        );
        $this->assertEquals(
            explode("/", "default/one/base/ok"),
            $p
        );
    }
    public function test_default_view_args(){    
        $p = [];
        $def = $this->controller->declaredDir . "/Views/default.phtml";
        igk_io_w2file($def, "<?php\n");
        $this->assertEquals(
            $this->controller->declaredDir . "/Views/default.phtml",
            $this->controller->getViewFile("default/one/base/ok/", 1, $p)
        );
        $this->assertEquals(
            ["one","base", "ok"],
            $p
        );
        @unlink($def);  
    }

    public function test_request_action(){
        $c = TestApplicationController::ctrl();
        $sdir = sys_get_temp_dir()."/appController";
        IO::CreateDir($sdir);
        $c::setEnvParam("DeclaredDir", $sdir); 

        igk_debug(1);
        $this->assertEquals(
            igk_io_baseuri()."/unittest/logintest",
            $c->getAppUri("logintest")
        );
        igk_debug(0);
        IO::RmDir($sdir);
    }
}
