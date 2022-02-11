<?php
// @file: IGKMailCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Net;
use function igk_resources_gets as __;

use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Html\Dom\HtmlSingleNodeViewerNode;

class MailConfigController extends ConfigControllerBase{
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="func"></param>
    public function addMailSendEvent($obj, $func){
        igk_die(__METHOD__." Not Obselete");
    }
    ///<summary></summary>
    public function getConfigPage(){
        return "mailserver";
    }
    ///<summary></summary>
    public function getName(){
        return IGK_MAIL_CTRL;
    }
    ///<summary>initialize system mail configuration</summary>
    ///<param name="mail">mail object</param>
    private function init_mail_config($mail){
        $mail->UseAuth=igk_app()->Configs->mail_useauth;
        $mail->User=igk_app()->Configs->mail_user;
        $mail->Pwd=igk_app()->Configs->mail_password;
        $mail->Port=igk_app()->Configs->mail_port;
        $mail->SmtpHost=igk_app()->Configs->mail_server;
        $mail->SocketType=igk_app()->Configs->mail_authtype;
    }
    ///<summary></summary>
    public function initMailSetting(){
        ini_set("smpt_port", igk_app()->Configs->mail_port);
        ini_set("SMTP", igk_app()->Configs->mail_server);
        ini_set("sendmail_from", igk_app()->Configs->mail_admin);
    }
    ///<summary></summary>
    ///<param name="func" default="null"></param>
    public function IsFunctionExposed($func){
        $tab=igk_array_createkeyarray(array("sendmailto", "register"), 1);
        if(isset($tab[$func]))
            return true;
        return parent::IsFunctionExposed($func);
    }
    ///<summary></summary>
    public function lock_mail(){
        if(!($maillist=constant('IGK_TB_MAINLINGLISTS'))){
            return;        }
        $c=igk_getr("clEmail");
        igk_db_update($this, igk_db_get_table_name($maillist), array("clActive"=>2), array("clEmail"=>$c));
        igk_sys_force_view();
        igk_navtocurrent();
    }
    ///<summary></summary>
    public function mail_testmail(){
      
        $app=igk_app();
        $to=igk_getr("clTestMail");
        if(empty($subject=igk_getr("subject")))
            $subject=__("Mail test: {0}", $app->getConfigs()->website_domain);
        if(empty($msg=igk_getr("msg")))
            $msg=__("<h1>Mail </h1><div>This is a test mail from <b>{0}</b></div>", $app->getConfigs()->website_domain);
        igk_app()->Configs->mail_testmail=$to;
        igk_save_config();
        $mailctrl=igk_getctrl(IGK_MAIL_CTRL);
        $c=$app->getConfigs()->mail_contact;
        if(($mailctrl != null) && !empty($c)){
            if($mailctrl->sendmail($c, $to, $subject, $msg)){
                igk_notifyctrl("mail:notifyResponse")->addSuccessr("msg.mailsend");
                igk_resetr();
            }
            else{
                igk_ilog("failed to send mail");
                igk_notifyctrl("mail:notifyResponse")->addError(__("msg.mailnotsend"). " ".$mailctrl->ErrorMsg. " ".igk_debuggerview()->getMessage());
            }
        }
        else{
            $this->msbox->addError("error ... ".$app->getConfigs()->mail_contact);
        }
        igk_set_env("replace_uri", igk_io_request_uri_path()); 
       // $this->View(); 
    }
    ///<summary></summary>
    public function mail_update(){
        $server=igk_getr("server");
        $mail=igk_getr("baseFrom");
        $port=igk_getr("port");
        $useauth=igk_getr("useauth");
        if(igk_server()->method("POST") && igk_valid_cref(1)){
            $cnf=igk_app()->getConfigs();
            $cnf->mail_server=$server;
            $cnf->mail_port=$port;
            $cnf->mail_admin=$mail;
            $cnf->mail_useauth=$useauth;
            $cnf->mail_contact=igk_getr("clContactTo");
            $cnf->mail_authtype=igk_getr("clAuthType");
            $cnf->mail_user=igk_getr("clMailUser");
            $cnf->mail_password=igk_getr("clMailPwd");
            $cnf->saveData();
            igk_save_config(true);
            igk_notifyctrl("mailconfig")->addSuccessr("Mail setting's updated");
        }
        igk_navtocurrent();
    }
    ///<summary></summary>
    ///<param name="args"></param>
    public function onMailSended($args){
        igk_hook("MailSend", array($this, $args));
    }
    ///<summary></summary>
    public function register(){
        $tb_maillist=constant('IGK_TB_MAINLINGLISTS');
        if($tb_maillist){
            $n="sys://mailregisterform";
            $c=igk_getr("clEmail");
            $n=igk_notifyctrl()->getNotification($n, true);
            if(empty($c)){
                $n->addErrorr("msg.emailnotvalid");
            }
            else{
                $n->addMsgr("msg.mailregistered");
                igk_resetr();
                igk_db_insert_if_not_exists($this, $tb_maillist, array("clEmail"=>$c));
            }
        }
        igk_sys_force_view();
        igk_navtocurrent();
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="func"></param>
    public function removeMailSendEvent($obj, $func){
        igk_die(__METHOD__." Not Obselete");
    }
    ///<summary></summary>
    ///<param name="fromName"></param>
    ///<param name="message" default="null"></param>
    public function send_contactmail($fromName, $message=null){
        $obj=igk_get_robj();
        $enode=igk_create_node("div");
        $mail=new Mail();
        $this->initMailSetting();
        $this->init_mail_config($mail);
        $mail->addTo(igk_app()->Configs->mail_contact);
        $div=igk_create_node("div");
        $div["style"]="border:1px solid black; min-height:32px;";
        $ul=$div->add("ul");
        $ul->li()->Content="Message From: ".$fromName;
        $ul->li()->Content="Email: ".$obj->clYourmail;
        $msg=$div->div();
        $msg->Content=$obj->clMessage;
        $mail->HtmlMsg=utf8_decode($div->render());
        $mail->Title=utf8_decode($obj->clSubject);
        $mail->ReplyTo=$obj->clYourmail;
        $mail->From="website@".igk_app()->Configs->website_domain;
        if($mail->sendMail()){
            igk_resetr();
            $div=igk_create_node("div");
            $div->Content=__("msg.email.correctlysend");
            $div->script()->Content="igk.animation.autohide(igk.getParentScript(), 3000);";
            $e=new HtmlSingleNodeViewerNode($div);
            $this->onMailSended(array(
                "clEmail"=>$obj->clYourmail,
                "clFirstName"=>$obj->clFirstName,
                "clLastName"=>$obj->clLastName
            ));
            return array(true, $e);
        }
        else{
            $enode->li()->Content=__("msg.mail.sendmailfailed");
            return array(false, $enode);
        }
    }
    ///<summary></summary>
    ///<param name="from"></param>
    ///<param name="to"></param>
    ///<param name="subject"></param>
    ///<param name="message"></param>
    ///<param name="reply" default="null"></param>
    ///<param name="attachement" default="null"></param>
    ///<param name="type" default="text/html"></param>
    public function sendmail($from, $to, $subject, $message, $reply=null, $attachement=null, $type="text/html"){
        $header=null;
        $this->initMailSetting();
        if($reply){
            ini_set("mail_from", $reply);
        }
        return Mail::Mail($to, $subject, $message, $from, $reply, $attachement, $type);
    }
    ///<summary></summary>
    public function sendmailto(){
        $to=igk_getr("n");
        $s=igk_getr("s");
        $m=igk_create_node("script");
        $m->Content=<<<EOF
window.open("mailto:$to?subject=$s","sendmail");
EOF;

        igk_app()->Doc->body->add(new HtmlSingleNodeViewerNode($m));
        igk_navtocurrent();
    }
    ///<summary></summary>
    public function View(){ 
        if(!$this->getIsVisible()){
            igk_html_rm($this->TargetNode);
            return;
        }

        $cnf = igk_app()->getConfigs(); 

        $c=$this->getTargetNode();
        $this->getConfigNode()->add($c);

        $c=$c->ClearChilds()->addPanelBox();
        igk_html_add_title($c, "title.configmailserver");
        $c->addPanel()->article($this, "mailserver");
        // igk_html_article($this, "mailserver", $c->addPanel());
        $div=$c->div();
        $div->addNotifyHost("mailconfig");
        $frm=$div->addForm();
        $frm["method"]="POST";
        $frm["action"]=$this->getUri("mail_update");
        igk_html_form_initfield($frm);
        $attribs=["class"=>"fitw igk-form-control form-control"];
        $frm->div()->addSLabelInput("server", "text", $cnf->mail_server, $attribs);
        $frm->div()->addSLabelInput("baseFrom", "text", $cnf->mail_admin, $attribs);
        $frm->div()->addSLabelInput("port", "text", $cnf->mail_port, $attribs);
        $o=$frm->div()->addSLabelInput("useauth", "checkbox", $cnf->mail_useauth, $attribs);
        $o->input["value"]="1";
        $o->setclass("dispib")->setStyle("width: 32px;");
        $frm->div()->host(function($t)use($cnf){$t->addLabel("cl.mailAuthType", __("clAuthType"));
            $sl=igk_html_build_select($t, "clAuthType", array("ssl"=>"ssl", "tsl"=>"tsl"), null, $cnf->mail_authtype);
            $sl["class"]="igk-form-control";
        });
        $frm->div()->addSLabelInput("clMailUser", "text", $cnf->mail_user);
        $frm->div()->addSLabelInput("clMailPwd", "password", $cnf->mail_password);
        $frm->div()->addSLabelInput("clContactTo", "text", $cnf->mail_contact, $attribs);
        $frm->actionbar(function($t){$t->addBtn("btn_update", __("Update"));
        });
        $frm=$div->addForm();
 

        $frm->notifyHost('mail:notifyResponse'); 
        $frm["method"]="POST";
        $frm["action"]=$this->getUri("mail_testmail");
        $frm["class"]="+send-mail-form";
        $fs=$frm->add("fieldset");
        $fs["style"]="padding: 15px; margin-left:-15px; margin-right: -15px; margin-bottom: 10px; border-bottom:none;";
        $fs->add("legend")->setContent(__("Test send mail"));
        $frm->host(function($f){
            $dv=$f->div();
            $dv->label()->Content=__("From");
            $dv->addInput("from", "text", igk_app()->Configs->mail_contact)->setClass("igk-form-control")->setAttribute("disabled", "true");
            $f->div()->addSLabelInput("clTestMail", "text", igk_app()->Configs->mail_testmail);
            $g=$f->div()->addSLabelInput("subject", "text", "");
            $g->input->setAttribute("placeholder", __("Subject"));
            $dv=$f->div();
            $dv->addLabel("msg")->Content=__("Message");
            $dv->addTextarea("msg")->setClass("igk-form-control igk-winui-text-editor dispib")
            ->setAttribute("placeholder", "Message");
            $dv=$f->div();
            $dv->add("label")->Content="&nbsp;";
            $dv->actionbar()->setClass("dispib")->addBtn("btn_testmail", __("Send"));
        });
        if($rp=igk_get_env("replace_uri")){
            $c->addObData(function() use ($rp){igk_ajx_replace_uri($rp);});
        }
    }
}
