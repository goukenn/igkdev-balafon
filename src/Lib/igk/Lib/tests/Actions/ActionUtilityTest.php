<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionUtilityTest.php
// @date: 20230120 08:13:09
namespace IGK\Tests\Actions;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\Tests\BaseTestCase;

/**
* 
* @package IGK\Tests\Actions
*/
class ActionUtilityTest extends BaseTestCase{

    /**
    * auto generate doc.
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
* auto generate doc.
* @package IGK\Tests\Actions
*/
class DummyActionController extends BaseController{

    /**
    * auto generate doc.
    */
    public function getEntryNameSpace(){
        return 'test\\dummy\\controller';
    }
}