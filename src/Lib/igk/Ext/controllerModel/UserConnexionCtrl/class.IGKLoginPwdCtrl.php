<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKLoginPwdCtrl.php
// @date: 20220803 13:48:58
// @desc: 

//controller code class declaration
//file is a part of the controller tab list
abstract class IGKUserConnexionCtrl extends \IGK\Controllers\ControllerTypeBase
{
	public function getName(){return get_class($this);}
	protected function initComplete($context=null){
		parent::initComplete();
		//please enter your controller declaration complete here

	}
	public static function GetInfo()
	{

	}
	//@@@ init target node
	protected function initTargetNode(){
		$node =  parent::initTargetNode();
		return $node;
	}
	public function getCanAddChild(){return false; }
 
	//@@@ parent view control
	public function View(){
		$t = $this->TargetNode;
		$t->clearChilds();
		$frm = $t->addForm();
		$frm["action"] = $this->getUri("connect");
		$frm->addSLabelInput("clLogin", "lb.login", "text");
		$frm->addSLabelInput("clPwd", "lb.password", "password");
		$frm->addInput("btn_connect" , "submit");
	}
	public abstract function connect();
	public abstract function logout();
} 