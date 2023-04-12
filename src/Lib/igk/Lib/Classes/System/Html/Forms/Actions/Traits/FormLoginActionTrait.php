<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormLoginActionTrait.php
// @date: 20221205 22:17:20
namespace IGK\System\Html\Forms\Actions\Traits;

use IGK\Helper\ViewHelper;
use IGK\System\Actions\Traits\ActionFormHandlerTrait;
use IGK\System\Html\Forms\FormHelper;
use IGK\System\Services\LoginServiceEvents;
use IGK\System\Services\SignProvider;
use function igk_resources_gets as __ ;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Actions\Traits
*/
trait FormLoginActionTrait{
    use ActionFormHandlerTrait;
    var $formLoginActionRememberMe = true;
    var $formLoginActionRegisterUri = "registerLogin";
    var $formLoginActionLogin = 'login';
    
    protected function form_login($form, $options = null)
    {
        $user = ViewHelper::CurrentCtrl()->getUser();
        if ($user){
            return;
        }
        if (!($ac=$form['action']) || ($ac=='.')){
            $form['action'] = $this->formLoginActionLogin ?? 'login';
        }
        

        $t = $form;
        $ctrl = ViewHelper::CurrentCtrl();
        $fname = ViewHelper::GetViewArgs('fname');
        $app_name = igk_getv($options, 'app_name');
        $this->registerUserActionAuthSocialUri = $ctrl->getAppUri($fname . "/connect");
        $form->div()->h1()->Content = __("Login");
        $form->cref();
        $form->confirm();
        $t->notifyHost(__FUNCTION__);
        $t->fields([
            "login" => ['required'=>true],
            "password" => ["type" => "password", 'required'=>true]
        ]);
        if ($this->formLoginActionRememberMe){
            $t->fields([
                "rememberme"=>["type"=>"checkbox", "label_text"=>__("Remember me")]
            ]);
        }
        if (SignProvider::IsRegistered()) {
            $t->div()->setClass("sep")->Content = __("--- OR --- ");
            $t->div()->setClass('igk-social-login-button-container')->yield(
                LoginServiceEvents::LoginWithSocialButton,
                [
                    "controller" => $ctrl,
                    "redirect_uri" => $this->registerUserActionAuthSocialUri,
                    "app_name" => $app_name
                ]
            );
        } 
        $t->actionbar(function($a)use($ctrl){
            $a['class'] = '+footer';
            $group = $a->button_group();
            $group->host(FormHelper::submit());
            $group->a($ctrl::uri($this->formLoginActionRegisterUri))
            ->setClass('igk-btn')
            ->Content = __("register");
            
            if ($this->registerUserActionForgotPasswordUri)
            $a->a($ctrl::uri($this->registerUserActionForgotPasswordUri))
                ->setClass('underline')
                ->Content = __("Forgot password ?");
        });
    }
}