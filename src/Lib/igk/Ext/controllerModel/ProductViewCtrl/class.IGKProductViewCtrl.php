<?php
///<summary>controller code class declaration file is a part of the controller tab list</summary>
abstract class IGKProductViewCtrl extends \IGK\Controllers\ControllerTypeBase
{
	public function getName(){return get_class($this);}

	protected function InitComplete(){
		parent::InitComplete();
		//please enter your controller declaration complete here
	}
	public static function GetAdditionnalConfigInfo()
	{
		return null;
	}
	//@@@ init target node
	protected function initTargetNode(){
		$node =  parent::initTargetNode();
		return $node;
	}
	public function getCanAddChild(){
		return false;
	}
	public function View()
	{
		extract($this->getSystemVars());
		$t->clearChilds();
		$frm = $t->div()->addForm();
		$frm["action"]="";
		$lb = $frm->add("label");
		$lb["for"] = "";
		$lb->Content = "";
		$sl = $frm->add("select");
		$tb = $igkproducttype->getDbEntries();
		if ($tb)
		foreach($tb->Rows as  $v)
		{
			$sl->add("options")->Content = $v->clName;
		}
		$t->div();
		$t->div();

	}

	public function getMoreInfo()
	{
	}
}