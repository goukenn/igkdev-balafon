<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.winui.pane.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\System\Html\Dom\HtmlNode;

/**
* Igkwin ui pane view.
*/
class IGKWinUI_paneView extends IGKWinUIControl
{

    /**
    * Property: script.
    * @var mixed
    */
    private $m_script;

    /**
    * Property: load uri.
    * @var mixed
    */
    private $m_loadUri;

    /**
    * Adds Group.
    * @param null|mixed $name
    */

    public function addGroup($name = null){
		$g = new IGKWinUI_paneViewgroup();
		$g->Name = $name;
		$this->Add($g);
		return $g;
	}

    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct("div");
		$this["class"]="pane-view fitw .fith" ;
		$this->m_script =  HtmlNode::CreateWebNode("script");
		$this->m_script->Content = "igk.winui.paneview.init();";
	}

    /**
    * Get rendering children.
    * @param null|mixed $options
    */

    protected function _getRenderingChildren($options = null)
	{
		$this->m_script->Content = <<<EOF
igk.gui.paneview.loadfromUri( igk.getParentScript(),"{$this->m_loadUri}");
EOF;
		return [
			$this->m_script
		];
	}

    /**
    * Getload uri.
    */

    public function getloadUri(){return $this->m_loadUri;}

    /**
    * Setload uri.
    * @param mixed $value
    */

    public function setloadUri($value){$this->m_loadUri = $value; }

}

/**
* Igkwin ui pane viewitem.
*/
class IGKWinUI_paneViewitem extends HtmlNode
{
	/** @var HtmlNode*/
	private $m_link;

    /**
    * Gethref.
    */

    public function gethref(){return $this->m_link["href"]; }

    /**
    * Sethref.
    * @param mixed $value
    */

    public function sethref($value){$this->m_link["href"] = $value;}

    /**
    * .ctr
    * @param null|mixed $link
    */
    public function __construct($link=null)
	{
		parent::__construct("div");
		$this["class"]="pane-view-groupitem";
		$this->m_link =  HtmlNode::CreateWebNode("a");
		$this->m_link["href"] = $link;
		parent::_AddChild($this->m_link,null);
	}

    /**
    * Adds Block.
    * @param null|mixed $attributes
    */

    public function addBlock($attributes=null)
	{
		$t = $this->m_link->Add("div", array("class"=>"pane-view-block"));
		$t->setAttributes($attributes);
		return $t;
	}

    /**
    * Add child.
    * @param mixed $item
    * @param null|mixed $index
    */

    protected function _addChild($item,$index=null)
	{//remove access to add list
		return false;
	}

    /**
    * Sets Block Class.
    * @param mixed $class
    */

    public function setBlockClass($class)
	{
		$t = $this->m_link->getElementsByTagName("div");
		if (is_array($t))
		{
			foreach($t as $v){
				$v->setClass($class);
			}
		}
	}
}

/**
* Igkwin ui pane viewgroup.
*/
class IGKWinUI_paneViewgroup extends HtmlNode
{

    /**
    * Property: title.
    * @var mixed
    */
    private $m_title; //group name

    /**
    * Returns Name.
    */

    public function getName(){return $this->m_title->Content;}

    /**
    * Sets Name.
    * @param mixed $value
    */

    public function setName($value){return $this->m_title->Content = $value; }

    /**
    * .ctr
    */
    public function __construct()
	{
		parent::__construct("div");
		$this["class"]="pane-view-group";
		$this->m_title =  HtmlNode::CreateWebNode("div");
		parent::_AddChild($this->m_title);
	}

    /**
    * Add child.
    * @param mixed $item
    * @param null|mixed $index
    */

    protected function _addChild($item, $index=null){
		if (get_class($item) == "IGKWinUI_paneViewitem")
		{
			$t =  parent::_AddChild($item,$index);
			return true;
		}
		return false;
	}

    /**
    * Adds Item.
    * @param null|mixed $link
    */

    public function addItem($link=null){
		$p = new IGKWinUI_paneViewitem($link);
		return $this->Add($p);
	}
} 