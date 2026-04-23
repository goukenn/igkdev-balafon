<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CoreControllerTest.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\Tests\Controllers;
use IGK\Helper\IO;

/**
* Core controller test.
* @package IGK\Tests\Controllers
*/
class CoreControllerTest extends ControllerBaseTestCase
{
    /**
    * Path to dir.
    * @var mixed
    */
    static $sm_dir;
    /**
    * Sets up the test environment before each test.
    * @return void
    */
    public function setUp() : void{
        $this->controller =  TestController::ctrl();
        parent::setUp();
        $this->controller::setEnvParam("DeclaredDir", self::$sm_dir); 
    }
    /**
    * Sets up shared resources before all tests.
    * @return void
    */
    public static function setUpBeforeClass(): void
    {
        $sdir = sys_get_temp_dir()."/testController";     
        IO::CreateDir($sdir."/Views");
        self::$sm_dir = $sdir;
    }
    /**
    * Tears down shared resources after all tests.
    * @return void
    */
    public static function tearDownAfterClass(): void
    {
        if (self::$sm_dir){
            IO::RmDir(self::$sm_dir);
            self::$sm_dir = null;
        }
    }
    /**
    * Tests get view file name.
    */
    public function test_get_view_file_name()
    {
        $this->assertEquals(
            $this->controller->declaredDir . "/Views/default.phtml",
            $this->controller->getViewFile("default.phtml", 0)
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
    /**
    * Tests view args.
    */
    public function test_view_args(){    
        $p = [];
        $def = $this->controller->declaredDir . "/Views/default.phtml";
        if (igk_io_file_exists($def))
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
    /**
    * Tests default view args.
    */
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
    /**
    * Tests request action.
    */
    public function test_request_action(){
        $c = TestApplicationController::ctrl();
        $sdir = sys_get_temp_dir()."/appController";
        IO::CreateDir($sdir);
        $c::setEnvParam("DeclaredDir", $sdir); 
        $this->assertEquals(
            igk_io_baseuri()."/unittest/logintest",
            $c->getAppUri("logintest")
        ); 
        IO::RmDir($sdir);
    }
}