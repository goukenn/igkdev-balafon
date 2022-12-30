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
        $t->actionbar(FormHelper::submit());
        if (SignProvider::IsRegistered()) {
            $t->div()->setClass("sep")->Content = __("--- OR --- ");
            $t->div()->setClass('igk-social-login-button-container')->yield(
                LoginServiceEvents::LoginWithSocialButton,
                [
                    "controller" => $ctrl,
                    "redirect_uri" => $this->registerUserActionAuthSocialUri,
                    "app_name" => "tonerafrika"
                ]
            );
        }
        $t->div()->actionbar(function ($a) {
            $ctrl = ViewHelper::CurrentCtrl();
            $a['class'] = '+footer';
            $a->a($ctrl::uri($this->formLoginActionRegisterUri))->Content = __("register");
            $a->space();
            if ($this->registerUserActionForgotPasswordUri)
            $a->a($ctrl::uri($this->registerUserActionForgotPasswordUri))
                ->setClass('underline')
                ->Content = __("Forgot password ?");
        });
    }
}