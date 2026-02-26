<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ManageControllerTest.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\Test\Controller;

use IGK\Tests\BaseTestCase;
use IGK\IUriActionRegistrableController;

/**
* auto generate doc.
* @package IGK\Test\Controller
*/
class ManageControllerTest extends BaseTestCase{

    /**
    * auto generate doc.
    */
    function test_manage_subdomain(){
        
        $data = igk_app()->getControllerManager()->getUserControllers(function ($v) {
            return $v instanceof IUriActionRegistrableController;
            });
        $this->assertTrue(is_array($data));

    }
}

