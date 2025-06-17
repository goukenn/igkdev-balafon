<?php

// init hooks

use IGK\Controllers\BaseController;
use IGK\Helper\Authorization;
use IGK\System\EntryClassResolution;

 igk_reg_hook(IGKEvents::HOOK_USER_ADDED, function($e){
    list($user, $ctrl) = igk_extract($e->args, 'user|ctrl');
    if ($ctrl && !BaseController::IsSysController(get_class($ctrl))){
        if (method_exists($ctrl,  EntryClassResolution::CTRL_METHOD_INIT_USER_FROM_SYSUSER)) {                   
            $ctrl->initUserFromSysUser($user);
        } else {
            if ($defautlRole = $ctrl::resolveClass(EntryClassResolution::Profiles)){
                if ($profile = $defautlRole::GetDefaultProfile()){
                    Authorization::BindUserToGroup($ctrl, $user, $profile);
                }
            }
            
        }
    }
});