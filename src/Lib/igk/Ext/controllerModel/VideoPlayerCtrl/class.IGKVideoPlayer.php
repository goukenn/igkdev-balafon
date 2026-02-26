<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKVideoPlayer.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\BaseController;
use IGK\Controllers\ExtraControllerProperty;
use IGK\Resources\R;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlUtils;

/**
* Igkhtml object node.
*/
final class IGKHtmlObjectNode extends HtmlNode
{

    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct("object");
	}
}

/**
* Igkhtml video source node.
*/
class IGKHtmlVideoSourceNode extends HtmlNode
{

    /**
    * Returns Src.
    */

    public 	function getSrc()
	{
		return $this["src"];
	}

    /**
    * Sets Src.
    * @param mixed $value
    */

    public 	function setSrc($value)
	{
		$this["src"] = $value;
	}

    /**
    * Returns Type.
    */

    public 	function getType()
	{
		return $this["type"];
	}

    /**
    * Sets Type.
    * @param mixed $value
    */

    public 	function setType($value)
	{
		$this["type"] = $value;
	}

    /**
    * .ctr
    * @param mixed $src
    * @param mixed $type
    */
    public function __construct($src, $type)
	{
		parent::__construct("source");
		$this["src"] = $src;
		$this["type"] = $type;
	}

    /**
    * Clears Childs.
    */

    public function ClearChilds()
	{
		//no clear childs
	}
}

/**
* Igkhtml video node.
*/
final class IGKHtmlVideoNode extends HtmlNode
{

    /**
    * Property: object node.
    * @var mixed
    */
    private $m_ObjectNode;

    /**
    * Property: sources.
    * @var mixed
    */
    private $m_sources;

    /**
    * Adds Source.
    * @param mixed $src
    * @param mixed $type
    */

    public function addSource($src, $type)
	{
		$h = new IGKHtmlVideoSourceNode($src, $type);

		$this->m_sources[] = $h;
		$this->add($h);
		if (igk_count($this->m_sources) == 1)
			$this["src"] = $src;
		else {
			$this["src"] = null;
		}
		return $h;
	}

    /**
    * Clears Source.
    */

    public  function clearSource()
	{
		$this->m_sources = array();
	}

    /**
    * Returns Allow Control.
    */

    public function getAllowControl()
	{
		return ($this["controls"] != null);
	}

    /**
    * Sets Allow Control.
    * @param mixed $value
    */

    public function setAllowControl($value)
	{
		if ($value == null) {
			$this["controls"] = null;
		} else {
			$this->activate("controls"); // = new IGKHtmlNoValueAttribute();
		}
	}

    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct("video");
		$this->m_sources = array();
		$this->m_ObjectNode = new IGKHtmlObjectNode();
		//auto control or not
		$this["controls"] = null;
		$this["height"] = "300px";
		$this["width"] = "400px";
	}

    /**
    * Get rendering children.
    * @param null|mixed $options
    */

    protected function _getRenderingChildren($options = null)
	{
		$this->_buildObject();
		$c = parent::_getRenderingChildren($options);
		$c[] = $this->m_ObjectNode;
		return $c;
	} 
	private function _buildObject()
	{
		$t = $this->m_ObjectNode;
		$t->clearChilds();
		$t["data"] = igk_count($this->m_sources) > 0 ? $this->m_sources[0]->Src : null;
		$t["width"] = $this["width"];
		$t["height"] = $this["height"];

		$r = $t->add("embed");
		$r["src"] =  igk_count($this->m_sources) > 0 ? $this->m_sources[0]->Src : null;
		$r["width"] = $this["width"];
		$r["height"] = $this["height"];
		$r->add("data", array());
		$t->div()->Content = R::ngets("msg.cantrendervideo");
		$this->m_ObjectNode = $t;
	}
}

/**
* Igkvideo player ctrl.
*/
abstract class IGKVideoPlayerCtrl extends \IGK\Controllers\ControllerTypeBase
{

    /**
    * Property: vid node.
    * @var mixed
    */
    private $m_vidNode;

    /**
    * Returns Additional Config Info.
    */

    public static function GetAdditionalConfigInfo()
	{
		return array(
			"clPrimaryMovie" => new ExtraControllerProperty("text", ""),
			"clPrimaryWidth" => new ExtraControllerProperty("text", "400px"),
			"clPrimaryHeight" => new ExtraControllerProperty("text", "300px")
		);
	}

    /**
    * .ctr
    */
    public function __construct()
	{ //vid player construct
		parent::__construct();
	}

    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */

    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode
	{
		$t  = parent::initTargetNode();

		$n = new IGKHtmlVideoNode();
		$t->add($n);
		$this->m_vidNode = $n;
		return $t;
	}

    /**
    * Sets up Ctrl Configonfig Settings.
    */

    protected function setupCtrlConfigonfigSettings()
	{
		parent::setupCtrlConfigonfigSettings();
		//init basics source
		$this->_initBasicsSource();
	}
	private function _initBasicsSource()
	{
		if (igk_getv($this->Configs, "clPrimaryMovie")) {
			$this->setSource($this->Configs->clPrimaryMovie, "video/mp4");
		}
		if (igk_getv($this->Configs, "clPrimaryWidth")) {
			$this->m_vidNode["width"] = $this->Configs->clPrimaryWidth;
		}
		if (igk_getv($this->Configs, "clPrimaryHeight")) {
			$this->m_vidNode["height"] = $this->Configs->clPrimarHeight;
		}
	}

    /**
    * Vidplayer editsource ajx.
    */

    public function vidplayer_editsource_ajx()
	{
	}

    /**
    * Returns Controller Config Options.
    */

    public function getControllerConfigOptions()
	{
		/** @var HtmlNode$t*/
		$t = parent::getControllerConfigOptions();
		HtmlUtils::AddImgLnk($t->add("li"), igk_js_post_frame($this->getUri("vidplayer_editsource_ajx")), "videos");
		return $t;
	}

    public function setSource($src = null, $type = "video/mp4")
	{
		$this->m_vidNode->clearSource();
		$this->addSource($src, $type);
	}

    /**
    * Adds Source.
    * @param null|mixed $src
    * @param mixed $type
    */

    public function addSource($src = null, $type = "video/mp4")
	{
		$src = $src == null ? igk_getr("src") : $src;
		$type = $type == null ? igk_getr("type") : $type;
		$this->m_vidNode->addSource(igk_io_baseuri() . "/R/Videos/" . $src, $type);
	}

    /**
    * No control.
    */

    public function noControl()
	{
		$this->m_vidNode->setAllowControl(false);
	}

    /**
    * Allows Control.
    */

    public function allowControl()
	{
		$this->m_vidNode->setAllowControl(true);
	}

    /**
    * View.
    * @return BaseController
    */

    public function  View(): BaseController
	{
		//no view. rendering
		if (!$this->getIsVisible())
			igk_html_rm($this->TargetNode);
		return $this;
	}

    /**
    * Show view file.
    */

    protected function _showViewFile()
	{
		//not visible by default
	}

    /**
    * Returns Can Add Child.
    */

    public function getCanAddChild()
	{
		return false;
	}

    /**
    * Show child.
    * @param null|mixed $targetnode
    */

    protected function _showChild($targetnode = null)
	{
		//no childs
	}
}
