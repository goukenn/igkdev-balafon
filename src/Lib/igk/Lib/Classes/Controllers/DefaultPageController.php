<?php
// @file: IGKDefaultPageController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IIGKUriActionRegistrableController;
use IIGKWebPageController;

abstract class DefaultPageController extends PageControllerBase implements IIGKUriActionRegistrableController, IIGKWebPageController{
    ///<summary></summary>
    public function __construct(){
        parent::__construct();
    }
    ///<summary>include current view</summary>
    protected function _renderViewFile($view=null){
        igk_wln(__METHOD__, "data \n");
        extract($this->getSystemVars());
        $view=$view ? $view: $this->getCurrentView();
        if(file_exists($view)){
            $f=$view;
        }
        else
            $f=$this->getViewFile($view);
        if(file_exists($f)){
            ob_start();
            $this->_include_file_on_context($f);
            $g=ob_get_contents();
            ob_end_clean();
            if(!empty($g)){
                $t->notagnode()->Content=$g;
            }
        }
        else{
            igk_debug_wln("Current view file does't exists : ".$f);
        }
    }
    ///default handle uri global uri
    public static function CheckBeforeAddControllerInfo($request){
        $g=igk_getv($request, "clDefaultPage");
        if(empty($g))
            return false;
        return 1;
    }
    ///<summary>used to evaluatue query expression</summary>
    ///<param name="xml">show error as xml if not handled</param>
    ///<param name="nav">demand to render or not the current document</param>
    public final function evaluateUri($patterninfo=null, $xml=true, $nav=null){
        $this->setEnvParam("from", __FUNCTION__);
        $t=$this->TargetNode;
        igk_html_rm($t);
        $inf=$patterninfo ? $patterninfo: igk_sys_ac_getpatterninfo();
        $nav=$nav ?? !igk_get_env("sys://no_render");
        if(!$inf || !is_object($inf)){
            igk_ilog_assert(igk_is_debug(), "pattern info not found or info not an object", __METHOD__);
            return;
        }
        $v_tp=$inf->getQueryParams();
        $c=igk_getv($v_tp, "function");
        $p=igk_getv($v_tp, "params");
        $q=igk_getv($v_tp, "query");
        include(IGK_LIB_DIR."/Inc/igk_sitemap.pinc");
        $do_rendering=function($t, $doc, $nav){
            if(igk_is_ajx_demand()){
                igk_ajx_replace_node($t);
            }
            else{
                igk_render_node($t, $doc, $nav);
            }
            if($nav){
                igk_exit();
            }
        };
        $doc=$this->Doc;
        $doc->TempTheme->resetAll();
        if(empty($c)){
            $this->View();
        }
        else{
            $priority=igk_get_env("sys://viewpriority", 1);
            switch($priority){
                case 1:
                igk_io_locate_view_file($this, $c, $p);
                if($p == null)
                    $p=array();
                else if(is_array($p) == false)
                    $p=array($p);
                if(method_exists($this, $c) && $this->IsFuncUriAvailable($c)){
                    call_user_func_array(array($this, $c), $p);
                }
                else{
                    if(file_exists($f=$this->getViewFile($c))){
                        $g=substr($f, strlen($this->getViewDir()) + 1);
                        if(!preg_match("#^". $c."#", $g) || ((($ext=igk_io_path_ext($c)) != $c) && !preg_match("/phtml$/", $ext))){
                            array_unshift($p, $c);
                        }
                        $this->getView($f, false, $p, $q);
                        $v_c=1;
                    }
                    else{
                        igk_wln_e("view file not present . 404");
                    }
                }
                $do_rendering($t, $doc, $nav);
                return;
                case 2:
                break;
            }
            $v_c=0;
            if(preg_match("/(\.(".IGK_VIEW_FILE_EXT_REGEX."))?$/i", $c)){
                $f=$this->getViewFile($c);
                if(file_exists($f)){
                    $this->getView($c, false, $p, $q);
                    $v_c=1;
                }
            }
            if($v_c == 0){
                if($this->IsFuncUriAvailable($c)){
                    if($p == null)
                        $p=array();
                    else if(is_array($p) == false)
                        $p=array($p);
                    call_user_func_array(array($this, $c), $p);
                }
                else{
                    if($xml){
                        ob_clean();
                        igk_html_output(404);
                        header("HTTP/1.0 404 Not Found");
                        $r=new IGKXmlNode("result_evaluation_uri");
                        $r->add("error")->Content="IGK_ERR_FUNCNOTAVAILABLE";
                        $r->add("msg")->Content="Function not available ";
                        $r->add("function")->Content=$c;
                        if(IGKViewMode::IsWebMaster()){
                            $c=$r->add("info");
                            $c->add("icode")->Content=igk_getv($_REQUEST, "code");
                            $c->add("ctrl")->Content=$this->Name;
                        }
                        if(!igk_sys_env_production()){
                            $data=igk_createnode("ObData", null, array(function(){
                                        igk_show_serverinfo();
                                    }));
                            $r->add($data);
                        }
                        $r->renderAJX();
                        igk_exit();
                    }
                    $f=$this->getDeclaredDir()."/Contents/404.php";
                    if(file_exists($f)){
                        include($f);
                        igk_exit();
                    }
                    igk_io_check_request_file($inf->uri);
                    if(igk_is_ajx_demand()){
                        igk_wl("<response><message>method not found [".$c."]</message></response>");
                    }
                    else{
                        $m="method not exists ".$c. " ".IGK_EVALUATE_URI_FUNC." ".$inf->uri;
                        igk_notifyctrl()->addError($m);
                        igk_navto($this->getAppUri());
                    }
                    if($nav)
                        igk_exit();
                    return true;
                }
            }
        }
        $do_rendering($t, $doc, $nav);
        return true;
    }
    ///<summary></summary>
    public static function GetAdditionalConfigInfo(){
        return array(
            "clDefaultPage"=>igk_createadditionalconfiginfo(array("clRequire"=>1, "clDefaultValue"=>"default")),
            IGK_CTRL_CNF_BASEURIPATTERN=>(object)[]
        );
    }
    ///get the name of the page that control this controller
    public function getBasicUriPattern(){
        return igk_getv($this->Configs, IGK_CTRL_CNF_BASEURIPATTERN);
    }
    ///<summary></summary>
    public function getExtraTitle(){
        if(igk_web_defaultpage() != $this->CurrentPage)
            return " - ".__("title.".$this->CurrentPage.".webpage");
        return IGK_STR_EMPTY;
    }
    ///<summary></summary>
    public function getIsVisible(){
        if(igk_sys_is_subdomain() && igk_sys_domain_control($this)){
            return true;
        }
        $cp=$this->CurrentPageFolder;
        $cnf=igk_app()->Configs;
        $v=($cp != IGK_CONFIG_MODE) && (strtolower($cnf->default_controller) == strtolower($this->Name));
        return $v;
    }
    ///<summary></summary>
    public function getRegInvokeUri(){
        return $this->getUri(IGK_EVALUATE_URI_FUNC);
    }
    ///<summary></summary>
    public function getRegisterToViewMecanism(){
        return true;
    }
    ///<summary></summary>
    public function getRegUriAction(){
        $primary=$this->getBasicUriPattern();
        if(empty($primary))
            return null;
        return "".$primary.IGK_REG_ACTION_METH;
    }
    ///<summary>handle evaluation uri</summary>
    ///<return>true if handle uri or false</return>
    public function handle_redirection_uri($uri, $forcehandleuri=1){
        igk_sys_handle_uri();
        $k=IGK_REG_ROUTE_PATTERN;
        $pattern=igk_pattern_matcher_get_pattern($k);
        $p=igk_pattern_get_matches($pattern, $uri, array("function"));
        $c=igk_getv($p, "function");
        if(!preg_match($pattern, $uri))
            return false;
        $e=new IGKSystemUriActionPatternInfo(array(
            "action"=>$k,
            "value"=>$this->getRegInvokeUri(),
            "pattern"=>$pattern,
            "uri"=>$uri,
            "ctrl"=>$this,
            "keys"=>igk_str_get_pattern_keys($k)
        ));
        $this->evaluateUri($e);
        return true;
    }
    ///<summary></summary>
    protected function InitComplete(){
        parent::InitComplete();
        igk_app()->Session->addUserChangedEvent($this, "View");
    }
    ///<summary></summary>
    ///<param name="doc"></param>
    protected function initDocument($doc){
        $f=$this->getDataDir()."/".IGK_RES_FOLDER."/Img/favicon.ico";
        if(file_exists($f)){
            $p=igk_io_basepath($f);
            $doc->Favicon=new IGKHtmlRelativeUriValueAttribute($p);
        }
    }
    ///<summary></summary>
    protected function initTargetNode(){
        $t=parent::initTargetNode();
        $k=IGK_CSS_DEFAULT_STYLE_FUNC_KEY;
        $t->setCallback($k, "return 'igk-page';");
        $t->setClass("+".$t->$k());
        return $t;
    }
    ///<summary></summary>
    ///<param name="uri" default="null"></param>
    public function is_handle_uri($uri=null){
        if(igk_const('IGK_REDIRECTION') == 1){
            if(preg_match("#^/!@#", igk_io_request_uri()))
                return false;
        }
        if(!defined("IGK_REDIRECTION") && !igk_get_env("sys://io_invoke_uri")){
            return false;
        }
        $uri=$uri == null ? igk_io_base_request_uri(): $uri;
        $k=IGK_REG_ROUTE_PATTERN;
        $pattern=igk_pattern_matcher_get_pattern($k);
        $p=igk_pattern_get_matches($pattern, $uri, igk_str_get_pattern_keys($k));
        $c=igk_getv($p, "function");
        return false;
    }
    ///<summary> get if a function is avaible for uri invocation</summary>
    public function IsFuncUriAvailable(& $m){
        $k=$m;
        if(!method_exists($this, $k)){
            $k="uri_".$k;
            if(!method_exists($this, $k))
                $k=null;
        }
        if($k !== null){
            $m=$k;
            return true;
        }
        return false;
    }
    ///<summary></summary>
    public function LoadTemplate(){
        $tempfile=igk_getr("tempfile");
        $this->saveCtrl();
        $this->View();
    }
    ///<summary></summary>
    ///<param name="file"></param>
    public function loadWebTheme($file){    }
    ///<summary></summary>
    ///<param name="uri"></param>
    public function manageErrorUriRequest($uri){    }
    ///<summary></summary>
    protected function OnMenuPageChanged(){
        $this->View();
    }
    ///<summary></summary>
    public function pageFolderChanged(){
        if($this->IsVisible)
            $this->View();
    }
    ///<summary></summary>
    public function restoreCtrl(){
        $f=$this->getDeclaredDir()."/.".$this->Name.".bck.zip";
        if(file_exists($f)){
            igk_zip_unzip($f, $this->getDeclaredDir());
            $this->View();
            unlink($f);
        }
    }
    ///<summary></summary>
    public function saveCtrl(){
        igk_zip_create_file($this->getDeclaredDir()."/.".$this->Name.".bck.zip", $this->getDeclaredDir());
    }
    ///<summary></summary>
    ///<param name="t" ref="true"></param>
    public static function SetAdditionalConfigInfo(& $t){
        $t["clDefaultPage"]=igk_getr("clDefaultPage");
        return 1;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setPageName($value){
        $this->m_pageview=$value;
    }
    ///<summary></summary>
    public function View(){
        $t=$this->TargetNode;
        $doc=igk_app()->Doc;
        $menu_ctrl=igk_getctrl(IGK_MENU_CTRL);
        if(!$menu_ctrl || !$this->getIsVisible()){
            igk_html_rm($t);
            return;
        }
        else if($this->getEnvParam("from") == null){
            igk_html_add($t, $doc->body->addBodyBox()->ClearChilds());
        }
        $menu_ctrl->setParentView($this->menu_content);
        $this->doc->Title=$this->m_Title ? $this->m_Title: igk_app()->Configs->website_title. $this->getExtraTitle();
        $this->initDocument($this->doc);
        $this->_initView();
        $c=strtolower(igk_getr("c", null));
        $view=$this->getCurrentView();
        if($c == strtolower($this->Name)){
            $view=igk_getr("v", $view);
        }
        $this->m_init=true;
        $this->_renderViewFile($view);
        if(!$this->ShowChildFlag){
            $this->_showChild(null);
        }
        $this->_onViewComplete();
    }
}
