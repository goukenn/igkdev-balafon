<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterUserActionTrait.php
// @date: 20221114 18:28:41
namespace IGK\Actions\Traits;
 
use Exception;
use IGK\Actions\SystemUserActionContants; 
use IGK\Helper\ActionHelper;
use IGK\Helper\ArticleContentBindingHelper;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper; 
use IGK\Models\Users;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Forms\Actions\Traits\FormLoginActionTrait;
use IGK\System\Html\Forms\FormHelper;
use IGK\System\Net\Mail;
use IGK\System\Process\CronJobProcess;
use IGK\System\Services\LoginServiceEvents;
use IGK\System\Services\SignProvider;
use IGKEvents;
use IGKException;
use IGKValidator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException; 

use function igk_resources_gets as __;

///<summary></summary>
/**
 * 
 * @package IGK\Actions\Traits
 */
trait RegisterUserActionTrait
{
    use LoginLogoutActionTrait;
    use NotifyActionTrait;
    use SysUserPasswordManagementActionTrait;
    use FormLoginActionTrait;

    var $registerOptions = [];
    var $registerController = null;
    var $noticationName;
    var $registerServiceNotifyName = 'register';
    var $registerUserActionRegistrationConfirmUri = 'confirmRegistration';
    var $registerUserActionForgotPasswordUri = 'forgotPassword';
    var $registerUserActionAuthSocialUri = 'login';
    var $registerUserMailRegistrationArticle = 'Registration/mail_registration';
    var $registerUserMailForgotPasswordArticle = 'Registration/mail_forgotpassword';
    /**
     * subscribe user 
     * @return false|object|void 
     * @throws IGKException 
     * @throws BindingResolutionException  
     * @throws NotFoundExceptionInterface  
     * @throws ContainerExceptionInterface  
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function register_post()
    {
        $not = igk_notifyctrl($this->registerServiceNotifyName);
        $ctrl = $this->getController();
        $domain = $this->getController()->getConfig('domain');
        $this->redirect = base64_decode(igk_getr("referer") ?? '');
        // if (igk_environment()->isDev()){
        //     $_REQUEST['login'] = 'dta8'.date('YmdHis');
        //     $_REQUEST['password'] = 'dta';
        //     $_REQUEST['repassword'] = 'dta';
        // }
        $p = igk_get_robj("login|password|repassword");
        extract((array)$p);
        if ($p->password != $p->repassword) {
            $not->danger(__('password missmatch'));
            igk_ilog(sprintf("password missmatch, %s vs %s ", $p->password, $p->repassword));
            return false;
        }
        if (!IGKValidator::IsValidPwd($p->password)) {
            igk_ilog('not a valid pwd');
            return false;
        }

        if (empty($login)) {
            igk_ilog('login is empty');
            return false;
        }

        if (!IGKValidator::IsEmail($login)) {
            if (empty($domain)) {
                igk_ilog('domain not specified');
                return false;
            }
            $p->login .= '@' . $domain;
        }
        // registerOptions
        $cl = $this->registerController;
        // TASK : REMOVE by pass for testing
        $passby = igk_environment()->isDev() && Users::Get('clLogin', $p->login);
        if ($passby || ($cl && $cl::Register(
            $this->getController(),
            $p->login,
            $p->password
        ))) {
            $g = Users::Get('clLogin', $p->login);
            $rep = $this->sendRegistrationMail($g);
            $not->success('user added');
            if ($uri = igk_getr('good_uri')) {
                $this->redirect = base64_decode($uri);
            }
            $ctrl->setEnvParam("registrationSuccess", 1);
            $this->redirect = $ctrl::uri($this->registerUserActionRegistrationConfirmUri);
            return $rep;
        }
        $not->danger('registration failed');
        $ctrl->setEnvParam("registrationSuccess", null);
        if ($uri = igk_getr('good_uri')) {
            $this->redirect = base64_decode($uri);
        }
    }

    protected function _init_trait_RegisterUserActionTrait($ctrl)
    {
        $this->registerOptions = [
            "usageCondition" => [
                "type" => "checkbox",
                "required" => 1,
                "label_text" => sprintf(__('accept <a href="%s"> usage condition </a>'), $ctrl::uri('usageCondition'))
            ]
        ];
    }
    /**
     * send registration mail
     * @param mixed $login 
     * @return object 
     * @throws IGKException 
     * @throws BindingResolutionException  
     * @throws NotFoundExceptionInterface   
     * @throws ContainerExceptionInterface 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function sendRegistrationMail(\IGK\Models\Users $user)
    {
        $login = $user->clLogin;
        $uri = $this->getRegistrationActionvationLink($user);
        $unreg_uri = $this->getRegistrationUnregActionvationLink($user);

        
        $v_reg_info = CronJobProcess::Register("mail", "mail.register.php", $info = (object)[
            "to" => $user->clLogin,
            "title" => __("Registration"),
            "msg" => $this->getRegistrationMailMessage($user, $uri), 
            "msg-fr" => null,
            "msg-nl" => null,
            "email" => $login,
            "activate_uri" => $uri,
            "unregister_uri" => $unreg_uri,
            "fromTitle" => ""
        ], $this->getController());
 
        $mail = new Mail();
        $mail->addTo($info->to);
        $from = sprintf('"%s" <%s>', strtoupper('Toner Afrika'), igk_configs()->mail_user);
        $mail->From = $from;
        $mail->setHtmlMsg($info->msg);
        $mail->setTitle($info->title); 

        $rep = $mail->sendMail();

        if ($rep) {
            $v_reg_info->crons_status = 1;
            $v_reg_info->save();
        } 
        return (object)["registrated" => 1];
    }

    
    /**
     * get registration mail
     * @param mixed $login 
     * @param mixed $uri 
     * @return mixed 
     * @throws IGKException 
     * @throws BindingResolutionException  
     * @throws NotFoundExceptionInterface  
     * @throws ContainerExceptionInterface  
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getRegistrationMailMessage(Users $user, ?string $registerUri = null, ?string $unregisterUri = null)
    {
        $ctrl = $this->getController();
        $content = '';
        $domain = $this->_GetDomain();
        $node = igk_create_node('div');
        if ($file = ArticleContentBindingHelper::GetBindingArticle(
            $ctrl,
            $this->registerUserMailRegistrationArticle
        )) {
            $content = file_get_contents($file);
        }
        $token = ActionHelper::GenerateUserRegistrationLinkToken($user);
        
        $uri = $registerUri ?? $ctrl::uri('activate?token=' . $token);
        $n = $node->span()->h2();
        $n->Content = $domain;
        $info = [
            'user_info' => ($user ? " " . (StringUtility::DisplayName($user->clFirstName, $user->clLastName) ??
                igk_getv(explode('@', $user->clLogin), 0)) :
                null),
            'registration_link' => $uri,
            'domain' => $domain,
            'date' => date('Y-m-d')
        ]; 
        $c = $file ? ArticleContentBindingHelper::BindContent($content, $info) : null;
        $n->loop([$user])->div()->Content = $c;
        return $node->render();
    }

    protected function getRegistrationActionvationLink(Users $user)
    {
        return null;
    }
    protected function getRegistrationUnregActionvationLink(Users $user)
    {
        return null;
    }
    private function _GetDomain()
    {
        return $this->getController()->getConfig('domain');
    }

    /**
     * present registration form
     * @param mixed $form 
     * @return void 
     */
    public function form_subscribe($form, $options = null)
    {

        $ctrl = $this->getController();
        if ($ctrl->getEnvParam("registrationSuccess")) {
            $form->div()->h1()->Content = __("Registration success");
            return;
        }

        /// igk_notifyctrl($this->registerServiceNotifyName)->info("Basic")->setAutohide(false);

        $form->div()->h1()->Content = __("Subscribe");
        $form->notifyHost($this->registerServiceNotifyName, false);
        // $form->div()->setClass('igk-panel igk-success')->Content = 'data check';
        $form->cref();
        $form->confirm();
        $referer = $options ? igk_getv($options, 'referer') : null;
        $good_uri = $options ? igk_getv($options, 'good_uri') : null;
        $bad_uri = $options ? igk_getv($options, 'bad_uri') : null;
        if ($referer)
            $form->input('referer', 'hidden', base64_encode($referer));
        if ($good_uri)
            $form->input('good_uri', 'hidden', base64_encode($good_uri));
        if ($bad_uri)
            $form->input('bad_uri', 'hidden', base64_encode($bad_uri));
        if (igk_environment()->isDev())
            $form->div()->Content = "#" . $ctrl->getConfig('domain');
        $form->host(function ($t) use ($ctrl) {
            $t->fields([
                "login" => ["placeholder" => __("email"), 
                    'label_text' => __("account"),
                    'required' => 1],
                "password" => ["type" => "password", "placeholder" => __("password"), 'required' => 1],
                "repassword" => ["type" => "password", "placeholder" => __("confirm password"), 'required' => 1]
            ]);

            $reg_options = $this->registerOptions;

            if ($reg_options) {
                $t->fields($reg_options);
            }

            $t->actionbar(FormHelper::submit());
            $subbar = $t->div()->actionbar()->setClass('+footer');
            $subbar->a($ctrl::uri($this->formLoginSigninView))->Content = __("Login") . " ";
            if ($this->registerUserActionForgotPasswordUri)
            $subbar->a($ctrl::uri($this->registerUserActionForgotPasswordUri))
                ->setClass('underline')
                ->Content = __("Forgot password ?");
        });
    }

 
    /**
     * activate user account
     */
    public function activate_get()
    {
        $ctrl = $this->getController();
        if ($token = igk_getr('token')){
            $rui = ActionHelper::ActivateUser($ctrl, $token);
            if ($rui){
                $this->redirect = $ctrl::uri($this->registerUserActionCompleteUri);
                $this->notify('account success');
                return $rui;
            }
        }
        $this->notifyActionName = 'default';
        $this->notify(__('account activation failed'), 'igk-danger');
        $this->redirect = $ctrl::uri('');
    }

    protected $registerUserActionNoticationName = 'register';
    protected $registerUserActionCompleteUri = 'ServiceLogin';
    protected $logoutUri = 'logout';  


    public function deleteAccount_delete(?\IGK\Models\Users $id = null)
    {
        return $this->deleteAccount_post($id);
    }
    public function deleteAccount_post(?\IGK\Models\Users $id = null)
    {

        $cuser = ActionHelper::CurrentActionUserModel($this);
        $user = $id ?? $cuser;
        $ctrl = $this->getController();
        $ret = false;
        if ($user && ($user->clStatus == 1)) {
            $user->clStatus = 0;
            $user->clFirstName = '';
            $user->clLastName = '';
            $user->clDeactivate = date(IGK_MYSQL_DATETIME_FORMAT);
            $prefix = $this->getDeactivatedAccountPrefix();
            $login = igk_str_rm_start($user->clLogin, $prefix);
            $user->clLogin = $prefix . $login;
            $ret = $user->save();
            igk_hook(IGKEvents::HOOK_USER_DELETE, [
                "user" => $user
            ]);
            if ($user == $cuser) {
                $this->redirect = $ctrl::uri($this->logoutUri);
            }
            return $user;
        }
    }
    /**
     * get deactivated account prefix
     * @return string 
     */
    protected function getDeactivatedAccountPrefix(): string
    {
        return SystemUserActionContants::DEACTIVATED_ACCOUNT_PREFIX;
    }

    public function confirmRegistration()
    {
        // + | -------------------------------------------------------------------------
        // + | access to this only if no in log_in - in and receive confirmation service
        // + |

        $mode = 'registration';
        $user = ActionHelper::CurrentActionUserModel($this);
        if (!is_null($user) || !$this->getController()->getParam('confirmRegistration')) {
            $this->redirect = $this->getController()::uri($this->homePage ?? '');
            return;
        }
        return (object)[
            'mode' => $mode,
            'user_model' => $user
        ];
    }

    // + | --------------------------------------------------------------------
    // + | PASSWORD MANAGEMENT
    // + |
    
    protected function forgotPassword_get()
    {
        if ($token = igk_getr('token')){
            return $this->error('token is empty');
        }

    }
    protected function forgotPassword_post(?string $account=null){
        $account = $account ?? igk_getr('account'); 
        if (empty($account) && !IGKValidator::IsValidPwd($account)){
            // $this->danger(__('not a valid mail'));
            return [
                "error"=>"not a valid mail 2 ",
                "status"=>401
            ];
        }
        if (!($s = \IGK\Models\Users::Get('clLogin', $account))){
            igk_ilog('try reset password with unknow account %s', $account);
            return [
                "error"=>"account not found",
                "status"=>402
            ];
        }
        if ($this->sendResetPasswordLink($s)){
            $this->success(__('password changed'));        
            return [
                'login'=>$account,
                "message"=>__("confirm password reset link send"),
                "status"=>200,
                'user'=>$s
            ];
        }
        return null;
    }

    protected function sendResetPasswordLink(Users $user){
        ActionHelper::SendMail($this->getController(), 
            $user->clLogin, igk_configs()->mail_user, __("reset password"), 
            $this->getResetPasswordMailMessage($user), null);
    }
    /**
     * get reset password mail message
     * @param Users $user 
     * @return null|string 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function getResetPasswordMailMessage(Users $user){
        $ctrl = $this->getController();
        $content = '';
        $domain = $this->_GetDomain();
        $node = igk_create_node('div');
        if ($file = ArticleContentBindingHelper::GetBindingArticle(
            $ctrl,
            $this->registerUserMailForgotPasswordArticle
        )) {
            $content = file_get_contents($file);
        }
        $token = ActionHelper::GenerateUserRegistrationLinkToken($user);       
        $uri =  $ctrl::uri('resetPassword?token=' . $token);
        $n = $node->span()->h2();
        $n->Content = $domain;
        $info = [
            'login' => ($user ? " " . (StringUtility::DisplayName($user->clFirstName, $user->clLastName) ??
                igk_getv(explode('@', $user->clLogin), 0)) :
                null),
            'reset_pwd_link' => $uri,
            'email' => $user->clLogin,
            'domain' => $domain,
            'date' => date('Y-m-d')
        ];
        $c = $file ? ArticleContentBindingHelper::BindContent($content, $info) : null;
        $n->loop([$user])->div()->Content = $c;
        return $node->render();
    }

    /**
     * form forgot password
     * @param mixed $a 
     * @param mixed $options 
     * @return void 
     */
    protected function form_forgot_password($a, $options=null){
        $a['action'] = $this->getController()::uri('forgotPassword');
        $a->h2()->Content = __('Reset password');
        $a->notifyhost($this->notifyActionName, true);//->setAutohide(true);
        $a->div()->Content  = __("your user account's verified email address and we will send you a password reset link.");
        $a->fields([
            "account"=>['type'=>'email','required'=>1, 'placeholder'=>__('email account')]
        ]);
        $a->actionbar(FormHelper::submit());
    }   
}
