<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKProductViewCtrl.php
// @date: 20220803 13:48:58
// @desc: 

///<summary>controller code class declaration file is a part of the controller tab list</summary>

use IGK\Controllers\BaseController;

abstract class IGKProductViewCtrl extends \IGK\Controllers\ControllerTypeBase
{
	public function getName(){return get_class($this);}
	 
	public static function GetAdditionnalConfigInfo()
	{
		return null;
	}	 
	public function getCanAddChild(){
		return false;
	}
	public function View():BaseController
	{ 
		$t = $this->getTargetNode();
		$t->clearChilds();
		$frm = $t->div()->addForm();
		$frm["action"]="";
		$lb = $frm->add("label");
		$lb["for"] = "";
		$lb->Content = "";
		$sl = $frm->add("select");
		$sl->option()->Content = "No product types loaded - override the view to get product list ";
		// $tb =  $igkproducttype->getDbEntries();
		// if ($tb)
		// foreach($tb->Rows as  $v)
		// {
		// 	$sl->add("options")->Content = $v->clName;
		// }
		$t->div(); 
		return $this;
	}

	public function getMoreInfo()
	{
	}
}