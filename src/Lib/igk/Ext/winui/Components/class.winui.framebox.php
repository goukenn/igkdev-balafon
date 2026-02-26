<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.winui.framebox.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\System\Html\Dom\HtmlNode;

/**
* Igkwin ui framebox.
*/
class IGKWinUI_framebox extends HtmlNode
{

    /**
    * Property: script.
    * @var mixed
    */
    private $m_script;

    /**
    * Property: nodes.
    * @var mixed
    */
    private $m_nodes;

    /**
    * Property: close uri.
    * @var mixed
    */
    var $closeUri;

    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct("div");
		$this["class"] = "posab fitw fith loc_t loc_l overflow_none ztop";

	}

    /**
    * Renders.
    * @param null|mixed $options
    */

    public function render($options =null)
	{
		$out ="";
		$out .="<div ";
		$out .= $this->getAttributeString();
		$out .= ">";
		$out .= $this->getInnerHtml()($options);
		$this->m_script =  HtmlNode::CreateWebNode("script");
		$this->m_script->Content = "igk.winui.framebox.initSingle(igk.getParentScript(), ".(($this->closeUri)?"'". $this->closeUri. "'":'null'). ");";
		$out .= $this->m_script->render($options);
		$out .= "</div>";
		return $out;
	}
} 