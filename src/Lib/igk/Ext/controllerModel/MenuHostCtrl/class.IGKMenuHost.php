<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKMenuHost.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\Controllers\BaseController;
use IGK\Controllers\ExtraControllerProperty;

/**
* Igkmenu host ctrl.
*/
abstract class IGKMenuHostCtrl extends \IGK\Controllers\ControllerTypeBase
{
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{return get_class($this);}
    /**
    * Returns Additional Config Info.
    */
    public static function GetAdditionalConfigInfo()
	{
		return array("clAllowMenuNavigation"=>new ExtraControllerProperty("bool", true));
	}
    /**
    * Sets Additional Config Info.
    * @param mixed & $tab
    */
    public static function SetAdditionalConfigInfo(& $tab)
	{
		$tab["clAllowMenuNavigation"] = igk_getr("clAllowMenuNavigation");
	}
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{ 
		$controllers = igk_app()->getControllerManager()->getControllers();
		$t = $this->getTargetNode();
		$t->clearChilds();
		$igkmenuctrl = igk_getv($controllers, "igkmenuctrl");
		if ($igkmenuctrl)
			$igkmenuctrl->setParentView($t);
		if ($this->Configs->clAllowMenuNavigation)
		{
		$v_tabs = $t->getElementsByTagName("a");
		foreach($v_tabs as $v)
		{
			$n = igk_regex_get("/p=(?P<name>[^&]+)/i","name", $v["href"] );
			$v["href"] = "#".$n;
			$v["igk-nav-link"]= $n;
		}
		$t->script()->Content =<<<EOF
var node = igk.getParentScript();
var parent = document.getElementById("{$this->app->Configs->web_pagectrl}");
igk.ready(function(){
igk.winui.navigationBar.init(node, parent,  {duration:1000, interval:20, "orientation":"vertical"});
});
EOF;
		}
		return $this;
	}
} 