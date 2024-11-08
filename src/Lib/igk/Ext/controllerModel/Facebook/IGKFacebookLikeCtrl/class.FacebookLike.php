<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.FacebookLike.php
// @date: 20220803 13:48:59
// @desc: 

///</summary>controller used to a a like on page � the target points</summary>

use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;

/** @package  */
abstract class IGKFacebookLikeCtrl  extends \IGK\Controllers\ControllerTypeBase
{
	public function getcanAddChild(){
		return false;
	}
	public static function GetAdditionalConfigInfo()
	{
		return array("clFacebookUri"=>igk_create_additional_config_info(array("clRequire"=>1)));
	}
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clFacebookUri"] = igk_getr("clFacebookUri");
	}
	public static function GetCtrlCategory(){
		return "COMMUNITY";
	}

	public function View():BaseController
	{
		$t = $this->getTargetNode();
		$t->clearChilds();
		$c = $t->Add("div");
$c->Content = <<<EOF
<iframe src="http://www.facebook.com/plugins/like.php?href={$this->Configs->clFacebookUri}&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
EOF;
return $this;
	}
}

final class IGKHtmlFacebookLikeItem extends HtmlNode
implements IIGKHtmlUriItem
{
	private $m_uri;

	public function getUri(){ return $this->m_uri; }
	public function setUri($v) { $this->m_uri = $v; return $this;}

	public function View() {
		$this->clearChilds();
        //$uri = $this->m_uri;
		$c = $this->Add("div");
// $c->Content = <<<EOF
// <iframe src="http://www.facebook.com/plugins/like.php?href={$uri}&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
// EOF;
$href = htmlentities($this->m_uri);
$c->Content = <<<EOF
<iframe src="//www.facebook.com/plugins/like.php?href={$href}&layout=button_count&action=like" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:65px;" allowTransparency="true"></iframe>
EOF; 

	} 
	public function __construct()
	{
		parent::__construct("div");
		$this->m_uri = "https://www.facebook.com/IGKDEV";
	}
} 