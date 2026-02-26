<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerExtensionTest.php
// @date: 20230309 12:46:00
namespace IGK\Tests\Controllers;

use IGK\Controllers\NotRegistrableControllerBase;
use IGK\System\Html\Css\CssUtils;
use IGK\Tests\BaseTestCase;

/**
* 
* @package IGK\Tests\Controllers
*/
class ControllerExtensionTest extends BaseTestCase{

    /**
    * auto generate doc.
    */
    public function test_bind_css_controller(){
        $ctrl = TestController::ctrl(); 
        igk_server()->REQUEST_URI = 'http://local.phpunit';
        //$n = $ctrl->getTargetNode();
        $v = CssUtils::GetControllerSelectorClassNameFromRegisterURI($ctrl, "dummy/info");
        
        $this->assertEquals('dummy dummy_info', $v, 'class name not maching');
    }
}

/**
* auto generate doc.
* @package IGK\Tests\Controllers
*/
class TestController extends NotRegistrableControllerBase{

}