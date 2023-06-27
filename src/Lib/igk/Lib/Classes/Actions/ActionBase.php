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
     * get current user model
     * @return null|IGK\Actions\Users 
     */
    public function getUser(): ?Users{
        $u = $this->getController()->getUser();
        if ($u instanceof IUserProfile){
            return $u->model();
        }
        return null;
    }
}