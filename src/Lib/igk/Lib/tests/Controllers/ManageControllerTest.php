<?php

namespace IGK\Test\Controller;

use IGK\Tests\BaseTestCase;
use IIGKUriActionRegistrableController;

class ManageControllerTest extends BaseTestCase{ 
    function test_manage_subdomain(){
        
        $data = igk_app()->getControllerManager()->getUserControllers(function ($v) {
            return $v instanceof IIGKUriActionRegistrableController;
            });
        $this->assertTrue(is_array($data));

    }
}

