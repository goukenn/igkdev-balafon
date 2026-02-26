<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAccordeonItem.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\IHtmlCookieItem;
use IGK\System\Html\Dom\HtmlNode;
use IGK\ValueListener;

require_once __DIR__."/AccordeonCookiePanel.php";

/**
* Html accordeon item.
*/
final class HtmlAccordeonItem extends HtmlNode
implements IHtmlCookieItem
{

    /**
    * Identifier: cookie id.
    * @var mixed
    */
    private $m_CookieId;

    /**
    * Count: pan count.
    * @var mixed
    */
    private $m_panCount;

    /**
    * Property: script.
    * @var mixed
    */
    private $m_script;

    /**
    * Returns Cookie Id.
    */

    public function getCookieId(){return $this->m_CookieId; }

    /**
    * Sets Cookie Id.
    * @param mixed $v
    */

    public function setCookieId($v){ $this->m_CookieId = $v; return $this;}

    /**
    * .ctr
    */
    public function __construct(){
		parent::__construct("div");
		// igk-panel-group panel-group
		$this["class"] = "igk-accordeon";
		$this->setAttribute("igk-js-toggle-cookies", new ValueListener($this, "CookieId"));
		$this->m_script = igk_create_node("balafonJS");
		$this->m_script->Content = "if (igk.winui.accordeon)igk.winui.accordeon.init();";
	}

    /**
    * Get rendering children.
    * @param null|mixed $o
    */

    protected function _getRenderingChildren($o=null){
		$s = parent::_getRenderingChildren($o);
		if ($this->m_script)
			$s[] = $this->m_script;
		return $s;
	}

    /**
    * Initializes Demo.
    * @param mixed $t
    */

    public function initDemo($t){

		// igk_die("kljb");


		$t->addCode()->Content = <<<EOF

	//add accordeon
	\$a = igk_create_node('accordeon');
	\$a->addPanel("title 1", "content for panel1", true);
	\$a->addPanel("title 2", "content for panel2", false);
	\$a->addPanel("title 3", "content for panel3", false);

EOF;


	}

    /**
    * Adds Panel.
    * @param mixed $title
    * @param mixed $content
    * @param mixed $active
    */

    public function addPanel($title, $content, $active=false)
	{
		$d = $this->div();
		$d->setClass("igk-panel");
		$h = $d->div()
		->setClass("igk-panel-heading")		;
		$m = $h->div()//A("#")
		//->setAttribute("igk-js-toggle","{parent:'^.igk-panel', target:'.igk-c', data:'igk-collapse'}")
		->setAttribute("igk-js-toggle-cookies", new ValueListener(new AccordeonCookiePanel($this, $this->m_panCount), "CookieId"));
		$m->Content = $title;

		//$active
		$d->div()
		->setClass( (!$active ? "igk-collapse" : ""). " igk-c in")
		->setAttribute("class", "igk-trans-all")
		->Content = $content;
		$this->m_panCount ++;
		return $d;
	}
}

