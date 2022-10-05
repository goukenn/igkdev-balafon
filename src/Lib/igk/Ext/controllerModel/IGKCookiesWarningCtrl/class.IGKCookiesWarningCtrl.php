<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.IGKCookiesWarningCtrl.php
// @date: 20220803 13:48:59
// @desc: 

//cookies browser

use IGK\Controllers\BaseController;

abstract class IGKCookiesWarningCtrl  extends \IGK\Controllers\ControllerTypeBase
{
	public function getCanAddChild(){return false;}
	protected function initComplete($context=null)
	{
		parent::initComplete($context);
		igk_trace();
		igk_exit();
		igk_js_load_script($this->App->Doc, dirname(__FILE__)."/".IGK_SCRIPT_FOLDER);
		$clname = ".".strtolower($this->Name);
		igk_css_regclass($clname, "{sys:posab,loct,fitw} overflow:hidden; min-height:32px; line-height:32px; text-align:center; vertical-align:middle; z-index:100; background-color:white;");
		igk_css_regclass($clname." span","line-height:1; font-size:0.8em;  display:inline-block; vertical-align:middle; ");
		igk_css_regclass($clname." .btn_close","[res:btn_close_warning]{sys:dispb,posab} top:50%; right:4px;margin-top:-12px; width:24px; height:24px;");
	}
	public function View() : BaseController
	{
		if (!isset($_COOKIE["igk-app-cookieswarning-inform"]) && $this->IsVisible)
		{
			$this->TargetNode->clearChilds();
			$t = $this->TargetNode;
			$t->add("span")->Content = R::ngets("msg.cookies.requirement");
			$d = $t->add("div", array("class"=>"btn_close"));
			$d->add("a", array("class"=>"fitw fith dispb", "onclick"=>"window.igk.ctrl.cookieswarning.close('".$this->TargetNode["id"]."'); return false;"))->Content = IGK_HTML_SPACE;

		}
		else{
			igk_html_rm($this->TargetNode);
		}
		return $this;
	} 
} 