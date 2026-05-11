<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TestApplicationController.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\Tests\Controllers;
use IGK\Controllers\ApplicationController;

/**
* Test application controller.
* @package IGK\Tests\Controllers
*/
class TestApplicationController extends ApplicationController{
    /**
    * auto generate doc.
    * @return mixed
    */
    private function _getTestDeclaredDir(){
        return $this->getEnvParam("DeclaredDir");
    }
    /**
    * Returns Declared Dir.
    * @return string
    */
    public function getDeclaredDir():string{
        return (string)$this->_getTestDeclaredDir();
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
    /**
    * Returns true if Entry Controller.
    */
    protected function IsEntryController(){ 
        return false;
    }
    /**
    * Returns Primary Css File.
    */
    protected function getPrimaryCssFile()
    {
        return ".__/Styles/default.pcss";
    }
}