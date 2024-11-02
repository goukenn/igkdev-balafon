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

use Exception;
use IGK\Controllers\BaseController;
use IGK\Models\Systemuri;
use IGK\System\Html\HtmlRenderer;
use IGKException;
use IGKSystemUriActionPatternInfo;
use IIGKUriActionListener;

final class SystemUriActionController extends ConfigControllerBase implements IIGKUriActionListener{
    //+ action routes
    const ROUTES=IGK_CUSTOM_CTRL_PARAM + 0x1;
    const CACHE_FILE = '.routes.cache';
    /**
     * handle resources 
     */
    const AC_RES_URI = 'res';
    /**
     * handle system uri
     */
    const AC_SYS_URI = 'sys';
    
    private static $sm_actions, $sm_routes;
    public static function GetCacheFile(){
        return igk_io_cachedir()."/".self::CACHE_FILE;
    }
   
    private static function _RegActions(SystemUriActionController $controller){
        if (self::$sm_actions === null){
            // @unlink(self::GetCacheFile());
            if (file_exists($file = self::GetCacheFile())){
                $tab = unserialize(file_get_contents($file));
                self::$sm_routes = $tab["routes"];
                self::$sm_actions = $tab["actions"];
                if (empty(self::$sm_actions)){
                    self::$sm_actions = self::InitActionList($controller, self::$sm_routes, true);
                } 
            }
            else {
                $g = & $controller->getRoutes();
                self::$sm_actions = self::InitActionList($controller, $g);
                // igk_wln_e("init route ", self::$sm_actions);
                register_shutdown_function(function()use($file)
                { 
                    igk_io_w2file($file, serialize([
                        "routes"=>self::$sm_routes,
                        "actions"=>self::$sm_actions
                    ])); 
                });
            } 
            krsort(self::$sm_routes);
        }
        return self::$sm_actions;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public function contains($key){
        
        $tab=$this->_refRoutes();
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
        igk_sess_write_close();
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
    public function getDataTableName(): ?string{
        return igk_db_get_table_name(IGK_TB_SYSTEMURI);
    }
    ///<summary></summary>
    public function getmailto(){
        igk_trace();
        igk_wln_e("get mail to");
        igk_navto("mailto:".IGK_AUTHOR_CONTACT);
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
    public function getUseDataSchema():bool{
        return false;
    }
    public function handle_redirection_uri($uri, $params = null, $redirection = 0, $render = 1){
       
        $app = igk_app();
        $actionctrl = $this; 
        if ($e = $actionctrl->matche($uri)){
            $e->requestparams = $params;
            $app->Session->RedirectionContext = $redirection; 
            try {
                $actionctrl->invokeUriPattern($e, $render);
            } catch (\Exception $ex) {
                throw $ex;
            }
            return true;
        } else {
            igk_dev_ilog(__METHOD__." not match. ".$uri);
        }
        return false;
    }
    ///<summary>get system uri actions routes</summary>
    ///<return refout="true"></return>
    public function & getRoutes(){
        if(self::$sm_routes === null){
            self::$sm_routes=array();
        }
        return self::$sm_routes;
    }
    private function & _refRoutes(){
        self::_RegActions($this);
        return $this->getRoutes();
    }
    ///<summary></summary>
    ///<param name="key" default="null"></param>
    public function getSystemUri($key=null){
        $tab=$this->_refRoutes();
        return igk_getv($tab,$key);
    }
    ///<summary></summary>
    public function gotoconfig(){ 
        igk_navto("/Configs", 301);
    }
    ///<summary></summary>
    public function init_wakeup(){    }
    ///<summary></summary>
    ///<param name="ctrl" default="null"></param>
    private static function InitActionList($ctrl, & $route, $forceReload=false){
        $actions=array();
        if (!$forceReload && (defined("IGK_NO_WEB") || igk_is_cmd())){
            return $actions;
        }
        $actions["^/config(\.php)?$"]=$ctrl->getUri("gotoconfig");
        $conf_ctrl=igk_getconfigwebpagectrl();
        if($conf_ctrl){
            $actions["^/Configs!Settings$"]=$conf_ctrl->getUri("configure_settings");
            // igk_wln_e(__FILE__.":".__LINE__, "init action ... ");
            $t=igk_get_env("sys://configs/options");
            if($t){
                foreach($t as $k=>$v){
                    $actions["^/Configs!{$k}$"]=$conf_ctrl->getUri("configure&v=".$v);
                }
            }
            $actions["^/reconnect$"]=$conf_ctrl->getUri("reconnect");
        }
        $actions["/@!(.)+"]=$ctrl->getUri("invoke_action");
        $actions["/clr$"]="?c=".IGK_SESSION_CTRL."&f=ClearS";
        $actions["/run-cron$"]="?c=".IGK_SESSION_CTRL."&f=RunCron";
        $actions["^/initsdb$"]="?c=".IGK_MYSQL_DB_CTRL."&f=pinitSDb";
        $actions["^/getmailto$"]=$ctrl->getUri("getmailto");
        if($uctrl=igk_getctrl(IGK_USER_CTRL)){
            $actions["^/connect$"]=$uctrl->getUri("connectpage");
            $actions["^/signup$"]=$uctrl->getUri("signup");
            $actions["^/users/begin_reset_pwd$"]=$uctrl->getUri("begin_pwd_reset");
            $actions["^/users/logout$"]=$uctrl->getUri("logout_lnk");
        }
        if($sys_c=igk_getctrl(IGK_SYS_CTRL, false)){
            $actions["^/sys_api/check_mod_rewrite$"]=$sys_c->getUri("mod_rewrite");
        }  
        // + | append actions to routes
        foreach($actions as $k=>$v){
            $route[$k]=$v;
        }  
        // + | -----------------------------------------------------------------------
        // + | TODO :  SELECT  * DB ROUTE LOOP FAILED, infinite loop
        // + | -----------------------------------------------------------------------
        if (!igk_configs()->get("no_db_route") && class_exists(Systemuri::class)){        
            try {
                $e = Systemuri::select_all(); 
         
            if($e){
                foreach($e as $k=>$v){
                    if(is_object($v)){
                        $route[$v->clName]=$v->clUri;
                    }
                    else{
                        igk_wln("uri : object not register from db");
                        igk_wln($v);
                    }
                }
            }
            } catch(Exception $ex){
                // no uri loader
            }
        }
        return $actions;
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
    public function invokeCtrlUriPattern(\IGK\Controllers\BaseController $ctrl, $pattern, $render=1){
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

        self::_RegActions($this); 
        switch($type){
            case self::AC_SYS_URI:
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
            case self::AC_RES_URI:
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
                igk_set_header(RequestResponseCode::NotFound);
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
        $r=$this->_refRoutes();
        $v_uri=$r ? igk_getv($r, $pattern->action): null;
        $app=igk_app();
        $app->Session->PageFolder=IGK_HOME_PAGEFOLDER;
        igk_set_env(IGK_ENV_URI_PATTERN_KEY, $pattern);

        // $v_uri = './?c=%7Ba4918130-ce95-8e6b-c4a0-7b906dcf8c51%7D&f=configure-settings';
        // igk_trace();
        // igk_wln_e($v_uri, $pattern->action, $r);


        $app->getControllerManager()->InvokeUri($v_uri);
        igk_set_env(IGK_ENV_URI_PATTERN_KEY, null);
        if($render){
            HtmlRenderer::RenderDocument();
            igk_exit();
        }
    }
    /**
     * get controller that match the query
     * @param string $uri 
     * @return null|\IGK\Controllers\BaseController 
     * @throws IGKException 
     */
    public static function GetMatchCtrl(string $uri, bool $forceMatch=false){
        static $rsolv = true;

        if ($rsolv){
            $rsolv = false;
        }else {
            return ;
        }
 
        $g = self::ctrl()->matche($uri);
        
        $c = null;
        $rsolv = true;
        if ($g){
            $tg = [];
            parse_str(igk_getv(parse_url($g->value), "query", ""), $tg);            
            if ($c = igk_getv($tg, "c")){ 
                return igk_getctrl($c, false);
            }
        }
    }
    ///<summary></summary>
    ///<param name="uri"></param>
    public function matche($uri){
        if (empty($uri)){
            return null;
        }
        $v_routes = $this->_refRoutes();  
        // igk_wl_pre($v_routes);
        // igk_wln_e(__FILE__.":".__LINE__);

        if($v_routes){
            //  krsort($v_routes);
            foreach($v_routes as $k=>$v){
                $pattern=igk_pattern_matcher_get_pattern($k); 
                if(preg_match_all($pattern, $uri)){
                     
                    return new IGKSystemUriActionPatternInfo(array(
                        "action"=>$k,
                        "ctrl"=> null,
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
    
        $v_routes = $this->_refRoutes();
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
        $v_tab = & $this->_refRoutes(); 
        if(isset($v_tab[$p])){
            return;       
         }
        $v_tab[$p]=$uri;
    }
    ///<summary></summary>
    ///<param name="uripattern"></param>
    public function sys_ac_unregister($uripattern){
        $tab=& $this->_refRoutes();
        if(isset($tab[$uripattern])){
            unset($tab[$uripattern]);
            // $this->setRoutes($tab);
        }
    }
    ///<summary></summary>
    public function View():BaseController{
        $c=$this->getTargetNode();
        if(!$this->getIsVisible()){
            $c->remove();
            return $this;
        }
        $this->ConfigNode->add($c);
        $c=$c->clearChilds()->addPanelBox();
        igk_html_add_title($c, "title.SystemUriView");
        //$c->addHSep();
        
        $c->notagnode()->article($this, "systemuri", []);
        //igk_html_article($this, "systemuri", $c->div());
        //$c->addHSep();
        $div=$c->div();
        $ul=$div->add("ul");
        $v_routes=$this->_refRoutes();
        foreach($v_routes as $k=>$v){
            $li=$ul->li()->setClass("clearb");
            $li->add("span", array("class"=>"igk-col-4-2 no-overflow"))->Content=$k;
            $li->add("span", array("class"=>"igk-col-4-2"))->Content=$v;
        }
        return $this;
    }
  
}
