<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TestController.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\Controllers;

use IGK\Controllers\BaseController; 

class TestController extends BaseController{
    
    /**
     * 
     * @return string
     */
    private function _getTestDeclaredDir(){
        return $this->getEnvParam("DeclaredDir");
    }
    public function getDeclaredDir():string{
        return $this->_getTestDeclaredDir();
    }
    public function getDeclaredFileName(){
        return $this->_getTestDeclaredDir()."/TestController.php"; 
    }
    public function getBasicUriPattern(){
        return "^/unittest";
    }
}