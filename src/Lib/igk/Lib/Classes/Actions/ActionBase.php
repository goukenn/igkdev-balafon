<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionBase.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Actions;
use IGK\Models\Users;
use IGK\System\Database\IUserProfile;
use IGK\System\Http\Traits\DieRequestTrait;
use IGKActionBase;
use IGKUserInfo;
/**
* Action base.
* @package IGK\Actions
*/
abstract class ActionBase extends IGKActionBase{
    use DieRequestTrait;
    /**
     * get user model
     * @return null|Users 
     */
    public function getUser(): ?Users{
        if ($this->m_user){
            return $this->m_user;
        }
        $u = $this->getController()->getUser();
        if ($u instanceof IUserProfile){
            return $u->model();
        }
        if ($u instanceof Users){
            return $u;
        }
        if ($u instanceof IGKUserInfo){
            return $u->model();
        }
        return null;
    }
    /**
    * Returns debug information for var_dump.
    */
    public function __debugInfo()
    {
        return [];
    }
}