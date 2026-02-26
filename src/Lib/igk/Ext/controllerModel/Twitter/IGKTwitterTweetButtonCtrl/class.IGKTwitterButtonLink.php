<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKTwitterButtonLink.php
// @date: 20220803 13:48:58
// @desc: 

/*
description: use to share a link to twitter
*/

use IGK\Controllers\BaseController;
use IGK\Resources\R;

/**
* auto generate doc.
*/
abstract class IGKTwitterButtonLinkCtrl  extends \IGK\Controllers\ControllerTypeBase
{
	/**
	 * Indicates whether child elements can be added to this controller.
	 *
	 * @return bool
	 */
	public function getcanAddChild(){
		return false;
	}
	/**
	 * Returns the list of additional configuration keys for this controller.
	 *
	 * @return array
	 */
	public static function GetAdditionalConfigInfo()
	{
		return array("clTwitterUri");
	}
	/**
	 * Populates additional configuration values from the request into the given array.
	 *
	 * @param array $t
	 * @return void
	 */
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clTwitterUri"] = igk_getr("clTwitterUri");
	}
	 
	/**
	 * Returns the category name used to group this controller in the UI.
	 *
	 * @return string
	 */
	public static function GetCtrlCategory(){
		return "COMMUNITY";
	}
	 

	/**
	 * Renders the Twitter share button with the configured URL and language.
	 *
	 * @return BaseController
	 */
	public function View():BaseController
	{
		$t = $this->getTargetNode();
		$t->clearChilds();
		$c = $t->Add("div");
		$tweet = R::ngets("lb.tweet");
		$l = R::GetCurrentLang();
$c->Content = <<<EOF
<a href="https://twitter.com/share" class="twitter-share-button" data-url="{$this->Configs->clTwitterUri}" data-lang="{$l}" >{$tweet->getValue()}</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
EOF;
return $this;
	}
} 