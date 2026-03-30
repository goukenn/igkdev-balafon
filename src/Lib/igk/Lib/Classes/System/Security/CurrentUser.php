<?php
// @author: C.A.D. BONDJE DOUE
// @file: CurrentUser.php
// @date: 20260329 20:39:44
namespace IGK\System\Security;

use IGK\Controllers\BaseController;
use IGK\Models\Users;
use IGK\System\IInjectable;

/**
* retrieve the current user 
* @package IGK\System\Security
* @author C.A.D. BONDJE DOUE
*/
class CurrentUser implements IInjectable{
    private $m_user;

    public function model(): Users{
        return $this->m_user;
    }
    public function __construct(Users $user)
    {
        $this->m_user = $user;
    }
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->m_user, $name], $arguments);
    }
    public function __get($name){
        return $this->m_user->{$name};
    }
    
}