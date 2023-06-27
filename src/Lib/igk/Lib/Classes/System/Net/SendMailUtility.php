<?php
// @author: C.A.D. BONDJE DOUE
// @file: SendMailUtility.php
// @date: 20230425 08:32:33
namespace IGK\System\Net;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helpers\ArticleHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Process\CronJobProcess;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use IGKException;
use ReflectionException;

use function igk_resources_gets as __;

///<summary></summary>
/**
* 
* @package IGK\System\Net
*/
class SendMailUtility{
    /**
     * send mail and process register
     * @param mixed $baseController 
     * @param mixed $to 
     * @param mixed $from 
     * @param mixed $title 
     * @param mixed $data 
     * @return int|bool 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws IGKException 
     */
    public static function SendMail($baseController, $to, $from, $title, $data){

        if ($data instanceof HtmlItemBase){
            $mailoptions = igk_mail_option();
            $data = $data->render($mailoptions);
        }

        $v_reg_info = CronJobProcess::Register("mail", "mail.register.php", $info = (object)[
            "to" => $to,
            "title" => $title,
            "msg" => $data,
            'from'=> $from, 
        ], $baseController);

        $mail = new Mail();
        $mail->From = $from;        
        $mail->addTo($to);        
        $mail->setHtmlMsg($data);
        $mail->setTitle($title);

        $rep = $mail->sendMail();
        if ($rep){
            $v_reg_info->crons_status = 1;
            $v_reg_info->save();
        }
        return $rep;
    }
    /**
     * send mail instruction 
     * @param BaseController $baseController 
     * @param string $article 
     * @param array $data 
     * @return int|bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     */
    public static function SendMailInstruction(BaseController $baseController, string $article, array $data){
        $farticle = ArticleHelper::ResolveGetArticle($baseController, $article) ?? igk_die(sprintf(__('no article found %s'), $article));         
        $brandname = $baseController->getConfig('brandName') ?? igk_configs()->website_domain;
        $recovery_uri = $data['recover_uri'];
        $msg = igk_create_notagnode();
        $msg->article($baseController, $farticle, array_merge($data,['uri'=>$recovery_uri]));

        $to = $data['email'];
        $data = $msg->render();
        empty($data) && igk_die(__("mail message is empty"));
        $title = __('recovery Password');
        $from = sprintf('"%s" <%s>', strtoupper($brandname), igk_configs()->mail_user);
        return self::SendMail($baseController, $to, $from, $title, $msg);       
    }
}