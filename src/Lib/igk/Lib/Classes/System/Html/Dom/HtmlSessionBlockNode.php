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
use IGK\Server;
use IGKHtmlRelativeUriValueAttribute;
 
use IGKViewMode;
use function igk_resources_gets as __;


final class HtmlSessionBlockNode extends HtmlCtrlNode{
 
    public function getIsVisible()
    {
        return Server::IsLocal() || igk_environment()->isDev();
    }
    
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function _acceptRender($options = null):bool{  
        return $this->getIsVisible();     
    }
    ///<summary></summary>
    private function __buildview($t){ 
        $t->addObData(function(){
            $cnf_=igk_getctrl(IGK_CONF_CTRL);
            $cnf_view=igk_is_conf_connected();
            $_owner=igk_getctrl(IGK_SESSION_CTRL);
            $t = igk_create_node("div");
            $t["class"]="debugzone igk-session-block google-Roboto";
            $t->setIndex(10000);
            $d=igk_create_node("div");
        

            $d->addSectionTitle(4)->Content=__("Debug Panel");
            $ul=$d->add("ul");
            $ul->setClass("debug-panel google-Roboto");
            $v_btn_class="igk-btn igk-btn-default";
            if(!igk_get_env("sys://error")){
                $ul["class"]="session btn-group action-group"; 
                $ul->li()->abtn($_owner->getUri("forceview"))
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("ForceView");
                $ul->li()->addAClearSAndReload()
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("ClearSession");
                if(igk_server_is_local() && ($sess_ctrl=igk_getctrl(IGK_SESSION_CTRL, false))){
                    $ul->li()->abtn($sess_ctrl->getUri("clearAllSession"))
                    ->activate('igk-app-action')
                    ->setClass($v_btn_class)->Content=__("Clear All Session");
                }
            }
            if(igk_server_is_local()){
                $ul->li()->a($cnf_->getUri("clearLogs"))
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("Clear Logs");
            }
            if($cnf_view)
                $ul->li()->abtn($cnf_->getUri('logout'))
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("Logout");
            if(igk_app()->getCurrentPageFolder() != "Configs"){
                $ul->li()->abtn(new IGKHtmlRelativeUriValueAttribute("/Configs"))
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("Configure");
            }
            $ul->li()->abtn(new IGKHtmlRelativeUriValueAttribute(IGK_BASE_DIR."/"))
            ->activate('igk-app-action')
            ->setClass($v_btn_class)->Content=__("Homepage");
            if(Server::IsLocal() || $cnf_view || !igk_sys_env_production()){
                $ul->li()->abtn(igk_getctrl(IGK_SESSION_CTRL)->getUri("clearcache"))
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("Clear Cache");
            }
            if($api_ctrl=igk_getctrl("api", false)){
                $ul->addVisible("igk_is_conf_connected")->li()->addA($api_ctrl->getAppUri(""))
                ->activate('igk-app-action')
                ->setClass($v_btn_class)->Content=__("API");
            }
            $ul=$d->add("ul")->setId("cnf-inf");
            $ul->li()->Content="Referer : ". igk_server()->REMOTE_ADDR;
            $ul->li()->Content="PHP VERSION : ". PHP_VERSION;
            $ul->li()->Content="CurrentLang : ". R::GetCurrentLang();
            $ul->li()->Content="CurrentPage : ". igk_app()->CurrentPage;
            $ul->li()->Content="CurrentFolder : ". igk_app()->getCurrentPageFolder();
            $ul->li()->Content="ViewMode : ". IGKViewMode::GetSystemViewMode();
            $ul->li()->Content="Environment : ". array("development", "production")[igk_sys_env_production()];
            if ($_id = session_id()){
                $ul->li()->Content="SessionID : ". $_id;
            }
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
                $li=$v_logul->li();
                $li->add("label")->setAttribute("for", "clAdmLogin")->Content=__("Login");
                $input=$li->addInput("clAdmLogin");
                $input["autocomplete"]="off";
                $input["placeholder"]=__("tip.login");
                $input["autofocus"]=true;
                $input->setClass("igk-form-control");
                $li=$v_logul->li();
                $li->addLabel()->Content=__("Password");
                $i=$li->addInput("clAdmPwd", "password");
                $i["placeholder"]=__("tip.password");
                $i["autocomplete"]="current-password";
                $i->setClass("igk-form-control");
                $i=$v_logul->li()->addInput("btn.connect", "submit", __("btn.connect"));
                if($bootstrap){
                    $i->setClass("btn btn-default igk-btn igk-btn-default");
                }
            }
            else{
                $div=$t->div()->setClass("igk-form-group");
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
                $t->div()->setClass("igk-cleartab");
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

    protected function _getRenderingChildren($options = null)
    { 
        // $v = parent::_getRenderingChildren();
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
