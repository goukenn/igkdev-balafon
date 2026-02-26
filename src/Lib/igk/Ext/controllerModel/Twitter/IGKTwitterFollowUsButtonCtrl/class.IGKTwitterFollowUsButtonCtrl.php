<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKTwitterFollowUsButtonCtrl.php
// @date: 20220803 13:48:58
// @desc: 

/*
file : class.IGKTwitterFollowUsButtonCtrl.php
create: 13-07-14
author: cad BONDJE DOUE
license : licence.txt
*/

use IGK\Controllers\BaseController;
use IGK\Controllers\ExtraControllerProperty;
use IGK\Resources\R;

/**
* auto generate doc.
*/
abstract class IGKTwitterFollowUsButtonCtrl extends \IGK\Controllers\ControllerTypeBase
{
	private $m_script;
	const sn = "twitter://followbutton";//script name

	/**
	 * Returns additional configuration properties for the follow button.
	 *
	 * @return array
	 */
	public static function GetAdditionalConfigInfo()
	{
	 return array(
		"clUri"=> new ExtraControllerProperty("text", "https://twitter.com/"),
		"clShowDataCount"=> new ExtraControllerProperty("select",
		array("true"=>"true", "false"=>"false"),
		"false"),
		"clButtonSize"=>new ExtraControllerProperty("select",
		array("medium"=>"medium", "large"=>"large"),
		"medium"),
		);
		//return array("clShowDataCount");
	}
	/**
	 * Initializes and returns the target HTML node.
	 *
	 * @return \IGK\System\Html\Dom\HtmlNode|null
	 */
	protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
		return parent::initTargetNode();
	}
	/**
	 * Completes initialization by injecting the Twitter widgets script.
	 *
	 * @param mixed $context
	 * @return void
	 */
	protected function initComplete($context=null){
		parent::initComplete();

		$s = $this->App->Doc->getScriptManager()->getScript(self::sn);
		if ($s==null){
		$this->m_script = $this->App->Doc->getScriptManager()->addScript(self::sn);

		$this->m_script->setContent(
<<<EOF
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
EOF
		);
		}

	}
	/**
	 * Renders the Twitter follow button into the target node.
	 *
	 * @return BaseController
	 */
	public function View():BaseController{
		$t = $this->getTargetNode();
		if ($this->getIsVisible())
		{ 
			$t->clearChilds();
			$show_count = igk_parsebool(igk_getv($this->Configs,"clShowDataCount", false));
			$t->add("a", array(
			"href"=>igk_getv($this->Configs,"clUri", "https://twitter.com/twitterapi"),
			"class"=>"twitter-follow-button",
			"data-show-count"=>$show_count,
			"data-size"=>($n = igk_getv($this->Configs,"clButtonSize",null))? ($n=="medium"?null: $n):$n,
			"data-lang"=>$this->getlang()
			));
		}
		else
			$t->remove();
		return $this;
	}
	/**
	 * Returns the current language code if supported by Twitter, or null.
	 *
	 * @return string|null
	 */
	public function getlang()
	{
		$l = R::GetCurrentLang();
		if (preg_match("/(fr|en|de|it|es|co|jp)/i", strtolower($l)))
		{
			return strtolower($l);
		}
		return null;
	}
} 