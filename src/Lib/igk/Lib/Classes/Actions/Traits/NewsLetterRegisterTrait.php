<?php
// @author: C.A.D. BONDJE DOUE
// @file: NewsLetterRegisterTrait.php
// @date: 20221115 08:45:03
namespace IGK\Actions\Traits;
 
use IGK\Models\Mailinglists;
use IGK\Resources\R;
use IGK\System\Exceptions\CrefNotValidException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Request;
use IGK\System\Process\CronJobProcess;
use IGKEvents;
use IGKException;
use IGKUserAgent;
use IGKValidator;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait NewsLetterRegisterTrait{
    use NewsLetterFormActionTrait;
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
            $mailinfo = Mailinglists::createEmptyRow();
            $mailinfo->clEmail = $mail;
            $mailinfo->clState = 0;
            $mailinfo->clml_Source = igk_configs()->website_domain;
            $mailinfo->clml_locale = strtolower(R::GetCurrentLang());
            $mailinfo->clml_agent = IGKUserAgent::Agent();
            if (!Mailinglists::select_row(["clEmail"=>$mail])){
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
                    CronJobProcess::Register("mail", "mail.register.php", (object)[
                        "title"=>"registration",
                        "msg"=>"welcome to , local.com<br /><p>please <a href=\"".$uri."\">click here to actived </a> your new letter</p>".
                        "Unregister use this linked <a href=\"".$un_reguri."\">Unregister</a>",
                        "msg-fr"=>null,
                        "msg-nl"=>null,
                        "email"=>$mail,
                        "activate_uri"=>$uri,
                        "unregister_uri"=>$un_reguri,
                    ], $this->getController());
                }
            }
        }
        $this->notify_danger("failed to register");
        igk_navto(igk_server()->HTTP_REFERER ?? $this->getController()->uri());
    }
   
}