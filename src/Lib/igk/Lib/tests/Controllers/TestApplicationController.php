<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TestApplicationController.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests\Controllers;

use IGK\Controllers\ApplicationController;

/**
* auto generate doc.
* @package IGK\Tests\Controllers
*/
class TestApplicationController extends ApplicationController{
    
    private function _getTestDeclaredDir(){
        return $this->getEnvParam("DeclaredDir");
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function getDeclaredDir():string{
        return (string)$this->_getTestDeclaredDir();
    }

    /**
    * auto generate doc.
    */
    public function getDeclaredFileName(){
        return $this->_getTestDeclaredDir()."/TestController.php"; 
    }

    /**
    * auto generate doc.
    */
    public function getBasicUriPattern(){
        return "^/unittest";
    }

    /**
    * auto generate doc.
    */
    protected function IsEntryController(){ 
        return false;
    }

    /**
    * auto generate doc.
    */
    protected function getPrimaryCssFile()
    {
        return ".__/Styles/default.pcss";
    }
}