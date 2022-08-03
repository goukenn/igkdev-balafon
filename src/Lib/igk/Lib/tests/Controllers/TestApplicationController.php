<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TestApplicationController.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\Controllers;

use IGK\Controllers\ApplicationController; 

class TestApplicationController extends ApplicationController{
    
    private function _getTestDeclaredDir(){
        return $this->getEnvParam("DeclaredDir");
    }
    public function getDeclaredDir():string{
        return (string)$this->_getTestDeclaredDir();
    }
    public function getDeclaredFileName(){
        return $this->_getTestDeclaredDir()."/TestController.php"; 
    }
    public function getBasicUriPattern(){
        return "^/unittest";
    }
    protected function IsEntryController(){ 
        return false;
    }
    protected function getPrimaryCssFile()
    {
        return ".__/Styles/default.pcss";
    }
}