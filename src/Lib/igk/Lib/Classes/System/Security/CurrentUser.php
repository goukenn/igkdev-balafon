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
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    private $m_user;
    /**
    * auto generate doc.
    * @return Users
    */
    public function model(): Users{
        return $this->m_user;
    }
    /**
    * .ctr
    * @param Users $user
    * @return void
    */
    public function __construct(Users $user)
    {
        $this->m_user = $user;
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    * @return void
    */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->m_user, $name], $arguments);
    }
    /**
    * .destructor
    * @param mixed $name
    * @return void
    */
    public function __get($name){
        return $this->m_user->{$name};
    }
}