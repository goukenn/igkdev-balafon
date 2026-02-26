<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitManager.php
// @date: 20221118 09:03:41
namespace IGK\Database;

use Exception;
use Error;
use IGK\Controllers\BaseController;
use IGK\Database\Helpers\DbInitManagement;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
use IGKException;

/**
* 
* @package IGK\Database
*/
class DbInitManager{

    /**
    * auto generate doc.
    * @param BaseController $controller
    */
    public function init(BaseController $controller){
        // + | --------------------------------------------------------------------
        // + | init profiles
        // + |
        $this->initProfile($controller);
    }
    /**
     * 
     * @param BaseController $controller 
     * @return void 
     * @throws Exception 
     * @throws Error 
     * @throws IGKException 
     */

    protected function initProfile(BaseController $controller){
        // igk_debug_wln('init controller profiles ... ');
        DbInitManagement::InitControllerProfile($controller);
    }
    /**
     * 
     * @param string $name 
     * @return (null|Groups|Authorizations)[] 
     */

    protected function _registerGroupAndAuth(string $name, ?BaseController $controller){
        return DbInitManagement::RegisterGroupAndAuth($name, $controller);
    }
}