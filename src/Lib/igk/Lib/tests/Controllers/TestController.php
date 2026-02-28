<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TestController.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\Controllers;

use IGK\Controllers\BaseController;

/**
* Test controller.
* @package IGK\Tests\Controllers
*/
class TestController extends BaseController{

    /**
    * auto generate doc.
    * @return string
    */
    private function _getTestDeclaredDir(){
        return $this->getEnvParam("DeclaredDir");
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function getDeclaredDir():string{
        return $this->_getTestDeclaredDir();
    }

    /**
    * Returns Declared File Name.
    */
    public function getDeclaredFileName(){
        return $this->_getTestDeclaredDir()."/TestController.php"; 
    }

    /**
    * Returns Basic Uri Pattern.
    */
    public function getBasicUriPattern(){
        return "^/unittest";
    }
}