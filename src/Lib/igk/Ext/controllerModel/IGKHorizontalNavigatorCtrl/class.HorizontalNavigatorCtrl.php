<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.HorizontalNavigatorCtrl.php
// @date: 20220803 13:48:59
// @desc: 

/*
file: class.HorizontalNavigatorCtrl
Description: control that will host every article and navigate thru them by configuration
*/
use IGK\Controllers\BaseController;
use IGK\Controllers\ExtraControllerProperty;
/**
 * Horizontal navigator controller 
 * @package 
 */
abstract class HorizontalNavigatorCtrl extends \IGK\Controllers\ControllerTypeBase {
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{return get_class($this);}
    /**
    * Getcan add child.
    */
    public function getcanAddChild(){
		return false;
	}
    /**
    * Returns Additional Default View Content.
    */
    public static function GetAdditionalDefaultViewContent(){
		return null;
	}
    /**
    * Returns Additional Config Info.
    */
    public static function GetAdditionalConfigInfo()
	{
		return array(
		"clShowBullet"=> new ExtraControllerProperty("bool", true),
		"clanim_NAV_ANIMFREQUENCY"=> new ExtraControllerProperty("text", 20),
		"clanim_NAV_ANIMDURATION"=> new ExtraControllerProperty("text", 1000),
		"clanim_NAV_AUTOANIMATE"=> new ExtraControllerProperty("bool", true),
		"clanim_NAV_AUTOPERIOD"=> new ExtraControllerProperty("text", 10000),
		"clanim_NAV_ANIMTYPE"=> new ExtraControllerProperty("select",
		array("translation"=>"translation", "rotation"=>"rotation", "fade"=>"fade"),
		"translation")
		);
	}
    /**
    * Sets Additional Config Info.
    * @param mixed & $t
    */
    public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clShowBullet"] = igk_getr("clShowBullet");
		$t["clanim_NAV_ANIMFREQUENCY"] = igk_getr("clanim_NAV_ANIMFREQUENCY");
		$t["clanim_NAV_ANIMDURATION"] = igk_getr("clanim_NAV_ANIMDURATION");
		$t["clanim_NAV_AUTOANIMATE"] = igk_getr("clanim_NAV_AUTOANIMATE");
		$t["clanim_NAV_ANIMTYPE"] = igk_getr("clanim_NAV_ANIMTYPE");
		$t["clanim_NAV_AUTOPERIOD"] = igk_getr("clanim_NAV_AUTOPERIOD");
	} 
    /**
    * View.
    * @return BaseController
    */
    public function View():BaseController{
		$this->TargetNode->clearChilds();
		$c = new JSHorizontalPane($this->TargetNode);
		$this->buildPage($c);
		$c->ShowBullet = true;
		$c->AnimInterval  = igk_getv($this->Configs, "clanim_NAV_ANIMFREQUENCY", 20);
		$c->AnimDuration  = igk_getv($this->Configs, "clanim_NAV_ANIMDURATION", 1000);
		$c->IsAutoAnimate = igk_getv($this->Configs, "clanim_NAV_AUTOANIMATE", 1);
		$c->AnimPeriod    = igk_getv($this->Configs, "clanim_NAV_AUTOPERIOD", 10000);
		$c->AnimType      = igk_getv($this->Configs, "clanim_NAV_ANIMTYPE", "translation");
		$c->flush();
		$this->_incViewfile("default");
		$this->_onViewComplete();
		return $this;
	}
    /**
    * Builds Page.
    * @param mixed $pane
    */
    protected function buildPage($pane){
		$t = $this->getAllArticles();
		if (is_array($t))
		{
			sort($t);
			foreach($t as $v)
			{
				igk_html_article($this, basename($v), $pane->addPage());
			}
		}
	}
} 