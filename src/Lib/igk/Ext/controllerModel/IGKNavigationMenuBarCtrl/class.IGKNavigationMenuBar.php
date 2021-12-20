<?php
/*
file: class.IGKNavigationMenuBar

*/
///<summary>represent a navigation menu bar</summary>

use IGK\Controllers\ExtraControllerProperty;
use IGK\System\Html\Dom\HtmlNode;

abstract class IGKNavigationMenuBarCtrl extends \IGK\Controllers\ControllerTypeBase
{ 
	public static function GetAdditionalConfigInfo()
	{
		return array("clSpeed"=>new ExtraControllerProperty("text", 1000));
	}
	public function View(){
		if ($this->IsVisible)
		{
			$this->TargetNode->ClearChilds();
			extract($this->getSystemVars());
			$ul =$t->add("ul");
			$v_dummy =  HtmlNode::CreateWebNode("dummy");
			$v_output = igk_html_article($this, "default", $v_dummy);

			foreach($v_dummy->getElementsByTagName("a") as  $v)
			{
				$ul->add("li")->add($v);
			}
			if ($v_output)
			{
				igk_html_article_options($this, $this->TargetNode, $this->getArticle("default"));
			}
			$script = $t->addScript();
			$script->Content = "window.igk.ctrl.bindPreloadDocument('navigation_menubar', function(){igk.winui.navigationbar.init(window.igk.getParentScript(),window.igk.ctrl.getCtrlById('".igk_get_defaultwebpagectrl()->Name."'),null);});";
			if ($this->TargetNode->HtmlItemParent == null)
			{
				igk_app()->getDoc()->getBody()->add($this->getTargetNode());
			}
		}
		else{
			$this->TargetNode->remove();
		}
	}
} 