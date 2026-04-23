<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.ContactZoneCtrl.php
// @date: 20220803 13:48:59
// @desc: 
use IGK\Controllers\BaseController;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlNotificationItemNode;

/**
* Contact zone ctrl.
*/
abstract class ContactZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{
    /**
    * Property: view zone.
    * @var mixed
    */
    private $m_viewZone;
    /**
    * Property: error.
    * @var mixed
    */
    private $m_error;
    /**
    * Returns Error.
    */
    public function getError(){return $this->m_error; }
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{return get_class($this);}
    /**
    * Initializes Complete.
    * @param null|mixed $context
    */
    protected function initComplete($context=null){
		parent::initComplete();
	}
    /**
    * Returns Additional Config Info.
    */
    public static function GetAdditionalConfigInfo()
	{
		return null;
	}
    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
		$node =  parent::initTargetNode();
		$this->m_viewZone = $node->div();
		return $node;
	}
    /**
    * Returns Can Add Child.
    */
    public function getCanAddChild(){
		return false;
	}
    /**
    * Validates form.
    */
    protected function validate_form()
	{
		$obj = igk_get_robj();
		$enode =  HtmlNode::CreateWebNode("div");
		IGKValidator::Assert(empty($obj->clFirstName), $error, $enode, R::ngets("ERR.Mail.NoFirstName"));
		IGKValidator::Assert(empty($obj->clLastName), $error, $enode, R::ngets("ERR.Mail.NoLastName"));
		IGKValidator::Assert(empty($obj->clMessage), $error, $enode, R::ngets("ERR.Mail.NoMessage"));
		IGKValidator::Assert(empty($obj->clMessage) || !IGKValidator::IsEmail($obj->clYourmail),  $error, $enode, R::ngets("ERR.Mail.MailNotValid"));
		return $enode;
	}
    /**
    * Sends mail.
    */
    public function send_mail()
	{
		$obj = igk_get_robj();
		$enode = $this->validate_form();
		$t = new HtmlNotificationItemNode($this->TargetNode, "send mail");
		if ($enode->ChildCount == 0)
		{
			$e = igk_getctrl("igkmailctrl")->send_contactmail( $obj->clFirstName. "  ".strtoupper($obj->clLastName), "" );
			$e[1]["igk-control-type"] = "notifyctrl";
			if ($e[0] == false)
			{
				$t["class"] = "igk_notify_error";
				$t->add($e[1]);
				$this->m_error = $t;
			}
			else{
				$t["class"] = "igk_notify_good";
				igk_resetr();
				$t->add($e[1]);
				$this->m_error = $t;
			}
		}
		else{
			$t["class"] = "igk_notify_error";
			$t->add($enode);
			$this->m_error =  $t ;
		}
		$this->View();
		$this->m_error = null;
		$this->m_response = null;
	}
    /**
    * Builds Contact Form.
    * @param mixed $target
    */
    protected function buildContactForm ($target)
	{
		$ul  = $target->add("ul");
		 igk_html_build_form($ul,
		array(
			array("clFirstName", "text",true),
			array("clLastName", "text",true),
			array("clYourmail", "text", true,"yourmail"),
			array("clSubject", "text", true,"sujet")
		 ));
		 $li = $ul->add("li");
		 $li->addLabel("lb.clMessage" , "clMessage");
		 $li->addTextArea("clMessage", "", array("defaulttext"=>R::ngets("tooltip.pleaseenteryourmessage")));
	}
    /**
    * View.
    * @return BaseController
    */
    public function View(): BaseController{		
		$t = $this->getTargetNode();
		$t->clearChilds();
		$this->_showViewFile();
		$t->Add("noscript", array("class"=>"hidden"))->div()->setAttributes(array("id"=>"igkdev-nocontact", "class"=>""))->Content = IGK_HTML_SPACE;
		$c = $t->div()->setAttributes(array("class"=>"igk-page-content nowrap"));
		$div = $c->div();
		$div["id"]= "igk-contact-info-node";
		$div["class"]= "igk-contact-info-node";
		igk_html_article($this, "contact_info", $div, null, true);
		$div = $c->div();
		$frm = $div->addForm();
		$frm["action"]= $this->getUri("send_mail");
		$frm["class"]= "igk_contact_mailform";
		$frm->add($this->Error);
		$this->buildContactForm($frm);
		$frm->div()->setAttributes(array("class"=>"contact_requested_field"))->Content = R::ngets("msg.requestedfield");
		$frm->addInput("btn_send", "submit", R::ngets("btn.sendmail"));
		return $this;
	}
} 