<?php
//controller code class declaration
//file is a part of the controller tab list
abstract class IGKUserConnexionCtrl extends \IGK\Controllers\ControllerTypeBase
{
	public function getName(){return get_class($this);}
	protected function initComplete(){
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
	//----------------------------------------
	//Please Enter your code declaration here
	//----------------------------------------
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