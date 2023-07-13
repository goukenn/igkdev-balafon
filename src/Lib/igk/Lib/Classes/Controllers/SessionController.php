<?php
// @file: IGKSessionController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGKOb; 
use IGKUserAgent;
use IGKValidator;
use IGK\Helper\StringUtility as IGKString;
use IGK\Resources\R;
use IGK\Server;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlSessionBlockNode;
use IGK\System\Http\Cookies;
use IGKEvents;

final class SessionController extends BaseController{
    ///<summary></summary>
    private function _viewTarget(){
        $this->getTargetNode()->clearChilds();
    }
  
    ///<summary></summary>
    public function changeviewmode(){
        if(!igk_is_conf_connected()){
            return;        }
        igk_set_env(IGK_ENV_NO_AJX_TEST, 1);
        $app=igk_app();
        $m=igk_getr("mode", 1);
        $app->setViewMode($m);
        igk_set_env(IGK_ENV_PAGEFOLDER_CHANGED_KEY, 1);
        $this->View();
        $app->Session->setRedirectTask("modview", 1);
        igk_sess_write_close();
        igk_navto_referer();
        igk_exit();
    }
    ///<summary></summary>
    public function ClearAllS(){
        igk_kill_all_sessions();
        $l=igk_sys_srv_referer();
        if(empty($l))
            $l=igk_io_baseuri();
        igk_navto($l);
    }
    ///<summary></summary>
    public function clearAllSession(){
        $exclude=igk_getr("exclude");
        if(igk_is_conf_connected() || igk_server_is_local()){
            $tab=igk_sys_get_all_openedsessionid();
            $cid=session_id();
            @igk_sess_write_close();
            $c=0;
            foreach($tab as $k=>$v){
                if($exclude && ($k == $cid))
                    continue;
                @unlink($v["file"]);
                $c++;
            }
        }
        $uri_demand=(".".igk_io_request_uri() == $this->getUri(__FUNCTION__));
        if($uri_demand){
            igk_navtobaseuri();
        }
    }
    ///<summary></summary>
    public function clearcache(){
        if(Server::IsLocal() || igk_is_conf_connected() || !igk_sys_env_production()){
            igk_clear_cache();
        }
        igk_navto_referer();
    }

    // protected function IsFunctionExposed($funcname){     
    //     $g = parent::__callStatic('invokeMacros', [__FUNCTION__, $this, $funcname]);        
    //     return $g;
    // }
    ///<summary> clear session and navigate</summary>
    public function ClearS($navigate=true){
 
        if ($session = igk_app()->getApplication()->getLibrary()->session){
            $session->destroy(); 
        } 
        $_rcu=explode("?", igk_io_request_uri())[0];
        if($navigate){

            $buri=0;
            $s=$_rcu;
            if($s && IGKString::EndWith($s, "/clr")){
                $s=igk_str_rm_last($s, "/clr");
                $_rcu=empty($s) ? "/": $s;
                $buri=1;
            }
            $m=igk_getr("r");
            if($m){
                $m=base64_decode($m);
                igk_navto($m);
            }
            $u=igk_sys_srv_referer();
            if(!empty($u)){
                igk_navto_referer();
            }
            else{
                if($buri){
                    igk_navto($_rcu);
                }
            }
            igk_navto(igk_io_baseuri());
        } 
    }
    ///<summary></summary>
    public function configPropertyChanged(){
        $this->View();
    }
    ///<summary></summary>
    public function ConfUserChanged(){
        $this->View();
    }
    ///<summary>call system force view on session controller</summary>
    public function forceview(){ 
        if ($doc=igk_app()->getDoc()){
            igk_hook(IGKEvents::HOOK_FORCE_VIEW, [$this]);
        }
    }
    ///<summary></summary>
    public function getIsVisible():bool{
        if(igk_get_env("sys://error"))
            return false;
        return !defined('IGK_NO_WEB') && !igk_const_defined('IGK_NO_SESSION_BUTTON') && (Server::IsLocal() || (!IGKUserAgent::isMobileDevice() && igk_is_conf_connected() && igk_configs()->allow_debugging));
    }
    ///<summary></summary>
    public function getName(){
        return IGK_SESSION_CTRL;
    }
    
    ///<summary></summary>
    protected function initComplete($context=null){   
       
        parent::initComplete();
        if(igk_is_atomic() || defined("IGK_INIT_SYSTEM"))
            return; 
        $n=igk_get_cookie_name(igk_sys_domain_name()."/".Cookies::USER_ID);
        $rs=igk_getv($_COOKIE, $n);
        if(!empty($rs)){ 
            try {
                $uid = igk_getv(explode(':', $rs), 0);
                $v = igk_user_info(IGK_UINFO_TOKENID, $uid);
                $d = substr($rs, strlen($uid) + 1);
                /// TODO : TOKEN USER - RESOLUTION
                // igk_wln_e(__FILE__.":".__LINE__, "the token:", $v, $d);
                if($v && ($v->clValue == $d)){
                    $r=igk_get_user($uid);
                    if($r){
                        igk_getctrl(IGK_USER_CTRL)->setUser($r);
                        igk_user_store_tokenid($r);
                    }
                } else {
                    
                    unset($_COOKIE[$rs]);
                    igk_getctrl(IGK_USER_CTRL)->setUser(null);
                    igk_clear_cookie('uid');
                    // igk_wln_e("token not found".$n, $_COOKIE, $rs, $_SESSION);

                } 
            }
            catch(\Exception $db){
                igk_ilog("possible db connection failed");
                igk_clear_cookie($n);
            }
        }
        OwnViewCtrl::RegViewCtrl($this, 0);
    
        igk_reg_hook(IGKEvents::HOOK_HTML_BODY, function($e){            
            $options = igk_getv($e->args, "options");
            echo $this->getTargetNode()->render($options); 
        });
    }
    
    ///<summary></summary>
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        return  new HtmlSessionBlockNode($this);
    }
    ///<summary></summary>
    public function invmodule(){
        if(igk_get_env(__METHOD__))
            igk_die("Can't invoke module twice");
        igk_set_env(__METHOD__, 1);
        $q=igk_getr("q");
        $g=array();
        parse_str(igk_getv(parse_url("?".base64_decode($q)), 'query'), $g);
        $modn=$g["n"];
        $mod=igk_init_module(str_replace(".", "/", $modn));
        $access=$g["q"];
        $listener=igk_getctrl($g["ctrl"]);
        if($mod){
            $mod->Listener=$listener;
            $tab=igk_str_array_rm_empty(explode('/', $access));
            call_user_func_array(array($mod, $tab[0]), array_slice($tab, 1));
        }
        else{
            igk_ilog("/!\\ module {$modn} not found : ".igk_io_request_uri());
            igk_ilog($g);
        }
        igk_set_env(__METHOD__, null);
        igk_exit();
    }
    ///<summary></summary>
    public function notify_forceview(){
        R::LoadLang();
        $this->View();
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    public function onHandleSystemEvent($msg){
        switch($msg){
            case IGK_ENV_NEW_DOC_CREATED:
            $args=array_slice(func_get_args(), 1);
            $this->onSessionNewDocCreated($args[0], $args[1]);
            break;
        }
    }
    ///<summary></summary>
    ///<param name="o">object that initiate</param>
    ///<param name="e">document</param>
    private function onSessionNewDocCreated($o, $e){
        if($e && !igk_const_defined('IGK_NO_SESSION_BUTTON')){
            $this->_viewTarget();
            $n=igk_html_node_clonenode($this->TargetNode);
            $n->setIndex(1000);
            $n->setCallBack("getIsVisible", array($this, "getIsVisible"));
            igk_html_add($n, $e->body);
        }
    }
    ///<summary></summary>
    public function PageChanged(){
        $this->View();
    }
    ///<summary>Session Controller Run Crons ----- </summary>
    public function RunCron(){
        $c=igk_getr("ctrl");
        if($c){
            $c=igk_getctrl($c);
            BaseController::RunCrons($c);
            igk_exit();
        }
        $tab=array();
        $server="BALAFON";
        $cookie_name = igk_environment()->session_cookie_name;

        $sessid=igk_getv($_COOKIE, $cookie_name, session_id());
        $strCookie= $cookie_name.'='.$sessid.'; path='.igk_get_cookie_path();
        $f=igk_data_get_cron_file();
        if(file_exists($f))
            $tab=igk_json_parse(igk_io_read_allfile($f));
        else
            $tab[]=igk_io_baseuri().$this->getUri("RunCron&ctrl=baobabtv");
        $doc=igk_get_document(__METHOD__);
        igk_sess_write_close();
        IGKOb::Start();
        foreach($tab as $v){
            if(!IGKValidator::IsUri($v) || ($v[0] == "?")){
                $v=igk_io_baseuri().$v;
            }
            $r=curl_init();
            if($r){
                curl_setopt($r, CURLOPT_URL, $v);
                curl_setopt($r, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($r, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($r, CURLOPT_USERAGENT, $server);
                curl_setopt($r, CURLOPT_COOKIE, $strCookie);
                $data=curl_exec($r);
                curl_close($r);
            }
        }
        $d=IGKOb::Content();
        IGKOb::Clear();
        $doc->body->addBodyBox()->div()->Content=$d;
        $doc->renderAJX();
        $doc->Dispose();
        igk_exit();
    }
    ///<summary></summary>
    public function update_setting(){
        $this->View();
    }
   
}
