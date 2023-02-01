<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionUtilityTest.php
// @date: 20230120 08:13:09
namespace IGK\Tests\Actions;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\Actions
*/
class ActionUtilityTest extends BaseTestCase{
    public function test_expected_action(){

        $this->assertEquals(
            \test\dummy\controller\Actions\ProductsAction::class,
            ActionHelper::ExpectedAction(DummyActionController::ctrl(), "products/default")
        );


        $this->assertEquals(
            \test\dummy\controller\Actions\ProductsAction::class,
            ActionHelper::ExpectedAction(DummyActionController::ctrl(), "products")
        );
    }
}

class DummyActionController extends BaseController{
    public function getEntryNameSpace(){
        return \test\dummy\controller::class;
    }
}