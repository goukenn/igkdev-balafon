<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKNavigationMenuBar.php
// @date: 20220803 13:48:59
// @desc: 

/*
file: class.IGKNavigationMenuBar

*/
use IGK\Controllers\BaseController;
use IGK\Controllers\ExtraControllerProperty;
use IGK\System\Html\Dom\HtmlNode;

/**
* auto generate doc.
*/
abstract class IGKNavigationMenuBarCtrl extends \IGK\Controllers\ControllerTypeBase
{ 
	/**
	 * Returns additional configuration info, including animation speed.
	 *
	 * @return array
	 */
	public static function GetAdditionalConfigInfo()
	{
		return array("clSpeed"=>new ExtraControllerProperty("text", 1000));
	}
	/**
	 * Renders the navigation menu bar from the default article's anchor links.
	 *
	 * @return BaseController
	 */
	public function View():BaseController{
		if ($this->IsVisible)
		{
			$t = $this->TargetNode->clearChilds();			
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
		return $this;
	}
} 