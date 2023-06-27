<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailServiceTrait.php
// @date: 20230516 10:05:08
namespace IGK\Services\Traits;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\System\Console\Logger;
use IGK\System\DataArgs;
use IGK\System\Html\HtmlContext;

use function igk_resources_gets as __;

///<summary></summary>
/**
* global mail service trait to send controller mail 
* @package IGK\Services\Traits
*/
trait MailServiceTrait{
      /**
     * send mail 
     * @return void 
     */
    public function sendMail(
        BaseController $controller, 
        string $to, 
        string $subject,
        string $file_or_article, 
        ?array $data=null, 
        ?string $from = null,
        ?string $mail_title = null
        ){
        $n = igk_create_notagnode();
        $data = $data instanceof DataArgs ? $data : new DataArgs($data ?? []);
        $n->article($controller , $file_or_article, $data);
        
        // + | --------------------------------------------------------------------
        // + | setup mail options
        // + |
        
        $options = igk_xml_create_render_option();
        $options->Context = HtmlContext::Mail;
        $options->controller = $controller;

        $sb = $n->render($options);
        Logger::info("mailto send: ".$sb);
        igk_exit();
        $from = $from ?? igk_configs()->mail_contact;
        $mail_title = $mail_title ?? igk_configs()->website_domain ?? $subject;

        return ActionHelper::SendMail(
            $controller,
            $to,
            $from,
            __($subject),
            $sb, null, $mail_title 
        );
    }
    
}