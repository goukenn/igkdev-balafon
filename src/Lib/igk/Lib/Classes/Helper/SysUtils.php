<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SysUtils.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Helper;

use Error;
use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Configuration\Controllers\ConfigControllerRegistry;
use IGKApp;
use IGKEvents;
use IGKException;
use ReflectionMethod;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\System\Configuration\ApplicationConfigConstants;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
use TypeError;

class SysUtils{
    /**
     * helper to secure web port 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function SecurePort(){
        $config = igk_configs(); 
        if ($config->force_secure_redirection) {            
            $tp = igk_server()->SERVER_PORT;
            $csport = $config->secure_port; 
            $sport = array_map('trim', array_filter(explode(',', $csport ?? 443)));            
            if (!in_array($tp, $sport)){
                igk_navto(igk_secure_uri(igk_io_fullrequesturi(), true, false));
                igk_exit();
            }
        }    
    }
    public static function TryServerAutoConnect(BaseController $ctrl){          
        $a = igk_server()->HTTP_USER_AGENT;
        $chek = igk_configs()->{ApplicationConfigConstants::allow_auto_connect_agents} ?? []; 
        if ( ($a && in_array($a, $chek))){ 
            if ($uid = igk_getv(igk_get_allheaders(), 'IGK_CURRENT_USER_ID')){           
                if ($user = \IGK\Models\Users::Get('clId', $uid)){
                    if ($r = $ctrl::login($user, null, false)){
                        $user = $ctrl->getUser();
                        return $user;
                    }               
                }
            }
        }
        return null;
    }
    /**
     * prepent sys db controller 
     * @param array $c 
     * @return void 
     */
    public static function PrependSysDb(array & $c){
        $sysdb = SysDbController::ctrl();
        // prepend sys deb 
        if ( false!== ($key = array_search($sysdb, $c))){
            unset($c[$key]);
        }
        array_unshift($c, $sysdb);
    }
    /**
     * evaluate source
     * @return void 
     */
    public static function Eval(){
        if (func_num_args()<1){
            igk_die("missing requirement arguments count.");
        }
        extract(func_get_arg(1));
        try{ 
            if (!igk_environment()->NoLogEval && igk_environment()->isDev()){
                igk_ilog('eval : '.func_get_arg(0));
            }
            eval("?>".func_get_arg(0));
        }catch (TypeError $error){
            throw new IGKException('eval failed', 500, $error);
        } catch (Error $error){
            $error_msg = $error->getMessage();
            igk_dev_wln_e("error : ", $error_msg);
            igk_ilog($error_msg);
        }
    }
    public static function Include(){
        if ((func_num_args()==2) && (is_array(func_get_arg(1)))){
            extract(func_get_arg(1));
        }
        return include(func_get_arg(0));
    }
    /**
     * get default project entry namespace
     * @param string $dir 
     * @return string 
     * @throws IGKException 
     */
    public static function GetProjectEntryNamespace(string $dir){
        $path = igk_io_collapse_path($dir);
        $path = igk_str_ns(str_replace("%project%/", "", $path));
        return sprintf("%s\\%s", defined('IGK_PROJECT_DEFAULT_NS') ? 
            constant('IGK_PROJECT_DEFAULT_NS'): 
            IGK_SYS_PROJECT_NS, 
            $path);
    }
    /**
     * get site title
     * @param string $title 
     * @return string 
     */
    public static function SiteTitle(string $title){
        $j = igk_configs()->get("site_title_join", " - ");
        return implode ($j, array_filter(array_merge(func_get_args(), [sprintf("[ %s ]", igk_configs()->website_domain)])) );
    }
    /**
     * get controller by name
     * @param string $ctrl 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetControllerByName(string $ctrl, $throwex = 1){        
        if ($ctrl == '%sys%'){
            return SysDbController::ctrl();
        }
        $ctrl = str_replace("/", "\\", $ctrl);  
        return  (IGKApp::IsInit() && class_exists($ctrl) && is_subclass_of($ctrl, BaseController::class) ) ?
                $ctrl::ctrl() : 
                igk_app()->getControllerManager()->getController($ctrl, $throwex);
    }
    /**
     * get application module from entry file
     * @param mixed $file 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetApplicationModule($file){
        return igk_get_module(igk_get_module_name(dirname($file)));
    }
    /**
     * @return array list of controller installed in project folder
     */
    public static function GetProjectControllers(callable $filter=null){
        if (!IGKApp::IsInit()) {
            return null;
        }
        $c = igk_app()->getControllerManager()->getControllers();
        $dir = igk_io_collapse_path(igk_io_projectdir());
        $projects_ctrl = [];
        foreach ($c as $k){
            $ccpath = igk_io_collapse_path($k->getDeclaredDir());;
            
            if (strstr($ccpath, $dir)) {
                if (!$filter || $filter($k))
                    $projects_ctrl[] = $k;
            }
        }
        return $projects_ctrl;
    }
    public static function GetDeclaredMethods($class){
        $ref = igk_sys_reflect_class($class);
        return  array_filter(array_map(function($m) use ($class){
            $n = $m->getName();
            if (strpos($n , "__")===0){
                return null;
            }
            if ($m->getDeclaringClass()->getName() == $class)
                return $n;
            return null;
        },$ref->getMethods( ReflectionMethod::IS_PUBLIC)));


    }
     /**
     * 
     * @param array|\IIGKArrayObject $n  item to convert
     * @return array 
     */
    public static function ToArray($n){
        if (!$n){
            return null;
        }
        if (is_array($n))
            return $n;
        return $n->to_array();
    } 

     ///<summary>Notifify message</summary>
     public static function Notify($message, $type="default"){
        if (igk_is_ajx_demand()){
            igk_ajx_toast($message, $type);
        }else {
            igk_notifyctrl()->bind($message, $type);
        }
    }
    ///<summary>exist on ajx deman</summary>
    /**
     * exit on ajx demand
     * @return void 
     * @throws IGKException 
     */
    public static function exitOnAJX(){
        if (igk_is_ajx_demand()){
            igk_hook(IGKEvents::HOOK_AJX_END_RESPONSE, []);
            igk_environment()->isAJXDemand = null;
        } 
        igk_exit();
    }

    public static function InitClassFields($c, $object){
        $properties = igk_relection_get_properties_keys(get_class($c)); 
        foreach($object as $k=>$v){
            if (key_exists($k = strtolower($k), $properties)){
                $m = $properties[$k]->getName();
                $c->$m = $v;
            }
        }
    }
    /***
     * init class variable
     */
    public static function InitClassVars($n, $tag){ 
        foreach(get_class_vars(get_class($n)) as $k=>$c){ 
            $n->$k = igk_getv($tag, $k, $c);
        } 

    }
    public static function assert_notify($condition, $successmsg, $errormessage, $name=null){
        $check = igk_check($condition);
        $notify = igk_notifyctrl($name);
        if ($check){
            $notify->success($successmsg);
        } else {
            $notify->error($errormessage);
        }
    }
    ///<summary>assert toation on ajx demand condition</summary>
    /**
     * assert toation on ajx demand condition
     * @param mixed $condition 
     * @param mixed $successmsg 
     * @param mixed $errormessage 
     * @return void 
     * @throws Exception 
     * @throws ReflectionException 
     */
    public static function assert_toast($condition, $successmsg, $errormessage){
        if (!igk_is_ajx_demand())
            return;
        $check = igk_check($condition);
        $d = ["msg"=>$successmsg, "type"=>"igk-success"];
        if (!$check){
            $d["msg"] =$errormessage;
            $d["type"]="igk-danger";
        }
        igk_ajx_toast($d["msg"], $d["type"]);
    }

    /**
     * get subdomain controller 
     * @return null|BaseController subdomain controller
     */
    public static function GetSubDomainCtrl(){
        $v_c = igk_app()->getApplication();
        if ($v_c->lib("subdomain")){
            return $v_c->getLibrary()->subdomain->subdomain; 
        }
        return null;
    }
    /**
      * @return null|BaseController subdomain controller
     */
    public static function CurrentBaseController(){
        // $a = igk_app();

        return igk_environment()->subdomainctrl ??
            igk_app()->getBaseCurrentCtrl() ?? igk_get_defaultwebpagectrl();
    }
    /**
     * 
     * @param string $name 
     * @return mixed 
     */
    public static function GetApplicationLibrary(string $name){
        return igk_getv(igk_app()->getApplication()->getLibrary(), $name);
    }

     ///JUST: store to controller
    ///<summary>clear cache for base dir</summary>
    /**
     * clear cache for base dir
     */
    public static function ClearCache($bdir = null, $init = 0)
    {
        $t = null;
        if ($bdir == null)
            $bdir = igk_io_cachedir();
        $init && !defined("IGK_INIT_SYSTEM") && define("IGK_INIT_SYSTEM", 1);
        // + | Clear assets folder
        if (is_dir($assets = igk_io_basedir() . "/" . IGK_RES_FOLDER)) {
            Logger::info("clean public cache: " . $assets);
            IO::CleanDir($assets);  
        }
        if (is_dir($bdir)) {
            Logger::info("rm :" . $bdir);
            IO::CleanDir($bdir);
            igk_io_w2file($bdir . "/.htaccess", "deny from all", false);
            igk_hook("sys://cache/clear");
        }
    }

    /**
     * resolv link path
     * @param string $rp 
     * @return string 
     * @throws IGKException 
     */
    public static function ResolvLinkPath(string $rp){

        if (is_null(igk_server()->HOME) && ($p = igk_configs()->get('access_home_dir'))){
            $home_dir = "/home/".igk_server()->USER;
            if (strpos($rp, $home_dir) === 0 ){
                $rp = $p."/".substr($rp , strlen($home_dir)+1);
            }
        }
        return $rp;
    }
}