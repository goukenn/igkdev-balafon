<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegisterUserServiceActionTrait.php
// @date: 20230516 08:45:52
namespace IGK\Actions\Traits;

use com\igkdev\bantubeat\Helper\MailService;
use Exception;
use IGK\Helper\ActionHelper;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKEvents;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* use to handle user registration with mail only to application 
* @package IGK\Actions\Traits
*/
trait RegisterUserServiceActionTrait{
    protected function _init_trait_RegisterUserServiceActionTrait(){
        igk_reg_hook(IGKEvents::HOOK_USER_ADDED, function($e){
            $user = $e->args["user"];
            // $lived_token = '';
            $lived_token = ActionHelper::GenerateUserRegistrationLinkToken($user);   
            $this->_createUserApp($user);
            $this->_sendMailRegistration($user,[
                'activation_uri'=>$this->uri('/auth/activate/'.$lived_token),
                'unsubscribe_uri'=>$this->uri('/auth/unsubscribe/'.$lived_token)
            ]);
        });
    }

    /**
     * 
     * @param string $token 
     * @return void 
     */
    public function auth(string $name, string $token){
        switch($name){
            case 'activate':
                ActionHelper::ActivateUser($this->getController(), $token, $this->_nextRegistrationLink());
                break;
            case 'unsubscribe':
                ActionHelper::UnregisterUser($this->getController(), $token, $this->_nexUnregisrationLink());
                break;
       }
       $this->die('No allowed');

    }
    /**
     * retrieve uri from action definition 
     * @param string $path 
     * @return mixed 
     */
    protected function uri(string $path){
        $entry_uri = strtolower(ActionHelper::GetActionName($this->getController(), get_class($this)));
        return $this->getController()->uri($entry_uri.$path);
    }
    /**
     * create application On user Added
     * @param mixed $user 
     * @return bool 
     */
    protected function _createUserApp($user):bool{
        return false;
    }
    /**
     * send user mail registration 
     * @param mixed $user 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _sendMailRegistration($user,?array $data=null, $title='Registration', $template_or_article='mails/registration.template'){
        $ctrl = $this->getController();
        if ($service = igk_app()->getService(\MailService::class)){
            $service->sendMail($ctrl, $user->clLogin, $title, $template_or_article, $data);
        }     
    }
    /**
     * send mail to reset user password
     * @param mixed $user 
     * @param null|array $data 
     * @param string $title 
     * @param string $template_or_article 
     * @return void 
     */
    protected function _sendMailChangePassword($user,?array $data=null, $title='Change password', $template_or_article='mails/resetPassword.template'){
        $ctrl = $this->getController();
        if ($service = igk_app()->getService(\MailService::class)){
            $service->sendMail($ctrl, $user->clLogin, $title, $template_or_article, $data);
        }     
    }
}