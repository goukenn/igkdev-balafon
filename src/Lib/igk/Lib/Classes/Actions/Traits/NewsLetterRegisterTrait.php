<?php
// @author: C.A.D. BONDJE DOUE
// @file: NewsLetterRegisterTrait.php
// @date: 20221115 08:45:03
namespace IGK\Actions\Traits;

use Exception;
use IGK\Helper\ActionHelper;
use IGK\Models\Mailinglists;
use IGK\Resources\R;
use IGK\System\Exceptions\CrefNotValidException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Http\Request;
use IGK\System\Process\CronJobProcess;
use IGKEvents;
use IGKException;
use IGKUserAgent;
use IGKValidator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait NewsLetterRegisterTrait{
    use NewsLetterFormActionTrait;

    /**
     * register to mail service
     * @param string $mail 
     * @param null|string $init 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws BindingResolutionException 
     * @throws BindingResolutionException 
     * @throws NotFoundExceptionInterface 
     * @throws NotFoundExceptionInterface 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws Exception 
     * @throws CssParserException 
     */
    protected function registerMail(string $mail, ?string $init=null){
        $mailinfo = Mailinglists::createEmptyRow();
        $mailinfo->clml_email = $mail;
        $mailinfo->clml_state = 0;
        $mailinfo->clml_source = igk_configs()->website_domain;
        $mailinfo->clml_locale = strtolower(R::GetCurrentLang());
        $mailinfo->clml_agent = IGKUserAgent::Agent();
        $mailinfo->clml_init = '';
        if (!Mailinglists::select_row(["clml_email"=>$mail])){
            if ($inf = Mailinglists::create($mailinfo)){
                $this->notify_success("welcome to new letter mailing list"); 
                igk_hook(IGKEvents::HOOK_MAIL_REGISTER, ["mailinfo"=>$inf]);
                $uri = igk_io_baseuri()."/registerService/activate-mail/?q=".base64_encode(http_build_query([
                    "email"=>$mail, 
                    "guid"=>igk_create_guid()
                ]));
                $un_reguri = igk_io_baseuri()."/registerService/unregister-mail/?q=".base64_encode(http_build_query([
                    "email"=>$mail,
                    "guid"=>igk_create_guid() 
                ]));
                $ctrl = $this->getController();
                $msg = $this->getNewsLetterMailRegistrationMessage($uri, $un_reguri);
                if (ActionHelper::SendMail(
                    $ctrl,
                    $mail,
                    $ctrl->getConfig('mail_contact'),
                    __('Registration'),
                    $msg, [], 
                    $ctrl->getConfig('mail_title')
                )){
                    return true;
                }
                // CronJobProcess::Register("mail", "mail.register.php", (object)[
                //     "title"=>"registration",
                //     "msg"=>"welcome to , local.com<br /><p>please <a href=\"".$uri."\">click here to actived </a> your new letter</p>".
                //     "Unregister use this linked <a href=\"".$un_reguri."\">Unregister</a>",
                //     "msg-fr"=>null,
                //     "msg-nl"=>null,
                //     "email"=>$mail,
                //     "activate_uri"=>$uri,
                //     "unregister_uri"=>$un_reguri,
                // ], $this->getController());
            }
        }
        return false;
    }
    /**
     * override to get new lette message - active 
     * @param mixed $gooduri 
     * @param mixed $baduri 
     * @return string 
     */
    protected function getNewsLetterMailRegistrationMessage($gooduri, $baduri){
        return "Welcome to , local.com<br /><p>please <a href=\"".$gooduri."\">click here to actived </a> your new letter</p>".
        "Unregister use this linked <a href=\"".$baduri."\">Unregister</a>";
    }
        /**
     * stay in touch
     * @param Request $request 
     * @return void 
     * @throws IGKException 
     * @throws CrefNotValidException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function stay_in_touch(Request $request){       
        $mail = $request->get("email");
        $uri = $un_reguri = null;
        if (igk_valid_cref(1) && IGKValidator::IsEmail($mail)){
           if ($this->registerMail($mail, __FUNCTION__)){
                $this->notify_success(__("successfully register"));
           }
        }
        else{
            $this->notify_danger("failed to register");
        }
        igk_navto(igk_server()->HTTP_REFERER ?? $this->getController()->uri());
    }
   
}