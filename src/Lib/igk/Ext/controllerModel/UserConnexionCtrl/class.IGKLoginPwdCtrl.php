<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKLoginPwdCtrl.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\Controllers\BaseController;

/**
* Igkuser connexion ctrl.
*/
abstract class IGKUserConnexionCtrl extends \IGK\Controllers\ControllerTypeBase
{
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
    * Returns Info.
    */
    public static function GetInfo()
	{
	}
    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
		$node =  parent::initTargetNode();
		return $node;
	}
    /**
    * Returns Can Add Child.
    */
    public function getCanAddChild(){return false; }
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{
		$t = $this->TargetNode;
		$t->clearChilds();
		$frm = $t->addForm();
		$frm["action"] = $this->getUri("connect");
		$frm->addSLabelInput("clLogin", "lb.login", "text");
		$frm->addSLabelInput("clPwd", "lb.password", "password");
		$frm->addInput("btn_connect" , "submit");
		return $this;
	}
    /**
    * Connects.
    */
    public abstract function connect();
	public abstract function logout();
} 