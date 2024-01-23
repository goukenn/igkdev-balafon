<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.winui.horizontalpane.php
// @date: 20220803 13:48:58
// @desc: 

use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\ViewHelper;
use IGK\Resources\R;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlRegistrableComponentBase;
use IGK\System\Html\HtmlNodeType;
use IGK\System\Html\HtmlReader;



require_once __DIR__. "/func.helper.pinc";
require_once __DIR__. "/HorizontalPaneInfoBox.php";
require_once __DIR__. "/HorizontalPaneManager.pinc";
require_once __DIR__. "/HorizontalAnimType.pinc"; 
require_once __DIR__. "/JSHorizontalPane.pinc"; 
require_once __DIR__. "/HorizontalPage.pinc"; 
 

///<summary>external html item. Add to View</summary>
final class HorizontalPaneItem extends HtmlRegistrableComponentBase
{
	private $m_pane;
	private $m_pagelistener;
	private $m_manager;
	private $m_infobox;
	private $m_infoboxScript;

	private $m_pattern;
	private $m_folder;
	private $m_ConfigFileName;
	private $m_ctrl; 

	/**
	 * get binded controller
	 * @return mixed 
	 */
	public function getCtrl(){
		return $this->m_ctrl;
	}
	public function getFolder()
	{
		return $this->m_folder;
	}
	public function setFolder($v)
	{
		$this->m_folder = IO::GetDir($v);
	}

	public function getConfigFileName()
	{
		return $this->m_ConfigFileName;
	}
	public function setConfigFileName($v)
	{
		$this->m_ConfigFileName = $v;
	}

	public function getPattern()
	{
		return $this->m_pattern;
	}
	public function setPattern($v)
	{
		$this->m_pattern = $v;
	}

	public function getPane()
	{
		return $this->m_pane;
	}
	public function __toString()
	{
		return __CLASS__;
	}

	public function getHasPage()
	{
		return $this->m_pane->pageNode->HasChilds;
	}

	public function loadingComplete()
	{
		$this->configure();
		$this->flush();
	}
	///<summary>call it first</summary>
	public function setCtrl($ctrl, $folder = null)
	{
		$this->clearPages();
		$this->Folder = $folder == null ? $ctrl->getDataDir() . "/R/barner" : $folder;
		$this->configure();
		$this->flush();
		$this->m_ctrl = $ctrl;
		return $this;
	}
	private function loadConfigSetting()
	{
		$f = $this->Folder . "/" . $this->ConfigFileName;

		if (!file_exists($f))
			return;
		$div =  HtmlReader::LoadFile($f);
		$d = igk_getv($div->getElementsByTagName("config"), 0);
		if ($d) {
			foreach ($d->Childs as $k) {

				if ($k->getType() == HtmlNodeType::Text)
					continue;
				$r = $k->TagName;
				$this->Pane->$r = trim($k->getInnerHtml());
			}
		}
	}

	public function storeDBConfigsSetting()
	{
		$f = $this->Folder . "/" . $this->ConfigFileName;
		$d = HtmlNode::CreateWebNode("config");
		//store

		$d->add("AnimType")->Content = $this->m_pane->AnimType;
		$d->add("AnimInterval")->Content = $this->m_pane->AnimInterval;
		$d->add("AnimPeriod")->Content = $this->m_pane->AnimPeriod;
		$d->add("AnimDuration")->Content = $this->m_pane->AnimDuration;
		$d->SaveToFile($f);
	}
	public function getOptionsXML($uri)
	{
		$d = HtmlNode::CreateWebNode("div");
		$d->add("li")->setAttributes(array("uri" => $uri . "&menu=option", "ajx" => 1, "complete" => "ns_igk.winui.horizontalScrollPane.append_to_body_from(this)"))->Content = "options";
		$d->addXmlNode("sep");
		foreach (igk_get_class_constants(HorizontalAnimType::class) as  $v) {
			$d->add("li")->setAttributeç($this->m_pane->AnimType == $v, "class", "+igk-checked")
				->setAttributes(array("uri" => $uri . "&menu=setanimtype&n=" . $v, "ajx" => 1))
				->Content = $v;
		}
		return $d->getinnerHTML();
	}
	public function EditPaneOptions($target)
	{
		if ($target == null)
			return;
		$pane = $this->m_pane;

		$s = <<<EOF
<igk:labelInput igk:id='AnimDuration' igk:value='{$pane->AnimDuration}' />
<igk:labelInput igk:id='AnimInterval' igk:value='{$pane->AnimInterval}'/>
<igk:labelInput igk:id='AnimPeriod' igk:value='{$pane->AnimPeriod}'/>
<igk:HSep />
<input type="submit" class="igk-btn igk-btn-default" value="[lang:btn.update]" / >
EOF;
		$target->Load(igk_html_databinding_treatresponse($s, null, null, null));
	}
	public function __construct()
	{
		parent::__construct("div");		
	}
	protected function initialize()
	{
		$this["class"] = "igk-hpane-container";
		$this->m_pane = new JSHorizontalPane($this);
		$this->m_pattern = "/\.phtml$/i";
		$this->m_manager = new HorizontalPaneManager($this);
		$this->m_infobox = new HorizontalPaneInfoBox(); //$this->div()->setClass("igk-pane-infobox");
		$this->m_pane->pageNode["igk-component-id"] = $this->m_manager["igk-component-id"];
		$this->add($this->m_manager);
		$this->m_infobox->setCallback("getIsVisible", igk_create_node_callback(array($this, "getIsVisible"), array('infobox')));
		$this->m_infobox->div()->setClass("igk-pane-infobox-c disptable fitw fith")
			->div()->setClass("disptabc alignc alignm")
			->Content = R::ngets("msg.nopageaddedtopane");
		$this->m_infoboxScript = $this->m_infobox->addScript();
		$this->m_ConfigFileName = "config.xml";
	}
	///mage all visibility
	public function isVisible($n, $t)
	{

		$u = igk_app()->session->User;
		switch ($t) {
			case "infobox":
				return !$this->HasPage && (IGKViewMode::IsSupportViewMode(IGKViewMode::WEBMASTER) || ($u && $u->auth("sys://designpage")));

			case "option":
				return (IGKViewMode::IsSupportVIewMode(IGKViewMode::WEBMASTER) || ($u && $u->auth('sys://designpage'))) && ($this->Folder != null);
		}
		return false;
	}
	public function loadData($data)
	{
		// igk_ilog("load data");
		// $data->renderAJX();
		// igk_wln($data->getElementsByTagName("page"));

		foreach ($data->getElementsByTagName("page") as $e) {
			$file = $e["file"];
			$p = $this->addPage();
			igk_html_bind_target(null, $p, $e->getInnerHtml()(), (object)array("file" => $file));
			$p->setFile($file);

			if (IGKViewMode::IsSupportViewMode(IGKViewMode::WEBMASTER)) {
				$c = igk_html_article_options(null, $p, $file);
				if ($c) {
					$uri = $this->m_manager->getController()->getUri("dropFile&n=" . base64_encode($file), $this->m_manager);
					$c->dropFileUri =
						"javascript: ns_igk.readyinvoke('igk.winui.horizontalScrollPane.dropfile_ajx', this, '{$uri}');";
				}
			}
		}
	}
	protected function _acceptRender($options = null):bool
	{
		if (!$this->IsVisible)
			return false;

		$p = $this->getPageViewListener();
		$uri = null;
		//$u = igk_app()->session->User;
		$u = igk_app()->session->User;

		$v = IGKViewMode::IsSupportViewMode(IGKViewMode::WEBMASTER) || ($u && $u->auth("sys://designpage"));
		if ($v) {
			if ($p) {
				$uri = $p->getAddHorizontalPageUri();
			} else {
				$uri = $this->m_manager->getController()->getUri("addUri", $this->m_manager);
			}
			$this['igk-horizontal-pane-draggable'] = 1;
// 			$this->m_infoboxScript->Content = <<<EOF
// ns_igk.readyinvoke('igk.winui.horizontalScrollPane.initdrag','{$uri}');
// EOF;
			$this->m_infoboxScript->setIsVisible(false);
		} else {
			$this->m_infoboxScript->Content = null;
			$this->m_infoboxScript->setIsVisible(false);
		}
		return true;
	} 
	public function setPageViewListener($listener)
	{
		if (($listener == null) || !igk_reflection_class_implement($listener, IIGKHorizontalPaneListener::class))
			igk_die("listener is not a valid value ");
		$this->m_pagelistener = $listener;
	}
	public function getPageViewListener()
	{
		return $this->m_pagelistener;
	}
	public function flush(){
		$this->m_pane->flush(); 
	}
	public function addPage($attributes = null)
	{
		return $this->m_pane->addPage($attributes);
	}
	public function clearPages()
	{
		$this->m_pane->Clear();
	}
	public function configure(
		$AnimDuration = 500,
		$AnimInterval = 20,
		$AnimPeriod = 25000,
		$IsAutoAnimate = true,
		$AnimType = JSHorizontalPane::DEFAULTANIMTYPE // "rotation" //"translation"/// fade, rotation
	) {
		// igk_ilog("configure page ");
		$this->m_pane->AnimDuration = $AnimDuration;
		$this->m_pane->AnimInterval = $AnimInterval;
		$this->m_pane->AnimPeriod = $AnimPeriod;
		$this->m_pane->IsAutoAnimate = $IsAutoAnimate;
		$this->m_pane->AnimType = $AnimType;

		if ($this->m_pagelistener != null) {
			$this->m_pagelistener->buildPages($this);
		} else {

			$data = igk_create_xmlnode("horizontal-pane-data"); // HtmlNode::CreateWebNode("horizontal-pane-data");
			$dir = $this->Folder;
			$p = $this->Pattern;
			if ($dir && IO::CreateDir($dir)) {

				IO::WriteToFileAsUtf8WBOM($dir . "/.htaccess", "allow from all", false);

				foreach (igk_io_getfiles($dir, $p, false) as  $v) {
					$v_p = $data->add("page");
					$v_p["file"] = $v;
					$v_p->LoadExpression(igk_io_read_allfile($v));
				}
				$this->loadData($data);
				$this->loadConfigSetting();
			}
		}
	}
	public static function InitComponent($doc, BaseController $ctrl=null){
		$ctrl = $ctrl ?? ViewHelper::CurrentCtrl(); 
		$doc->addTempScript( __DIR__."/Scripts/igk.winui.horizontalScrollPane.js", ["v"=>IGK_VERSION])->activate('defer'); 
		if (igk_environment()->isOPS()){ 
			CssUtils::InjectStyleContent($doc, __DIR__."/Styles/default.pcss");	 
		}
	}
}
