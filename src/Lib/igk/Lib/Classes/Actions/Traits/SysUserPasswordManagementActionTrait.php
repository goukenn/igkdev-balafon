<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SysUserPasswordManagementActionTrait.php
// @date: 20221117 22:55:33
// @desc: 
namespace IGK\Actions\Traits;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\Models\Users;
use IGK\System\Html\Forms\FormHelper;
use IGKValidator;

trait SysUserPasswordManagementActionTrait
{
    use RedirectHelperActionTrait;

    public function resetPassword_get(BaseController $ctrl)
    {
        if (!($token = igk_getr('token'))) {
            return $this->error('token is empty');
        }
        $ctrl = $this->getController();
        $current_user = ActionHelper::CurrentActionUserModel($this);

        if ($current_user){
            // + | --------------------------------------------------------------------
            // + | ALREADY CONNECTER - with an account
            // + |
            $this->warning('already connected');
            $this->redirectTo(null);
            return $current_user;
        } 

        if ($token) {
            // activate first
            $linkLoken = ActionHelper::GetAliveToken($token);
            if ($linkLoken) {
                $rui = ActionHelper::ActivateUser($ctrl, $token, $linkLoken);
                $user = Users::Get('clGuid', $linkLoken->regLinkUserGuid);
                if ($rui) {
                    //
                    igk_ilog('user get activated');
                    return [
                        'message' => 'new account',
                        'user' => $user,
                    ];
                } else {
                    $linkLoken->regLinkAlive = 0;
                    $linkLoken->regLinkActivate = 'NOW()';
                    $linkLoken->save();
                }
            }
            else {

            }
            igk_wln_e(__FILE__.":".__LINE__, $linkLoken.'');
        }
    }
    public function resetPassword_post()
    {
        $u = ActionHelper::CurrentActionUserModel($this);
        if (!$u) {
            return false;
        }
        $password = igk_getr('password');
        $repassword = igk_getr('repassword');
        $not = igk_notifyctrl($this->registerUserActionNoticationName);
        if($r = ActionHelper::ChangePassword($u, $password, $repassword)){
            return $r;
        } else {
            
            return false;
        }
       
    }

    /**
     * protected form action 
     * @param mixed $t 
     * @return void 
     */
    protected function form_reset_password($a)
    {   
        
        
        $a->h2()->Content = __("Reset password"); 
        $a->notifyhost($this->notifyActionName, true); //->setAutohide(true);
        $a->div()->Content  = ""; 
        $a->fields([
            "password" => ['type' => 'password', 'required' => 1, 'placeholder' => __('password')],
            "repassword" => ['type' => 'password', 'required' => 1, 'placeholder' => __('confirm password')]
        ]);
        $a->actionbar(FormHelper::submit());
    }
}
