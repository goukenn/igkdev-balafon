<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SignInProviderBase.php
// @date: 20220607 19:41:35
// @desc: autor base
namespace IGK\System\Services;
use IGK\Helper\ViewHelper;
use IGKEvents;
trait SignInProviderTrait{
    /**
     * Registers user-added and user-exists event hooks for sign-in handling.
     *
     * @param mixed $uinfo The user information passed to event callbacks.
     * @return void
     */
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
