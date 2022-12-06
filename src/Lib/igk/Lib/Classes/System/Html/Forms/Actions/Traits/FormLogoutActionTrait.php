<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FormLogoutActionTrait.php
// @date: 20220603 07:37:39
// @desc: logout action trait


namespace IGK\System\Html\Forms\Actions\Traits;

/**
 * 
 */
trait FormLogoutActionTrait{
    public function logout(){
        $this->ctrl->logout(1);
    }
}