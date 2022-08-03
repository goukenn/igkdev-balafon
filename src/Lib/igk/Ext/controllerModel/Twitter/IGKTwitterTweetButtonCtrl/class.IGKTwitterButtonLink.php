<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKTwitterButtonLink.php
// @date: 20220803 13:48:58
// @desc: 

/*
description: use to share a link to twitter
*/

use IGK\Resources\R;

abstract class IGKTwitterButtonLinkCtrl  extends \IGK\Controllers\ControllerTypeBase
{
	public function getcanAddChild(){
		return false;
	}
	public static function GetAdditionalConfigInfo()
	{
		return array("clTwitterUri");
	}
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clTwitterUri"] = igk_getr("clTwitterUri");
	}
	 
	public static function GetCtrlCategory(){
		return "COMMUNITY";
	}
	 

	public function View()
	{

		extract($this->getSystemVars());
		$t->clearChilds();
		$c = $t->Add("div");
		$tweet = R::ngets("lb.tweet");
		$l = R::GetCurrentLang();
$c->Content = <<<EOF
<a href="https://twitter.com/share" class="twitter-share-button" data-url="{$this->Configs->clTwitterUri}" data-lang="{$l}" >{$tweet->getValue()}</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
EOF;

	}
} 