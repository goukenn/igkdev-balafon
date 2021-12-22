<?php

///<summary> System Controllers Managers. store list of different controller table. </summary>
///<note></note>

use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\Resources\R;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGK\Cache\SystemFileCache as IGKSysCache;
use IGK\Controllers\RootControllerBase;
use IGK\System\Configuration\Controllers\ConfigControllerRegistry;
use IGK\System\Configuration\Controllers\SystemUriActionController;
use IGK\System\Drawing\Color;

use function igk_resources_gets as __;


/**
*  System Controllers Managers. store list of different controller table.
*/
final class IGKControllerManagerObject extends IGKObject {
    /**
     * @var array store [classe:instance] of registrated controller
     */
    private $m_classReg;

    private $m_tbcontrollers;
    private $m_initEvent;
    private $m_register;  
    /**     
     * @var IGKControllerManagerObject controller instance
     */
    private static $sm_instance;
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        if(func_num_args()>0){
            igk_die("argument not allowed");
        }
        $this->m_tbcontrollers=array();   
        $this->m_initEvent=0;
    }
    /**
     * get or init controller instance
     */
    public function getControllerInstance($classname){
        // priority to class name
        $g = igk_getv($this->m_classReg, $classname);
        if ($g)
            return $g;

        if (isset($this->m_tbcontrollers[$classname])){
            return $this->m_tbcontrollers[$classname];
        }
        foreach($this->m_tbcontrollers as $c){
            if (get_class($c) === $classname){
                $this->m_classReg[$classname] = $c;
                return $c;               
            }
        }
        $n  = $classname; 
        if (class_exists($n) && is_subclass_of($n, BaseController::class)){ 
            $o = RootControllerBase::CreateInstanceAndInit($n, function($o, $n){                
                $this->{$o->getName()} = $o;
                $this->m_tbcontrollers[$n] = $o;  
                return true;               
            });                    
            return $o;
        } else { 
            if (igk_environment()->is("DEV")){
            echo ($msg = "failed to initialize class instance : $n");
            // echo "failed::: ". igk_sys_request_time()."<br />";  
           //  igk_dev_wln($msg, array_keys(  $this->m_tbcontrollers));
            igk_trace();
            @session_destroy();
            //session controller make infinite loop
            igk_exit();
            }
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function __get($key){
        $key=strtolower($key);
        $c=igk_getv($this->m_tbcontrollers, $key);
        if($c){
            if(igk_is_class_incomplete($c)){
                unset($this->m_tbcontrollers[$key]);
                return null;
            }
            return $c;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function __set($key, $value){
        $key=strtolower($key);
        if($value === null){
            if(isset($this->m_tbcontrollers[$key]))
                unset($this->m_tbcontrollers[$key]);
            return;
        }
        else{
            if(is_object($value) &&  ($value instanceof BaseController)){            
                $this->m_tbcontrollers[$key]=$value;
            }
        }
    }
    /**
     * get registrated keys
     * @return int[]|string[] 
     */
    public function getInitControllerKeys(){
        return array_keys($this->m_tbcontrollers);
    }
    ///<summary>display value</summary>
    /**
    * display value
    */
    public function __toString(){
        return "Controllers [#".count($this->m_tbcontrollers)."]";
    }
    ///<summary></summary>
    ///<param name="s"></param>
    ///<param name="tab"></param>
    ///<param name="fname"></param>
    ///<param name="fsize"></param>
    ///<param name="x"></param>
    ///<param name="y"></param>
    ///<param name="cl"></param>
    /**
    * 
    * @param mixed $s
    * @param mixed $tab
    * @param mixed $fname
    * @param mixed $fsize
    * @param mixed $x
    * @param mixed $y
    * @param mixed $cl
    */
    private function _cm_measure($s, $tab, $fname, $fsize, $x, $y, $cl){
        $rc=(object)array("x"=>0, "y"=>0, "width"=>0, "height"=>0);
        if(is_array($tab)){
            foreach($tab as $v){
                if($v->WebParentCtrl == null){
                    $t=$this->_cm_measure($s, $v, $fname, $fsize, $x, $y, $cl);
                    $rc->height += $t->height;
                    $rc->width=max($rc->width, $t->x + $t->width);
                }
            }
        }
        else{
            return $this->renderController($s, $tab, $fname, $fsize, $x, $y, $cl);
        }
        return $rc;
    }
    ///generate
    /**
    */
    private function _initController($ctrl){
        $this->registerController($ctrl, true);
    } 
     
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="regname" default="null"></param>
    /**
    * 
    * @param mixed $ctrl
    * @param mixed $regname the default value is null
    */
    private function _registerCtrl($ctrl, $regname=null){
        if ($ctrl=== null){
            igk_die("ctrl not null");
        }
        $n=$ctrl->getName();
        $this->$n=$ctrl;
        $this->m_classReg[get_class($ctrl)] = $ctrl;
        if(!self::IsSystemController($ctrl)){
            $s=$regname ?? $ctrl->Configs->clRegisterName;
            if(!empty($s)){
                $rg=$this->getRegisters();
                if(isset($this->Registers->$s) && ($this->Registers->$s != null))
                    igk_die("you already register a controller with the name {$s} - please check configuration");
                $this->m_register->$s=$ctrl;
                return 1;
            }
        } 
        return 0;
    }
    ///<summary></summary>
    ///<param name="a"></param>
    ///<param name="b"></param>
    /**
    * 
    * @param mixed $a
    * @param mixed $b
    */
    private function _sort_byConfigNodeIndex($a, $b){
        if($a->Configs->clTargetNodeIndex && $b->Configs->clTargetNodeIndex){
            $i=$a->Configs->clTargetNodeIndex;
            $j=$b->Configs->clTargetNodeIndex;
            return ($i == $j) ? 0: (($i < $j) ? -1: 1);
        }
        return strcmp($a->Name, $b->Name);
    }
    ///JUST: store to controller
    ///<summary>clear cache for base dir</summary>
    /**
    * clear cache for base dir
    */
    public static function ClearCache($bdir=null, $init=0){
        $t=null;
        if($bdir == null)
            $bdir=igk_io_cachedir();
        $init && !defined("IGK_INIT_SYSTEM") && define("IGK_INIT_SYSTEM", 1);
        // + | Clear assets folder
        if (is_dir($assets = igk_io_basedir()."/".IGK_RES_FOLDER)){
            Logger::info("remove cache:".$assets);
            IO::RmDir($assets);
        }
        if (is_dir($bdir)){
            Logger::info("rm :".$bdir);
            IO::RmDir($bdir);           
            igk_io_w2file($bdir."/.htaccess", "deny from all", false);
            igk_hook("sys://cache/clear");
        } 
    }
    ///<summary></summary>
    /**
    * 
    */
    public function ClearCtrlCache(){
        $fc=self::FileCtrlCache();
        if(file_exists($fc)){
            unlink($fc);
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function cm_controllerschema(){
        if(!defined("IGK_GD_SUPPORT")){
            igk_exit();
        }
        $y=14;
        $x=0;
        $fname=igk_io_currentrelativepath(igk_get_uvar("CONFIG_SCHEMA_FONT"));
        if(!file_exists($fname)){
            igk_exit();
        }
        $fsize=14;
        $cl=Color::FromFloat(1.0);
        $tb=$this->getUserControllers();
        $s=IGKGD::Create(32, 32);
        $rect=$this->_cm_measure($s, $tb, $fname, $fsize, $x, $y, $cl);
        $s->Dispose();
        if(($rect->width<=0) || ($rect->height<=0))
            igk_exit();
        $x=40;
        $y=28;
        $s=IGKGD::Create($rect->width + 300 + (2 * $x), $rect->height + ($y));
        $s->Clear($cl);
        if(file_exists($fname)){
            foreach($tb as  $v){
                if($v->WebParentCtrl == null){
                    $t=$this->renderController($s, $v, $fname, $fsize, $x, $y, Color::Black(), $rect->width + 150);
                    $y += $t->height;
                }
            }
        }
        header("Content-type: image/png");
        $s->render();
        $s->Dispose();
        unset($s);
        igk_exit();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function Count(){
        return count($this->m_tbcontrollers);
    }
     
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
    * 
    * @param mixed $ctrl
    */
    public function dropController($ctrl){
        if(!$ctrl)
            return false;
        $k=strtolower($ctrl->Name);
        $d=dirname($ctrl->getDeclaredFileName());
        $ctrl->dropController();
        $r=false;
        if(is_dir($d)){
            $r=IO::RmDir($d, true);
        }
        unset($this->m_tbcontrollers[$k]);
        BaseController::UnRegisterInitComplete($ctrl);
        igk_notification_push_event(IGK_DROP_CTRL_EVENT, array($ctrl));
        return true;
    }
    ///<summary></summary>
    ///<param name="name"></param>
    //@remove controller by name
    /**
    * 
    * @param mixed $name
    */
    public function dropControllerByName($name){
        $k=strtolower($name);
        if(isset($this->m_tbcontrollers[$k])){
            $ctrl=$this->m_tbcontrollers[$k];
            return $this->dropController($ctrl);
        }
        return false;
    }
    ///<summary>get file ctrl cache</summary>
    /**
    * get file ctrl cache
    */
    private static function FileCtrlCache(){
        return igk_io_syspath(IGK_FILE_CTRL_CACHE);
    }
    ///<summary></summary>
    ///<param name="classname"></param>
    /**
    * 
    * @param mixed $classname
    */
    public function getControllerFromClass($classname){
        if($this->m_classReg == null)
            $this->m_classReg=array();
        if(isset($this->m_classReg[$classname]))
            return $this->m_classReg[$classname];
        foreach($this->m_tbcontrollers as  $v){
            if(get_class($v) == $classname){
                $this->m_classReg[$classname]=$v;
                return $v;
            }
        }
        return null;
    }
    ///<summary>get array of initialized controller</summary>
    ///<return refout="true"></return>
    /**
    * get array of initialized controller
    * @return mixed|array controller list 
    */
    public function getControllers(){
        return array_unique(array_values($this->m_tbcontrollers));
    }   
    /**
     * get controller reference
     */
    public function & getControllerRef(){
        return $this->m_tbcontrollers;
    }
    ///<summary>get the current instance manager</summary>
    /**
    * get the current instance manager
    */
    public static function getInstance( ){
        if (func_num_args()>0){
            igk_die("argument count not allowed ".__METHOD__);
        }  
        if(self::$sm_instance === null){ 
			self::$sm_instance = new self();
            igk_reg_hook(IGKEvents::HOOK_INIT_APP, function($e){                
                self::$sm_instance->InitControllers($e->args[0]);
            }); 
        }  
        return self::$sm_instance;
    }
    ///<summary>get registerd global controller for specific fonctionnality</summary>
    /**
    * get registerd global controller for specific fonctionnality
    */
    public function getRegCtrl($name){
       igk_die("not allowed: ".__METHOD__);  
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return mixed|array register list
    */
    public function & getRegisters(){
        if($this->m_register == null)
            $this->m_register=igk_createobj();
        return $this->m_register;
    }
    ///return the user controller list.
    ///there is 2 controller type . framework controller and user controllers
    /**
    */
    public function getUserControllers($callbackfilter=null){
        $tab=$this->getControllers();
        $out=array();
        if(igk_count($tab) > 0){
            // igk_wln("try....", __FILE__.":".__LINE__, $tab);
            // $tab_k=array_keys($tab);
            // igk_usort($tab_k, "igk_key_sort");
            foreach($tab as $v){
                // $v=$tab[$k];
                // if (get_class($v) == "igk_default"){
                //     echo "stop here ";
                //     $modify = $v->getCanModify();
                //     echo "modify ".$modify; 
                // }

                if(get_class($v) === __PHP_Incomplete_Class::class){
                    // unset($tab[$k]);
                    continue;
                }
                if(IGKControllerManagerObject::IsSystemController($v) || IGKControllerManagerObject::IsIncludedController($v) || !$v->getCanModify())
                    continue;
                if($callbackfilter && !$callbackfilter($v)){
                    igk_wln("failed to implement");
                    continue;
                }
                $out[]=$v;
                
            }
        } 
        return $out;
    }
    ///<summary>init all controllers</summary>
    /**
    * init all controllers
    * @param IGKApp application controller
    */
    private function InitControllers(IGKApp $app){
        if (func_num_args()>1){
            igk_die("init controller with extra argument not allowed");
        }
        if (igk_env_count(__METHOD__)>1){
            /// TODO: avoid init controller twice
            /// case in bafon command --project:list
            ///
            igk_wln(__METHOD__, "Int countroller twice : only allowed one. ");
        }
        $initialize_all = $app->getConfigs()->init_all_controller;

        $_init_callback=function() {
            // + | hook global controller init complete
            igk_hook(IGKEvents::HOOK_CONTROLLER_INIT_COMPLETE, [$this]);
            $this->onInitComplete();
        };
        if(igk_is_singlecore_app()){
            $this->registerController( new SystemUriActionController());
            $c=igk_sys_getconfig("default_controller");
            if(empty($c) || !class_exists($c, false)){
                igk_die("no controller found to be a single application");
            }
            $v_ctrl=new $c();
            $this->_registerCtrl($v_ctrl, null); // empty($rn) ? null: $rn);
            BaseController::RegisterInitComplete($v_ctrl);
            $_init_callback();
            return;
        }
        $no_cache = defined("IGK_NO_CACHE_LIB");
        $sysload=false;
        $fc=self::FileCtrlCache();
        if(!$no_cache){
            // $sf = ['IGKMySQLDataCtrl'=> \IGK\System\Database\MySQL\IGKMySQLDataCtrl::class];
            if(file_exists($fc)){
                // igk_ilog("load controller from cache: ".$fc);
                $caches = include($fc);
                $resolvCtrl = & self::GetResolvController();
                // igk_wln_e("load from cache", $caches, $resolvCtrl);
                foreach($caches as $m){
                    $d = array_filter(explode("|", $m));
                    $cl = trim($d[0]);
                    if (empty($cl))
                        continue;
                        
                    $reg_name = trim($d[1]);
                    $reg_cname = trim($d[2]); 
                    $resolvCtrl[$reg_cname] = $cl;
                    if (1 || $initialize_all){
                        $v_ctrl = new $cl();
                        $this->registerController($v_ctrl, true);   
                    }
                }                 
                $sysload=true; 
            }
        }       

        if(!$sysload){
            $sfc="GetCanCreateFrameworkInstance";
            $tempty=array();
            $m = "";
            $tab = self::GetRegisteryController();
            $declared = array_merge(
                array_combine($keys=get_declared_classes(), $keys), 
                $tab);


            $loaded = [];
            foreach($declared as $k=>$v){
                if (isset($loaded[$v]))
                    continue;                
                if(is_subclass_of($v, NonAtomicTypeBase::class)){
                    continue;
                } 
                if(is_subclass_of($v, BaseController::class)){

                    $v_rc=igk_sys_reflect_class($v);
                    if($v_rc->isAbstract() || (method_exists($v, $sfc) && !call_user_func_array(array($v, $sfc), $tempty)))
                        continue;
                    if (($_vctrl =  $v_rc->getConstructor()) && $_vctrl->isPrivate())
                        continue;
                    $loaded[$v] = 1;
                    $t = new $v();
                    $this->_registerCtrl($t);
                    ConfigControllerRegistry::RegisterInitComplete($t);
                    $m .= "'".$t->getCacheInfo()."',".IGK_LF; 
                }
            }  
            if(!$no_cache){
               igk_io_w2file($fc, PHPScriptBuilderUtility::GetArrayReturn($m, $fc), true);
            }
        }
        $_init_callback(); 
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="new" default="false"></param>
    /**
    * 
    * @param mixed $ctrl
    * @param mixed $new the default value is false
    */
    public function initCtrl($ctrl, $new=false){
        $n=strtolower($ctrl->getName());
        if(!isset($this->m_tbcontrollers[$n])){
            $this->$n=$ctrl;
            BaseController::RegisterInitComplete($ctrl);
        }
        $this->onInitComplete();
        if($new)
            $this->storeControllerLibCache();
    }
    ///used to invoke function and return response. main used in igk_api
    /**
    */
    public function InvokeFunctionUri($uri=null){
        $c=null;
        $f=null;
        if($uri == null){
            $c=igk_getru("c", null);
            $f=igk_getru("f", null);
        }
        else{
            $args=igk_getquery_args($uri);
            $c=str_replace("-", "_", igk_getv($args, "c"));
            $f=str_replace("-", "_", igk_getv($args, "f"));
        }
        if($c && $f){
            if($this->$c && $this->$c->IsFunctionExposed($f)){
                $this->$c->App->Session->URI_AJX_CONTEXT=IGKString::EndWith($f, IGK_AJX_METHOD_SUFFIX) || (igk_getr("ajx") == 1);
                return $this->$c->$f();
            }
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="uri"></param>
    /**
    * 
    * @param mixed $uri
    */
    public function InvokeNavUri($uri){
        $args=igk_getquery_args($uri);
        $c=str_replace("-", "_", igk_getv($args, "c"));
        $f=str_replace("-", "_", igk_getv($args, "f"));
        $p=igk_getv($args, "p");
        $l=igk_getv($args, "l");
        if($p){
            igk_getctrl(IGK_MENU_CTRL)->setPage($p, igk_getv($args, "pageindex", 0));
        }
        if($l){
            R::ChangeLang(igk_getv($args, "l"));
        }
        $bck=$_REQUEST;
        $arg=igk_io_arg_from($f);
        $_REQUEST=array_merge($bck, $args);
        if($c && $f){
            $ctrl=$this->$c;
            if(($ctrl) && method_exists(get_class($ctrl), $f) && $ctrl->IsFunctionExposed($f)){
                if(is_array($arg))
                    call_user_func_array(array($this->$c, $f), $arg);
                else{
                    if($arg)
                        $ctrl->$f($arg);
                    else
                        $ctrl->$f();
                }
            }
        }
        $_REQUEST=$bck;
    }
    ///<summary></summary>
    ///<param name="pattern"></param>
    /**
    * 
    * @param mixed $pattern
    */
    public function InvokePattern($pattern){
        return $this->InvokeUri($pattern->value, 1, $pattern);
    }
    ///<summary>use to invoke system controller method</summary>
    ///<return>the selected uri</return>
    /**
    * use to invoke system controller method
    */
    public function InvokeUri($uri=null, $defaultBehaviour=true, $pattern=null){

		
	   igk_sys_handle_uri();
        $c=null;
        $f=null;
        $args=null;
        $igk=igk_app();
        $igk->Session->URI_AJX_CONTEXT=0;
        if($uri == null){
            if(($p=igk_getr("p", null)) != null){
                if(igk_sys_is_page($p)){
                    $igk->CurrentPage=$p;
                }
            }
            if(igk_getr("l", null) != null){
                R::ChangeLang(igk_getr("l"));
            }
            if(igk_getr("history", 0) == 1){
                igk_debug_wln("notice:form history");
            }
            $c=igk_getru("c", null);
            $f=igk_getru("f", "invokeUri");
        }
        else{
            $args=igk_getquery_args($uri);
            $c=str_replace("-", "_", igk_getv($args, "c"));
            $f=str_replace("-", "_", igk_getv($args, "f"));
            $p=igk_getv($args, "p");
            $l=igk_getv($args, "l");
            if($p){
                igk_getctrl(IGK_MENU_CTRL)->setPage($p, igk_getv($args, "pageindex", 0));
                unset($args["p"]);
            }
            if($l){
                R::ChangeLang(igk_getv($args, "l"));
                unset($args["l"]);
            }
        }
        $arg=igk_io_arg_from($f);
        if($c && $f){ 
            $ctrl=$this->$c ?? ($pattern ? $pattern->ctrl: null) ?? igk_template_create_ctrl($c);
            if(!$ctrl){
                return null;
            }
            if(!method_exists($ctrl, $f)){
                igk_html_output(404);
                if(!igk_get_contents($ctrl, 404, ["method not found"])){
                    igk_die("method not exists --- > [".get_class($ctrl)."::".$f."] ".$uri);
                }
                igk_exit();
                return false;
            }
            if($f == IGK_EVALUATE_URI_FUNC){
                igk_app()->setBaseCurrentCtrl($ctrl);
            }

            if(($f == IGK_EVALUATE_URI_FUNC) || $ctrl->IsFunctionExposed($f)){
                igk_app()->Session->URI_AJX_CONTEXT=igk_is_ajx_demand() || IGKString::EndWith($f, IGK_AJX_METHOD_SUFFIX) || (igk_getr("ajx") == 1);
                $fd=null;
                // if(($fd=$ctrl->getConstantFile()) && file_exists($fd))
                //     include_once($fd);
                if(($fd=$ctrl->getDbConstantFile()) && file_exists($fd))
                    include_once($fd);
                unset($fd);
                igk_set_env(IGK_ENV_REQUEST_METHOD, strtolower(get_class($ctrl)."::".$f));
                igk_set_env(IGK_ENV_INVOKE_ARGS, $args);
				// igk_wln(__FILE__.":".__LINE__, "invoke : ".$f);
                if(is_array($arg))
                    call_user_func_array(array($ctrl, $f), $arg);
                else{
                    if($arg)
                        $ctrl->$f($arg);
                    else{
                        $ctrl->$f();
                    }
                }
                igk_set_env(IGK_ENV_INVOKE_ARGS, null);
                igk_set_env(IGK_ENV_REQUEST_METHOD, null);
                if($defaultBehaviour && $this->$c->App->Session->URI_AJX_CONTEXT){
                    igk_exit();
                }
            }
        }
        return $c;
    }
    ///<summary></summary>
    ///<param name="controller"></param>
    /**
    * 
    * @param mixed $controller
    */
    public static function IsIncludedController($controller){
        $instance=self::getInstance();
        $dir=null;
        if(is_string($controller)){
            $controller=strtolower($controller);
            $v=$instance->$controller;
            $dir=dirname($v->getDeclaredFileName());
        }
        else if(is_object($controller) && igk_reflection_class_extends(get_class($controller), BaseController::class)){
            $dir=dirname($controller->getDeclaredFileName());
        }
        $o  =  igk_io_basepath(IO::GetDir(IGK_LIB_DIR."/".IGK_INC_FOLDER));
        $dir=  igk_io_basepath($dir);
        $i=0;
        while($o && (strlen($dir) > 0) && !preg_match("/^(\.|\/|\\\\)$/", $dir)){
            if($dir === $o){
                return true;
            }
            $dir=dirname($dir);
            if (($dir == "..")|| ($dir ==".")){
                return false;
            }
        }
        return false;
    }
    ///<summary>true if controller is a system controller otherwise false</summary>
    ///<param name="controller">controller name or controller instance </param>
    ///<note>for the plateform SystemController are controller stored in the Global Lib directory or in e </note>
    /**
    * true if controller is a system controller otherwise false
    * @param mixed $controller controller name or controller instance
    */
    public static function IsSystemController($controller){
        $instance=self::getInstance();
        if(is_string($controller)){
            $controller=strtolower($controller);
            $v=$instance->$controller;
            if($v->getDeclaredFileName() == __FILE__)
                return true;
        }
        $cl="";
        if(is_object($controller) && ($controller instanceof BaseController)){
            $v=strstr($controller->getDeclaredFileName(), IGK_LIB_DIR);
            $r=($v) || \IGK\Controllers\RootControllerBase::IsSystemController($controller) || BaseController::IsSysController($controller);
            return $r;
        }
        return false;
    }
    ///<summary>raise init complete event</summary>
    /**
    * raise init complete event
    */
    private function onInitComplete(){
        ConfigControllerRegistry::InvokeRegisterComplete();
        if(defined('IGK_NO_WEB'))
            return; 
        if(!$this->m_initEvent){
            $this->m_initEvent=1;
            igk_hook("sys://event/defaultpagechanged", array($this, "defaultpagechanged"));
        }
    }
    ///<summary>register controller for specific fonctionnality</summary>
    /**
    * register controller for specific fonctionnality
    * @deprecated register controller not allowed
    */
    public function register($name, $ctrl){        
        $this->m_tbcontrollers[get_class($ctrl)] = $ctrl; 
    }    
    ///<summary>register controller. register to init complete</summary>    
    /**
     * register controller. register to init complete
     */
    public function registerController($controller, $initComplete = true){
        $this->_registerCtrl($controller);
        $initComplete && ConfigControllerRegistry::RegisterInitComplete($controller);
    }
     
    ///<summary>reload controller table list</summary>
    /**
    * reload controller table list
    */
    public function reloadModules($tab, $redirect, $initCtrl=1){
        igk_set_env("sys://reloadingCtrl", 1);
        $dir=igk_io_projectdir();
        //$g=$this->m_tbcontrollers;
        if($initCtrl){
            $this->m_tbcontrollers=array(); 
        }
        igk_loadlib($dir);
        $classes=get_declared_classes();
        foreach($classes as $v){
            if(isset($tab[$v])){
                $n=$tab[$v];
                if(isset($this->m_tbcontrollers[$n])){
                    igk_die($n. " must not exists in controller tab");
                }
                continue;
            }
            if(igk_reflection_class_extends($v, IGK_CTRLNONATOMICTYPEBASECLASS)){
                continue;
            }
            if(is_subclass_of($v, BaseController::class)){
                $v_rc=igk_sys_reflect_class($v);
                if($v_rc->isAbstract())
                    continue;
                $t=new $v();
                $n=strtolower($t->Name);
                if(!isset($this->m_tbcontrollers[$n])){
                    igk_ilog_assert(igk_is_debug(), "register create new  : >>>> ".$v." ? ".isset($tab[$v]));
                    $this->$n=$t;
                    BaseController::RegisterInitComplete($t);
                }
                else{
                    unset($t);
                }
            }
        }
        $this->onInitComplete();
        igk_notification_push_event("sys://notify/ctrl/reload", $this);
        igk_set_env("sys://reloadingCtrl", null);
        if($redirect){
            igk_navtocurrent();
        }
    }
    ///<summary></summary>
    ///<param name="s"></param>
    ///<param name="v"></param>
    ///<param name="fname"></param>
    ///<param name="fsize"></param>
    ///<param name="x"></param>
    ///<param name="y"></param>
    ///<param name="cl"></param>
    ///<param name="indexpos"></param>
    /**
    * 
    * @param mixed $s
    * @param mixed $v
    * @param mixed $fname
    * @param mixed $fsize
    * @param mixed $x
    * @param mixed $y
    * @param mixed $cl
    * @param mixed $indexpos the default value is 0
    */
    private function renderController($s, $v, $fname, $fsize, $x, $y, $cl, $indexpos=0){
        $t=$s->DrawString($v->Name, $fname, $fsize, $x, $y, $cl);
        $s->DrawString($v->Configs->clTargetNodeIndex, $fname, $fsize, $indexpos, $y, $cl);
        if($v->Childs){
            $y += $t->height;
            $tab=$v->Childs;
            usort($tab, array($this, "_sort_byConfigNodeIndex"));
            $i=igk_count($tab);
            foreach($tab as  $m){
                $i--;
                $tt=$this->renderController($s, $m, $fname, $fsize, $x + 50, $y, $cl, $indexpos);
                $t->height += $tt->height;
                $t->width=max($tt->width + 50, $t->width);
                $y += $tt->height;
            }
        }
        return $t;
    }
    ///<summary>store system controller library</summary>
    /**
    * store system controller library
    */
    private function storeControllerLibCache(){
         $fc=self::FileCtrlCache();
        if(empty($fc))
            return;
        @unlink($fc);
        $m=" ";
        foreach($this->m_register as $k=>$v){
            $m .= "'".$v->getCacheInfo()."'".IGK_LF;
        }
        igk_io_w2file($fc, igk_cache_array_content($m, $fc), true);
        IGKSysCache::Init_CachedHook();
    }
     
    ///<summary></summary>
    ///<param name="forceview"></param>
    /**
    * 
    * @param mixed $forceview the default value is 0
    */
    public function ViewControllers($forceview=0){
        $u=igk_io_base_request_uri();
        $u = explode("?", $u)[0]; 
 
        if($forceview || (!igk_sys_is_subdomain()) && (preg_match('#^(\/|index.php)?$#', $u))){
            $ctrls=self::getInstance()->m_tbviewcontrollers;
            if($ctrls){
                foreach($ctrls as $k){
                    if(($k->getWebParentCtrl() !== null) || (igk_own_view_ctrl($k))){
                        continue;
                    }
                    $k->View();

                }
            }
        }
    }

    /**
     * retrieve controller
     * @param mixed $ctrlname 
     * @param int $throwex 
     * @return mixed 
     * @throws IGKException 
     */
    public function getController($ctrlname, $throwex = 1){
        $cc = $this;
        $app  = IGKApp::getInstance();
        if(!$app){
            echo $ctrlname . " \n";             
            if($throwex){ 
                igk_die("/!\\ Application not initialized. can't get controller ".$ctrlname);
            }
            return null; 
        }
        if(is_string($ctrlname)){
            $ctrlname=trim($ctrlname);
            if (empty($ctrlname)){
                return null;
            }
     
            if($cc === null || !is_object($cc)){
                if($throwex){
                    igk_die("igk_app() ControllerManager is null. Session probably destroyed.".IGKApp::IsInit());
                }
                return null;
            }
            $v=$cc->$ctrlname;
            if($v == null){
                $v=igk_init_ctrl($ctrlname);
            }
            if($throwex && ($v === null)){   
                $msg  = __("Controller [{0}] not found", $ctrlname);
                igk_environment()->is("DEV") && igk_wln_e($ctrlname, $msg, igk_ob_get_func("igk_trace"));
                igk_die($msg);
            }
            return $v;
        }
        else{
            if(is_object($ctrlname) && igk_is_ctrl($ctrlname)){
                return $ctrlname;
            }
        }
        return null;
    }

    public static function InitController($ctrlname){        
        $n= self::GetSystemController($ctrlname); // igk_sys_get_controller($ctrlname);
        if (($n===null) && class_exists($ctrlname)){
            $n = $ctrlname;
        }     
        if(!empty($n) && class_exists($n)){
            if ($man= self::$sm_instance){                 
                $o = $man->getControllerInstance($n);
                return $o;
            }
        }
        return null;
    }

    public static function GetSystemController($n){
        
        $b= igk_environment()->get("sys://app/controllers");
        if($b && $n){
            if ($g = igk_getv($b, $n))
                return $g; 
        }
        $g = self::GetRegisteryController();

        return igk_getv(self::GetRegisteryController(), $n);
    }
    public static function GetRegisteryController(){
        $g = ConfigControllerRegistry::GetResolvController(); 
        return $g;
    }
    /**
     * get .controller.pinc registrated
     * @return mixed 
     */
    public static function & GetResolvController(){
        static $resolv_ctrl;
        if ($resolv_ctrl === null){
            $resolv_ctrl = include(IGK_LIB_DIR."/.controller.pinc");
        }
        return $resolv_ctrl;
    }
}