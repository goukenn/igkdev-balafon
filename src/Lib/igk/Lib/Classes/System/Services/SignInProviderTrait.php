<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SignInProviderBase.php
// @date: 20220607 19:41:35
// @desc: autor base


namespace IGK\System\Services;

use IGK\Helper\ViewHelper;
use IGKEvents;

trait SignInProviderTrait{
    protected function registerEvents($uinfo){
        igk_reg_hook(IGKEvents::HOOK_USER_ADDED, function($e)use($uinfo){                     
            $this->userAdded($e->args[0], $uinfo);
            ViewHelper::CurrentCtrl()::login($e->args[0],null, $this->navigate_onlogin);
        });
        igk_reg_hook(IGKEvents::HOOK_USER_EXISTS, function($e)use($uinfo){     
            $this->userExists($e->args[0], $uinfo); 
            ViewHelper::CurrentCtrl()::login($e->args[0],null,  $this->navigate_onlogin);
        });
    }
}