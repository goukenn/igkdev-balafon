<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionBase.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Actions;

use IGK\Models\Users;
use IGK\System\Database\IUserProfile;
use IGKActionBase;

abstract class ActionBase extends IGKActionBase{
    /**
     * get user model
     * @return null|Users 
     */
    public function getUser(): ?Users{
        if ($this->_user){
            return $this->_user;
        }
        $u = $this->getController()->getUser();
        if ($u instanceof IUserProfile){
            return $u->model();
        }
        return null;
    }
}