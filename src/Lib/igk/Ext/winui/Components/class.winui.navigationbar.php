<?php
// @author: C.A.D. BONDJE DOUE
// @filename: class.winui.navigationbar.php
// @date: 20220803 13:48:58
// @desc: 


/*
* file: class.winui.navigationbar.php
* description : control that can't make navigation page control
* requirement : Scripts/winui/igk.winui.navigationbar.js
**/

$s  = igk_dir(IGK_LIB_DIR."/Scripts/winui/igk.winui.navigationbar.js");

// if (igk_io_file_exists($s) == false)
// {
	// igk_wln("base dir :  ".IGK_LIB_DIR);
	// igk_wln( "current working dir : ". getcwd());
	// igk_wln("files not found : ".$s);

	// return;
// }
if (defined("IGK_WINUI_NAVIGATIONBAR"))
	return;

define("IGK_WINUI_NAVIGATIONBAR",1);

/**
* auto generate doc.
*/
class IGKWinUINavigationBar extends  IGKWinUIControl
{
	private $m_target; //target that will host navigation bar
	private $m_scripts ;
	private $m_pages;
	private $m_ciblingCtrl;

    /**
    * auto generate doc.
    */
    public function getTarget(){return $this->m_target;}

    /**
    * auto generate doc.
    * @param mixed $target
    */
    public function setTarget($target) { $this->m_target  = $target;}

    /**
    * auto generate doc.
    */
    public function getCiblingCtrl(){return $this->m_ciblingCtrl;}

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setCiblingCtrl($value){ $this->m_ciblingCtrl = $value; }

	public function __construct()
	{
		parent::__construct("div");
		$this["class"] ="IGKWinUI";
		$this->m_scripts =  HtmlNode::CreateWebNode("script");
		$this->m_pages = array();


	}

    /**
    * auto generate doc.
    * @param null|mixed & $xmloptions
    */
    protected function innerHTML(& $xmloptions=null)
	{
		$o =  parent::innerHtml($xmloptions);
		if($this->m_ciblingCtrl){
		$ctrl = strtolower($this->m_ciblingCtrl->Name);
		$this->m_scripts->Content = <<<EOF
(function(node, parent){igk.ready(function(){ igk.winui.navigationbar.init(node, parent,  {duration:1000, interval:20, "orientation":"vertical"},false, false);});})(igk.getParentScript(), document.getElementById('{$ctrl}'));
EOF;
		$o .= $this->m_scripts->render();
		}
		return $o;
	}

	///<summary>add item buttuon</summary>
	///@@ $page: the page name
	///@@ $target : the controller target name

    /**
    * auto generate doc.
    * @param mixed $page
    * @param mixed $target
    */
    public function addPage($page, $target)
	{
		$t = $this->add("a", array(
		   "href"=>"#".strtolower($page),
		   "igk-navigation-target"=>$target));

		$t->Content = R::ngets("btn.".strtolower($page));
		$this->m_pages[] = $t;
	}

    /**
    * auto generate doc.
    */
    public function init()
	{

$p_content = $this->TargetNode->div()->setAttributes(array("class"=>"kms-page-content" ));

$v_d =  HtmlNode::CreateWebNode("div");
$v_d["style"] = "overflow:hidden";

igk_html_article($this , "default.phtml", $v_d);
$d = $v_d->getElementsByTagName("li");
$globalmenu = array();
if ($d)
{
	foreach($d as $k)
	{
	   $s =  $k->getInnerHtml();
	   $uri = $k["uri"];
	   $k->clearChilds();
	   //$k->Add("a", array("href"=>$this->getUri("navigate&n=".base64_encode($s))))->Content = $s;
	   $ctrl_name = strtolower("kms_".$s."ctrl");
	   if ( ($uri==null) &&  ( ($ctrl = igk_getctrl($ctrl_name, false)) !=null) && ($ctrl->webParentCtrl!=null) && ($ctrl->webParentCtrl->Name == "kms_defaultctrl"))
	   {
			$k->Add("a", array(
		   "href"=>"#".strtolower($s),
		   "igk-navigation-target"=>$ctrl_name))->Content=  R::ngets("btn.".strtolower($s));
		   $globalmenu[]  = $s;
	   }
	   else{
			if ($uri==null){
				$k->Add("a", array("href"=>$this->getUri("navigate&n=".$s), "class"=>"nofocus"))->Content = R::ngets("btn.".$s);
			}
			else{
				$k->Add("a", array("href"=>$uri, "target"=>"_blank", "class"=>"nofocus" ))->Content = R::ngets("btn.".$s);
			}
	   }
	}
	$v_d->script()->Content = <<<EOF
(function(node, parent){igk.ready(function(){ igk.winui.navigationBar.init(node, parent,  {duration:1000, interval:20, "orientation":"vertical"},false, false);});})(igk.getParentScript(), document.getElementById('kms_defaultctrl'));
EOF;
}
	}
} 