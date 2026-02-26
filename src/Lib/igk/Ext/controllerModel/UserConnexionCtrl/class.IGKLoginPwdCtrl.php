<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKLoginPwdCtrl.php
// @date: 20220803 13:48:58
// @desc: 

//controller code class declaration
//file is a part of the controller tab list

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
		//please enter your controller declaration complete here

	}

    /**
    * Returns Info.
    */
    public static function GetInfo()
	{

	}
	//@@@ init target node

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
 
	//@@@ parent view control

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