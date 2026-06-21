<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitManager.php
// @date: 20221118 09:03:41
namespace IGK\Database;
use Exception;
use Error;
use IGK\Controllers\BaseController;
use IGK\Database\Helpers\DbInitManagement;  

 
/**
* auto generate doc.
* @package IGK\Database
*/
class DbInitManager{
    /**
    * Initializes.
    * @param BaseController $controller
    */
    public function init(BaseController $controller){
        // + | --------------------------------------------------------------------
        // + | init profiles
        // + |
        $this->initProfile($controller);
    }
    /**
    * auto generate doc.
    * @param BaseController $controller
    * @return void
    */
    protected function initProfile(BaseController $controller){
        DbInitManagement::InitControllerProfile($controller);
    }
    /**
    * auto generate doc.
    * @param string $name
    * @param ?BaseController $controller
    */
    protected function _registerGroupAndAuth(string $name, ?BaseController $controller){
        return DbInitManagement::RegisterGroupAndAuth($name, $controller);
    }
}