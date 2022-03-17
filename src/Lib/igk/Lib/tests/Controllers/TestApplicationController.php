<?php
namespace IGK\Tests\Controllers;

use IGK\Controllers\ApplicationController; 

class TestApplicationController extends ApplicationController{
    
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
    protected function IsEntryController(){ 
        return false;
    }
}