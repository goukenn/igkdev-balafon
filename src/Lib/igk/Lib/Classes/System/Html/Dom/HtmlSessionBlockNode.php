<?php
// @file: IGKHtmlSessionBlockNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

use IGK\Controllers\SessionController;
use IGK\Resources\R;
use IGKHtmlRelativeUriValueAttribute;
use IGKServer;
use IGKViewMode;
use function igk_resources_gets as __;


final class HtmlSessionBlockNode extends HtmlCtrlNode{
 
    public function getIsVisible()
    {
        return IGKServer::IsLocal() || igk_environment()->is("DEV");
    }
    
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __AcceptRender($o=null){ 
        return true;

        // if(!$this->getIsVisible() || !$o || !$o->Document){
        //     return false;
        // }
        // $key=".@".get_class($this)."/rendered";
        // if(isset($o->{$key}) && $o->{$key}){
        //     return false;
        // }
        // $o->{$key}=1;
        // if(igk_xml_is_mailoptions($o) || igk_xml_is_cachingrequired($o))
        //     return false;
        // $this->clearChilds();



        // $v=$this->__buildview();
        // $t=$this;
        // $t->addNodeCallback("mem_usage", function($t){
        //     return $t->memoryusageinfo();
        // });
        // $t->addJSReadyScript('igk.ctrl.sessionblock.init');
        return true;
    }
    ///<summary></summary>
    private function __buildview($t){
        $t->addObData(function(){
            $cnf_=igk_getctrl(IGK_CONF_CTRL);
            $cnf_view=igk_is_conf_connected();
            $_owner=igk_getctrl(IGK_SESSION_CTRL);
            $t = igk_createnode("div");
            $t["class"]="debugzone igk-session-block google-Roboto";
            $t->setIndex(10000);
            $d=igk_createnode("div");
        

            $d->addSectionTitle(4)->Content=__("Debug Panel");
            $ul=$d->add("ul");
            $ul->setClass("debug-panel google-Roboto");
            $v_btn_class="btn btn-default igk-btn igk-btn-default";
            if(!igk_get_env("sys://error")){
                $ul["class"]="btn-group action-group"; 
                $ul->addLi()->add("a", array("href"=>$_owner->getUri("forceview")))->setClass($v_btn_class)->Content=__("ForceView");
                $ul->addLi()->addAClearSAndReload()->clearClass()->setClass($v_btn_class)->Content=__("ClearSession");
                if(igk_server_is_local() && ($sess_ctrl=igk_getctrl(IGK_SESSION_CTRL, false))){
                    $ul->li()->a($sess_ctrl->getUri("clearAllSession"))->setClass($v_btn_class)->Content=__("Clear All Session");
                }
            }
            if(igk_server_is_local()){
                $ul->li()->a($cnf_->getUri("clearLogs"))->setClass($v_btn_class)->Content=__("Clear Logs");
            }
            if($cnf_view)
                $ul->addLi()->add("a", array("href"=>$cnf_->getUri('logout')))->setClass($v_btn_class)->Content=__("Logout");
            if(igk_app()->getCurrentPageFolder() != "Configs"){
                $ul->addLi()->add("a", array("href"=>new IGKHtmlRelativeUriValueAttribute("/Configs")))->setClass($v_btn_class)->Content=__("Configure");
            }
            $ul->addLi()->add("a", array("href"=>new IGKHtmlRelativeUriValueAttribute(IGK_BASE_DIR."/")))->setClass($v_btn_class)->Content=__("Homepage");
            if(IGKServer::IsLocal() || $cnf_view || !igk_sys_env_production()){
                $ul->addLi()->add("a", array("href"=>igk_getctrl(IGK_SESSION_CTRL)->getUri("clearcache")))->setClass($v_btn_class)->Content=__("Clear Cache");
            }
            if($api_ctrl=igk_getctrl("api", false)){
                $ul->addVisible("igk_is_conf_connected")->addLi()->addA($api_ctrl->getAppUri(""))->setClass($v_btn_class)->Content=__("API");
            }
            $ul=$d->add("ul")->setId("cnf-inf");
            $ul->li()->Content="Referer : ". igk_server()->REMOTE_ADDR;
            $ul->li()->Content="PHP VERSION : ". PHP_VERSION;
            $ul->li()->Content="CurrentLang : ". R::GetCurrentLang();
            $ul->li()->Content="CurrentPage : ". igk_app()->CurrentPage;
            $ul->li()->Content="CurrentFolder : ". igk_app()->CurrentPageFolder;
            $ul->li()->Content="ViewMode : ". IGKViewMode::GetSystemViewMode();
            $ul->li()->Content="Environment : ". array("development", "production")[igk_sys_env_production()];
            // $ul->li()->Content=new IGKSessionIdValue();
            $bootstrap=igk_sys_getconfig("BootStrap.Enabled");
            $tab=igk_sys_debug_components();
            if($tab){
                foreach($tab as $k=>$v){
                    $v($this, $_owner);
                }
            }
            $current_page = igk_app()->getCurrentPageFolder();
            if(!$cnf_view && ($current_page != IGK_CONFIG_PAGEFOLDER)){
                $logindiv=$t->add("div");
                $logindiv->setCallback("getIsVisible", igk_create_expression_callback("return igk_app()->CurrentPageFolder !== IGK_CONFIG_PAGEFOLDER;", null));
                $frm=$logindiv->addForm();
                $frm["action"]=igk_getconfigwebpagectrl()->getUri("connectToConfig");
                $frm["class"]="igk-debug-connect-form";
                igk_html_form_initfield($frm);
                $v_logul=$frm->addPanelBox()->add("ul");
                $li=$v_logul->addLi();
                $li->add("label")->setAttribute("for", "clAdmLogin")->Content=__("Login");
                $input=$li->addInput("clAdmLogin");
                $input["autocomplete"]="off";
                $input["placeholder"]=__("tip.login");
                $input["autofocus"]=true;
                $input->setClass("igk-form-control");
                $li=$v_logul->addLi();
                $li->addLabel()->Content=__("Password");
                $i=$li->addInput("clAdmPwd", "password");
                $i["placeholder"]=__("tip.password");
                $i["autocomplete"]="current-password";
                $i->setClass("igk-form-control");
                $i=$v_logul->addLi()->addInput("btn.connect", "submit", __("btn.connect"));
                if($bootstrap){
                    $i->setClass("btn btn-default igk-btn igk-btn-default");
                }
            }
            else{
                $div=$t->addDiv()->setClass("igk-form-group");
                if($cnf_view){
                    $sl=$div->addSelect("clViewMode")->setClass("igk-form-control");
                    $uri=$_owner->getUri("changeviewmode");
                    $sl["onchange"]="javascript: ns_igk.ajx.get('{$uri}&mode='+this.value,null, ns_igk.ajx.fn.replace_body); return false;";
                    $mode=igk_app()->getViewMode();
                    foreach(igk_get_class_constants("IGKViewMode") as $k=>$v){
                        $opt=$sl->add("option")->setContent($k)->setAttribute("value", $v);
                        if($v == $mode){
                            $opt["selected"]=true;
                        }
                    }
                }
                $t->addDiv()->setClass("igk-cleartab");
            }
            echo $d->render();
        }
        , IGK_HTML_NOTAG_ELEMENT);
    }
    private $callback_mem;
    ///<summary></summary>
    public function __construct(SessionController $controller){
        parent::__construct($controller, "div");
        $this->callback_mem = $this->addNodeCallback("mem_usage", function($t){
            return $t->memoryusageinfo();
        });
    }
    ///<summary></summary>
    public function onAppExit(){
        $app=igk_app();
        if(igk_is_ajx_demand() && $this->IsVisible && $app->Session->getRedirectTask('modview')){
            $this->renderAJX();
            $app->Session->{"modeview"}=null;
        }
    }

    protected function __getRenderingChildren($options = null)
    { 
        // $v = parent::__getRenderingChildren();
        $n = new HtmlNode("div");
        $this->__buildview($n);
        $v =[
            $n,
            $this->callback_mem,
            igk_html_node_jsreadyscript('igk.ctrl.sessionblock.init'),
        ];  
        return $v;
    }
}
