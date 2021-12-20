<?php
// @file: IGKSystemUriActionCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com


namespace IGK\System\Configuration\Controllers;

use IGK\Models\Systemuri;
use IGK\System\Html\HtmlRenderer;
use IGKSystemUriActionPatternInfo;
use IIGKUriActionListener;

final class IGKSystemUriActionCtrl extends ConfigControllerBase implements IIGKUriActionListener{
    //+ action routes
    const ROUTES=IGK_CUSTOM_CTRL_PARAM + 0x1;
    private static $sm_actions, $sm_routes;
    ///<summary></summary>
    ///<param name="key"></param>
    public function contains($key){
        $tab=$this->getRoutes();
        if(is_array($tab))
            return array_key_exists($key, $tab);
        return false;
    }
    ///<summary>invoke this method with curl service to dispathc message to controller</summary>
    public function dispatchMessage(){
        if(!igk_is_srv_request()){
            if(!igk_sys_env_production()){
                igk_ajx_toast("Can't dispatch message : not a local server request");
            }
            igk_exit();
        }
        $ctrl=igk_getctrl(igk_getr("ctrl"));
        $uri=igk_getr("uri");
        $param=igk_getr("param");
        $src=igk_getr("source");
        session_write_close();
        if($ctrl !== $this){
            $e=igk_sys_ac_create_pattern($ctrl, $uri);
            $cp=$e->getQueryParams();
            $bck=$_REQUEST;
            $_REQUEST=$param;
            $fc=igk_getv($cp, "function");
            $p=igk_getv($cp, "params");
            if($p && !is_array($p)){
                $p=array($p);
                $cp["params"]=$p;
            }
            igk_set_env("sys://no_render", 1);
            igk_set_env("sys://ajx_demand", 1);
            igk_set_env("sys://action/dispatchmessage", array("ctrl"=>1, "uri"=>$uri));
            if(method_exists($ctrl, $fc)){
                call_user_func_array(array($ctrl, $fc), is_array($p) ? $p: array($p));
            }
            else{
                $g=0;
                if(method_exists($ctrl, "OnMessage")){
                    call_user_func_array(array($ctrl, "OnMessage"), array($src, is_array($cp) ? $cp: array($cp)));
                    $g=1;
                }
                if(!$g){
                    igk_ajx_toast("Can't dispatch message : {$uri}");
                }
            }
            igk_set_env("sys://action/dispatchmessage", 0);
        }
        igk_exit();
    }
    ///<summary>get action list</summary>
    public function getActions(){
        return $this->getRoutes();
    }
    ///<summary></summary>
    public function getCanAddChild(){
        return false;
    }
    ///<summary></summary>
    public function getConfigPage(){
        return "systemuri";
    }
    ///<summary></summary>
    public function getDataTableInfo(){
        return null;
    }
    ///<summary></summary>
    public function getDataTableName(){
        return igk_db_get_table_name(IGK_TB_SYSTEMURI);
    }
    ///<summary></summary>
    public function getmailto(){
        igk_navto("mailto:bondje.doue@igkdev.com");
        igk_exit();
    }
    ///<summary></summary>
    public function getName(){
        return IGK_SYSACTION_CTRL;
    }
    ///<summary></summary>
    public function getPatternInfo(){
        return igk_get_env(IGK_ENV_URI_PATTERN_KEY);
    }
    ///<summary>get system uri actions routes</summary>
    ///<return refout="true"></return>
    public function & getRoutes(){
        if(self::$sm_routes == null){
            self::$sm_routes=array();
        }
        return self::$sm_routes;
    }
    ///<summary></summary>
    ///<param name="key" default="null"></param>
    public function getSystemUri($key=null){
        $tab=$this->getRoutes();
        if(isset($tab[$key]))
            return $tab[$key];
        return null;
    }
    ///<summary></summary>
    public function gotoconfig(){
        igk_navtocurrent("Configs");
    }
    ///<summary></summary>
    public function init_wakeup(){    }
    ///<summary></summary>
    ///<param name="ctrl" default="null"></param>
    private static function InitActionList($ctrl){
        igk_die("init action");
        if (defined("IGK_NO_WEB")){
            return;
        }
        self::$sm_actions=array();
        self::$sm_actions["^/config(.php)?$"]=$ctrl->getUri("gotoconfig");
        $conf_ctrl=igk_getconfigwebpagectrl();
        if($conf_ctrl){
            self::$sm_actions["^/Configs!Settings$"]=$conf_ctrl->getUri("configure_settings");
            $t=igk_get_env("sys://configs/options");
            if($t){
                foreach($t as $k=>$v){
                    self::$sm_actions["^/Configs!{$k}$"]=$conf_ctrl->getUri("configure&v=".$v);
                }
            }
            self::$sm_actions["^/reconnect$"]=$conf_ctrl->getUri("reconnect");
        }
        self::$sm_actions["/@!(.)+"]=$ctrl->getUri("invoke_action");
        self::$sm_actions["/clr$"]="?c=".IGK_SESSION_CTRL."&f=ClearS";
        self::$sm_actions["/run-cron$"]="?c=".IGK_SESSION_CTRL."&f=RunCron";
        self::$sm_actions["^/initsdb$"]="?c=".IGK_MYSQL_DB_CTRL."&f=pinitSDb";
        self::$sm_actions["^/getmailto$"]=$ctrl->getUri("getmailto");
        if($uctrl=igk_getctrl(IGK_USER_CTRL)){
            self::$sm_actions["^/connect$"]=$uctrl->getUri("connectpage");
            self::$sm_actions["^/signup$"]=$uctrl->getUri("signup");
            self::$sm_actions["^/users/begin_reset_pwd$"]=$uctrl->getUri("begin_pwd_reset");
            self::$sm_actions["^/users/logout$"]=$uctrl->getUri("logout_lnk");
        }
        if($sys_c=igk_getctrl(IGK_SYS_CTRL, false)){
            self::$sm_actions["^/sys_api/check_mod_rewrite$"]=$sys_c->getUri("mod_rewrite");
        }
        $route=& $ctrl->getRoutes();
        foreach(self::$sm_actions as $k=>$v){
            $route[$k]=$v;
        }
        // igk_wln_e(__FILE__.":".__LINE__, get_class($ctrl));
        $e=$ctrl->getDbEntries();
        if($e && ($e->RowCount > 0)){
            foreach($e->Rows as $k=>$v){
                if(is_object($v)){
                    $route[$v->clName]=$v->clUri;
                }
                else{
                    igk_wln("uri : object not register from db");
                    igk_wln($v);
                }
            }
        }
        return self::$sm_actions;
    }
   
    public function getDbEntries(){
        return []; 
    }
    ///<summary></summary>
    public function invoke_action(){
        $u=igk_getv($_SERVER, "REQUEST_URI");
        $c=preg_match_all("/^\/@!(?P<name>([^\/]+))(\/(?P<param>(.)+))?$/i", $u, $tab);
        if($c){
            $n=strtolower(igk_getv($tab, "name")[0]);
            $p=explode("/", igk_getv($tab, "param")[0]);
            switch($n){
                case "actions":
                $ke="sys://actions/scripts/".$p[0];
                $s=igk_get_env($ke);
                if($s && igk_is_callable($s)){
                    call_user_func_array($s, array_slice($p, 1));
                }
                break;default: 
                $ke="sys://".$n."/scripts/".$p[0];
                $s=igk_get_env($ke);
                if($s && igk_is_callable($s)){
                    call_user_func_array($s, array_slice($p, 1));
                }
                break;
            }
        }
        else{
            igk_ilog("expression not valid ".$u);
        }
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="pattern"></param>
    ///<param name="render" default="1"></param>
    public function invokeCtrlUriPattern($ctrl, $pattern, $render=1){
        if(igk_get_env("sys://call/".__METHOD__) == 1){
            igk_debug_wln("Invoke Ctrl Uri Pattern is not allowed");
        }
        igk_set_env("sys://call/".__METHOD__, 1);
        $this->setEnvParam("targetctrl", $ctrl);
        igk_set_env(IGK_ENV_URI_PATTERN_KEY, $pattern);
        igk_app()->getControllerManager()->InvokePattern($pattern);
        $this->setEnvParam("targetctrl", null);
        if($render){
            HtmlRenderer::RenderDocument();
        }
    }
    ///<summary></summary>
    ///<param name="type"></param>
    ///<param name="ctrl"></param>
    ///<param name="func"></param>
    ///<param name="args"></param>
    public function invokePageAction($type, $ctrl, $func, $args){
        switch($type){
            case "sys":
            $ctrl=igk_getctrl($ctrl);
            if($ctrl){
                $e=igk_sys_ac_create_pattern($ctrl, "/".$func."/".$args);
                $cp=$e->getQueryParams();
                $f=igk_getv($cp, "function");
                $args=igk_getv($cp, "params");
                if($ctrl->IsFunctionExposed($f)){
                    try {
                        call_user_func_array(array($ctrl, $f), is_array($args) ? $args: array($args));
                    }
                    catch(\Exception $ex){
                        igk_show_exception($ex);
                        igk_exit();
                    }
                }
                else{
                    igk_wln("not exposed");
                }
                igk_navto(igk_io_baseuri());
                igk_exit();
            }
            break;
            case "res":
            $uri=igk_server()->REQUEST_URI;
            $uri=substr($uri, 7);
            $q=parse_url($uri);
            $f=igk_io_basedir($q["path"]);
            if(file_exists($f)){
                igk_header_content_file($f);
                include($f);
            }
            else{
                igk_ilog("resource file not present: ".$f);
                igk_set_header(404);
            }
            igk_exit();
            break;
        }
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public function invokeUri($key){
        igk_app()->getControllerManager()->InvokeUri($this->getSystemUri($key));
        HtmlRenderer::RenderDocument(); 
    }
    ///<summary></summary>
    ///<param name="pattern"></param>
    ///<param name="render" default="1"></param>
    public function invokeUriPattern($pattern, $render=1){
        $r=$this->getRoutes();
        $v_uri=$r ? igk_getv($r, $pattern->action): null;
        $app=igk_app();
        $app->Session->PageFolder=IGK_HOME_PAGEFOLDER;
        igk_set_env(IGK_ENV_URI_PATTERN_KEY, $pattern);
        $app->getControllerManager()->InvokeUri($v_uri);
        igk_set_env(IGK_ENV_URI_PATTERN_KEY, null);
        if($render){
            HtmlRenderer::RenderDocument();
            igk_exit();
        }
    }
    ///<summary></summary>
    ///<param name="k"></param>
    public function IsFunctionExposed($k){
        return true;
    }
    ///<summary></summary>
    ///<param name="uri"></param>
    public function matche($uri){
        $v_routes=$this->getRoutes();
        if($v_routes){
            krsort($v_routes);
            foreach($v_routes as $k=>$v){
                $pattern=igk_pattern_matcher_get_pattern($k);
                if(preg_match_all($pattern, $uri)){
                    return new IGKSystemUriActionPatternInfo(array(
                        "action"=>$k,
                        "ctrl"=>null,
                        "value"=>$v,
                        "pattern"=>$pattern,
                        "uri"=>$uri,
                        "keys"=>igk_str_get_pattern_keys($k)
                    ));
                }
            }
        }
        return null;
    }
    ///<summary> use to match global registrated uri</summary>
    public function matche_global($uri){
        $v_routes=$this->getRoutes();
        if($v_routes){
            foreach($v_routes as $k=>$v){
                $pattern=igk_pattern_matcher_get_pattern($k);
                if(preg_match($pattern, $uri)){
                    return new IGKSystemUriActionPatternInfo(array(
                        "action"=>$k,
                        "ctrl"=>null,
                        "value"=>$v,
                        "pattern"=>$pattern,
                        "uri"=>$uri,
                        "keys"=>igk_str_get_pattern_keys($k)
                    ));
                }
            }
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="routes"></param>
    protected function setRoutes($routes){
        $this->setEnvParam(self::ROUTES, $routes);
    }
    ///<summary></summary>
    public function sys_ac_navigateto(){
        $p=igk_getr('p');
        if(isset($p)){
            igk_navto(igk_io_baseuri()."/".$p);
        }
        $k=igk_sys_ac_getpatterninfo();
        $tab=$k->getQueryParams();
        $l=igk_getv($tab, "lang");
        $s="?p=".igk_getv($tab, "page");
        if($l){
            $s .= "&l=".$l;
        }
        igk_app()->getControllerManager()->InvokeUri($s);
    }
    ///<summary></summary>
    ///<param name="p"></param>
    ///<param name="uri"></param>
    public function sys_ac_register($p, $uri){
        $v_tab=& $this->getRoutes();
        if(isset($v_tab[$p])){
            return;        }
        $v_tab[$p]=$uri;
    }
    ///<summary></summary>
    ///<param name="uripattern"></param>
    public function sys_ac_unregister($uripattern){
        $tab=& $this->getRoutes();
        if(isset($tab[$uripattern])){
            unset($tab[$uripattern]);
            $this->setRoutes($tab);
        }
    }
    ///<summary></summary>
    public function View(){
        if(!$this->getIsVisible()){
            igk_html_rm($this->TargetNode);
            return;
        }
        $c=$this->TargetNode;
        $this->ConfigNode->add($c);
        $c=$c->ClearChilds()->addPanelBox();
        igk_html_add_title($c, "title.SystemUriView");
        $c->addHSep();
        igk_html_article($this, "systemuri", $c->addDiv());
        $c->addHSep();
        $div=$c->addDiv();
        $ul=$div->add("ul");
        $v_routes=$this->getRoutes();
        foreach($v_routes as $k=>$v){
            $li=$ul->addLi()->setClass("clearb");
            $li->add("span", array("class"=>"igk-col-4-2 no-overflow igk-text-ellipis"))->Content=$k;
            $li->add("span", array("class"=>"igk-col-4-2"))->Content=$v;
        }
    }
}
