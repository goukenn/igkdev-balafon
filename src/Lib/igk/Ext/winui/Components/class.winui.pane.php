<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.winui.pane.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\System\Html\Dom\HtmlNode;

/**
* auto generate doc.
*/
class IGKWinUI_paneView extends IGKWinUIControl
{
	private $m_script;
	private $m_loadUri;

    /**
    * auto generate doc.
    * @param null|mixed $name
    */
    public function addGroup($name = null){
		$g = new IGKWinUI_paneViewgroup();
		$g->Name = $name;
		$this->Add($g);
		return $g;
	}
	public function __construct()
	{
		parent::__construct("div");
		$this["class"]="pane-view fitw .fith" ;
		$this->m_script =  HtmlNode::CreateWebNode("script");
		$this->m_script->Content = "igk.winui.paneview.init();";
	}

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    public function getloadUri(){return $this->m_loadUri;}

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setloadUri($value){$this->m_loadUri = $value; }

}

/**
* auto generate doc.
*/
class IGKWinUI_paneViewitem extends HtmlNode
{
	/** @var HtmlNode*/
	private $m_link;

    /**
    * auto generate doc.
    */
    public function gethref(){return $this->m_link["href"]; }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function sethref($value){$this->m_link["href"] = $value;}

	public function __construct($link=null)
	{
		parent::__construct("div");
		$this["class"]="pane-view-groupitem";
		$this->m_link =  HtmlNode::CreateWebNode("a");
		$this->m_link["href"] = $link;
		parent::_AddChild($this->m_link,null);
	}

    /**
    * auto generate doc.
    * @param null|mixed $attributes
    */
    public function addBlock($attributes=null)
	{
		$t = $this->m_link->Add("div", array("class"=>"pane-view-block"));
		$t->setAttributes($attributes);
		return $t;
	}

    /**
    * auto generate doc.
    * @param mixed $item
    * @param null|mixed $index
    */
    protected function _addChild($item,$index=null)
	{//remove access to add list
		return false;
	}

    /**
    * auto generate doc.
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
* auto generate doc.
*/
class IGKWinUI_paneViewgroup extends HtmlNode
{
	private $m_title; //group name

    /**
    * auto generate doc.
    */
    public function getName(){return $this->m_title->Content;}

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setName($value){return $this->m_title->Content = $value; }
	public function __construct()
	{
		parent::__construct("div");
		$this["class"]="pane-view-group";
		$this->m_title =  HtmlNode::CreateWebNode("div");
		parent::_AddChild($this->m_title);
	}

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @param null|mixed $link
    */
    public function addItem($link=null){
		$p = new IGKWinUI_paneViewitem($link);
		return $this->Add($p);
	}
} 