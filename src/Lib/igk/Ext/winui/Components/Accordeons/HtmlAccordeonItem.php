<?php

use IGK\System\Html\Dom\HtmlNode;

require_once __DIR__."/AccordeonCookiePanel.php";


////<summary>represent an accordeon html item</summary>
final class HtmlAccordeonItem extends HtmlNode
implements IIGKHtmlCookieItem
{
	private $m_CookieId;
	private $m_panCount;
	private $m_script;
	public function getCookieId(){return $this->m_CookieId; }
	public function setCookieId($v){ $this->m_CookieId = $v; return $this;}

	public function __construct(){
		parent::__construct("div");
		// igk-panel-group panel-group
		$this["class"] = "igk-accordeon";
		$this->setAttribute("igk-js-toggle-cookies", new IGKValueListener($this, "CookieId"));
		$this->m_script = igk_createnode("balafonJS");
		$this->m_script->Content = "if (igk.winui.accordeon)igk.winui.accordeon.init();";
	}
	protected function __getRenderingChildren($o=null){
		$s = parent::__getRenderingChildren($o);
		if ($this->m_script)
			$s[] = $this->m_script;
		return $s;
	}
	public function initDemo($t){

		// igk_die("kljb");


		$t->addCode()->Content = <<<EOF

	//add accordeon
	\$a = igk_createnode('accordeon');
	\$a->addPanel("title 1", "content for panel1", true);
	\$a->addPanel("title 2", "content for panel2", false);
	\$a->addPanel("title 3", "content for panel3", false);

EOF;


	}
	public function addPanel($title, $content, $active=false)
	{
		$d = $this->addDiv();
		$d->setClass("igk-panel");
		$h = $d->addDiv()
		->setClass("igk-panel-heading")		;
		$m = $h->addDiv()//A("#")
		//->setAttribute("igk-js-toggle","{parent:'^.igk-panel', target:'.igk-c', data:'igk-collapse'}")
		->setAttribute("igk-js-toggle-cookies", new IGKValueListener(new AccordeonCookiePanel($this, $this->m_panCount), "CookieId"));
		$m->Content = $title;

		//$active
		$d->addDiv()
		->setClass( (!$active ? "igk-collapse" : ""). " igk-c in")
		->setAttribute("class", "igk-trans-all")
		->Content = $content;
		$this->m_panCount ++;
		return $d;
	}
}

