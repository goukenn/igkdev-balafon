<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKProductViewCtrl.php
// @date: 20220803 13:48:58
// @desc:

use IGK\Controllers\BaseController;

abstract class IGKProductViewCtrl extends \IGK\Controllers\ControllerTypeBase
{
	/**
	 * Return the name of this controller (its class name).
	 *
	 * @return string
	 */
	public function getName(): string{return get_class($this);}

	/**
	 * Return additional configuration info (none for this controller).
	 *
	 * @return null
	 */
	public static function GetAdditionnalConfigInfo()
	{
		return null;
	}

	/**
	 * Return whether this controller can accept child controllers.
	 *
	 * @return bool
	 */
	public function getCanAddChild(){
		return false;
	}

	/**
	 * Render the default product view form into the target node.
	 *
	 * @return BaseController
	 */
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

	/**
	 * Return additional product information (to be overridden by subclasses).
	 *
	 * @return void
	 */
	public function getMoreInfo()
	{
	}
}
