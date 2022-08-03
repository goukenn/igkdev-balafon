<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.ContactZoneCtrl.php
// @date: 20220803 13:48:59
// @desc: 

//controller code class declaration
//file is a part of the controller tab list

use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlNotificationItemNode;

abstract class ContactZoneCtrl extends \IGK\Controllers\ControllerTypeBase
{
	private $m_viewZone;
	private $m_error;

	public function getError(){return $this->m_error; }
	public function getName(){return get_class($this);}

	protected function initComplete($context=null){
		parent::initComplete();
		//please enter your controller declaration complete here

	}
	public static function GetAdditionalConfigInfo()
	{
		return null;
	}
	//@@@ init target node
	protected function initTargetNode(){
		$node =  parent::initTargetNode();
		$this->m_viewZone = $node->div();
		return $node;
	}
	public function getCanAddChild(){
		return false;
	}
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
	public function send_mail()
	{
		$obj = igk_get_robj();
		$enode = $this->validate_form();
		$t = new HtmlNotificationItemNode($this->TargetNode, "send mail");
		if ($enode->ChildCount == 0)
		{//no error found

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
		//igk_navtocurrent("./#".strtolower($this->Name));

	}
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

			//array("clMessage", "textarea", true, "message")

	}

	public function View(){
		extract($this->getSystemVars());
		$t = $this->TargetNode;
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


	}
} 