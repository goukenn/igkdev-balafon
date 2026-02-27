<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionUtilityTest.php
// @date: 20230120 08:13:09
namespace IGK\Tests\Actions;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\Tests\BaseTestCase;

/**
* auto generate doc.
* @package IGK\Tests\Actions
*/
class ActionUtilityTest extends BaseTestCase{

    /**
    * Tests expected action.
    */
    public function test_expected_action(){
        $cl = 'test\\dummy\\controller\\Actions\\ProductsAction';
        $this->assertEquals(
            $cl,
            ActionHelper::ExpectedAction(DummyActionController::ctrl(), "products/default")
        );


        $this->assertEquals(
            $cl,
            ActionHelper::ExpectedAction(DummyActionController::ctrl(), "products")
        );
    }
}

/**
* Dummy action controller.
* @package IGK\Tests\Actions
*/
class DummyActionController extends BaseController{

    /**
    * Returns Entry Name Space.
    */
    public function getEntryNameSpace(){
        return 'test\\dummy\\controller';
    }
}