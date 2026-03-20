<?php

// @author: C.A.D. BONDJE DOUE
// @licence: IGKDEV - Balafon @ 2019
// @Description: Use to add extra module to system. that module include function declared on .module.pinc file with the $reg array

namespace IGK\Controllers;
use Error;
use Exception;
use IGK\Helper\IO; 
use IGK\ApplicationLoader;
use IGK\Constants;
use IGK\Helper\Activator;
use IGK\System\Configuration\ModuleConfiguration; 
use IGK\System\Controllers\ControllerMethods;
use IGK\System\Diagnostics\Debugs;
use IGK\System\Exceptions\ApplicationModuleInitException; 
use IGKException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\IO\Path;
use IGK\System\Modules\Traits\ModuleIncludeDefinitionInvokeTrait; 
use Throwable;
use TypeError;

/**
* represent application module class
* @method function initDoc($doc, ...$args) initialize document
*/
final class ApplicationModuleController extends BaseController{
    use ModuleIncludeDefinitionInvokeTrait;
    /**
    * Constant: init doc method.
    * @var mixed
    */
    const INIT_DOC_METHOD = "initDoc";
    /**
     * constant: method used to init the module when first loaded 
     */
    const DID_INIT_METHOD = "didInitModule";

    /**
    * Constant: conf module.
    * @var mixed
    */
    const CONF_MODULE = Constants::MODULE_CONF_FILE;

    /**
    * Constant: module initializer fname.
    * @var mixed
    */
    const MODULE_INITIALIZER_FNAME = ".module.pinc";

    /**
    * Path to dir.
    * @var mixed
    */
    private $m_dir;

    /**
    * Property: doc.
    * @var mixed
    */
    private $m_doc;

    /**
    * Collection of fclist.
    * @var mixed
    */
    private $m_fclist;

    /**
    * Listener: listener.
    * @var mixed
    */
    private $m_listener;

    /**
    * Property: src.
    * @var mixed
    */
    private $m_src;             // source code

    /**
    * Property: initializer.
    * @var mixed
    */
    private $m_initializer;     // used to extend module class properties

    /**
    * Property: configs.
    * @var mixed
    */
    private $m_configs;         // configuration

    /**
    * Property: boot.
    * @var mixed
    */
    var $boot;
    /**
     * get application module configuration value
     * @param mixed $name 
     * @param mixed $default 
     * @return mixed 
     * @throws IGKException 
     */
    public function config($name, $default=null){
        return igk_conf_get($this->m_configs, $name, $default, 1);
    }

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return null;
    }

    /**
    * Returns true if Module.
    * @param mixed $controllerClass
    */
    public static function IsModule($controllerClass){
        return is_object($controllerClass) && (get_class($controllerClass) == static::class) && strstr($controllerClass->getDeclaredDir(), igk_get_module_dir());
    }
    /**
     * module always target system data adapter
     * @return mixed
     */

    public function getDataAdapter(){
        return SysDbController::ctrl()->getDataAdapter();
    }
    /**
     * check if this module extends methods
     * @param mixed $method 
     * @return bool 
     * @throws IGKException 
     */

    public function supportMethod($method):bool{
        return is_callable(igk_getv($this->m_fclist, $method));
    }

    /**
    * Initializes Class.
    * @param mixed $classname
    */
    public function initClass($classname){
        if (class_exists($classname)){
            $this->m_initializer = new $classname();
        }
    }

    /**
    * Environment settings.
    */
    public function environmentSettings(){
        $e = igk_environment();
        $v_k = str_replace('.', '\\', trim($this->getName(), '.'));
        return $e->get($e->find($v_k));
    }

    /**
    * invoke internal function list 
    * @param string $name
    * @param mixed $args
    */
    function __call($name, $args){
        $n = $name;
        $fc=igk_getv($this->m_fclist, $n);
        if($fc){
            // + | check that in methods can be initialize
            if ($env_param = $this->getEnvParam($n)){
                if (method_exists(ApplicationModuleMethodChecker::class , $n)){                
                    if (!ApplicationModuleMethodChecker::$n($this, $env_param)){
                        return null;
                    }
                }
            } 
            igk_push_env(__CLASS__."/callee", $n);
            $o = $this->invokeInclusion($name, $args);
            // $o=call_user_func_array($fc, $args);
            $dc=igk_pop_env(__CLASS__."/callee");
            return $o;
        } 
        return null;
    }

    /**
    * Returns Module Key.
    * @param mixed $name
    */
    protected function getModuleKey($name=""){
        $s = "module://".$this->name;
        if (!empty($name = trim($name, "/"))){
            $s.= "/".$name;
        }
        return $s;
    }
    /**
     * set environment params
     * @param mixed $name 
     * @param mixed $value 
     * @return $this 
     */

    public function setEnvParam($name, $value){
        igk_set_env($this->getModuleKey($name), $value); 
        return $this;
    }

    /**
    * Returns Env Param.
    * @param mixed $name
    * @param null|mixed $default
    */
    public function getEnvParam($name, $default=null){
        return igk_get_env($this->getModuleKey($name), $default); 
    }

    /**
    * Returns Entry Namespace.
    */
    public function getEntryNamespace(){
        return str_replace("/","\\", $this->config("entry_NS",igk_get_module_name($this->m_dir)));
    }
    /**
     * override this to get classes dir
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */

    public function getTestClassesDir(){
        if ($fc=igk_getv($this->m_fclist, __FUNCTION__)){
            return $this->__call(__FUNCTION__, []);
        }
        return implode('/' , [dirname($this->getClassesDir()), IGK_TESTS_FOLDER]);
    }
    /**
     * get assets dir
     * @return mixed 
     * @throws Exception 
     * @throws EnvironmentArrayException 
     */

    public function getAssetsDir(){
        if ($fc=igk_getv($this->m_fclist, __FUNCTION__)){
            return $this->__call($fc, []);
        }
        return Path::Combine($this->getDeclaredDir(),"/Data/assets");
    }

    /**
    * Base module constructor. 
    * @param string $dir base directory
    */
    public function __construct(string $dir){
        parent::__construct();
        $this->m_dir=IO::GetDir($dir);
        $this->m_fclist=[];       
        $this->_initModuleClasses();
        $c=realpath($dir."/.module.pinc");
        if(igk_io_file_exists($c, true)){
            $this->_init($c);
        }  
        igk_reg_hook('sys://module/didInitModule', function($e){
            if ($e->args['module'] === $this){
                $this->didInitModule();
            }
        });
    }
    /**
     * 
     * @return mixed 
     */
    protected function &getInvocationList()
    {
        return $this->m_fclist;
    }

    /**
    * auto generate doc.
    * @return
    */
    private function _initModuleClasses(){
        $dir = $this->getDeclaredDir();
        $classLib = $dir."/Lib/Classes"; 
        if (is_dir($classLib)){
            if (!empty($dir) &&  is_link($dir)){
                $dir = @readlink($dir);
            } 
            if (!is_dir($dir)){
                $dir = "";
            } 
            $entry_ns =  str_replace("/","\\", $this->config("entry_NS",igk_get_module_name($dir)));
            $libdir=$classLib;  
            $fc = function($n)use($entry_ns, $libdir){ 
                $fc = "";
                //  if ($n ==\igk\js\Vue3\Components\VueApplicationNode::class){
                //   igk_wln_e("try load ".$n . " ".$this->getName(), $entry_ns, $dir = $this->getDeclaredDir());
                //  }
                if (!empty($entry_ns) && (strpos( strtolower($n), strtolower($entry_ns.'\\'))===0)){
                    // and matching start of the entry namespace
                    $cl = ltrim(substr($n, strlen($entry_ns)), "\\");
                    if (igk_io_file_exists($fc = igk_dir($libdir."/".$cl.".php"), true)){                         
                        include($fc);                        
                        if (!class_exists($n, false) && !interface_exists($n, false) && !trait_exists($n, false)){               
                            igk_die("file loaded but {$n}, interface or trait not exists");
                        }
                        return 1;
                    }
                    if (igk_environment()->isTesting()){
                        $pos = $entry_ns."\\Tests\\";
                        if (strpos($n, $pos)=== 0){ 
                            $cl = ltrim(substr($n, strlen($pos)), "\\");
                            if (igk_io_file_exists($fc = $this->getTestClassesDir()."/".$cl.".php", true)){
                                include($fc);
                                if (!class_exists($n, false) && !interface_exists($n, false)){               
                                    igk_die("file loaded but class $cl does not exists");
                                }
                                return 1;
                            } 
                        } 
                    }
                } 
            };
            ApplicationLoader::RegisterAutoload($fc, $libdir);
        }
    }

    /**
    * auto generate doc.
    */
    function __sleep(){
        $this->m_fclist=array();
        $this->m_src=null;
        return array("m_dir");
    }

    /**
    * auto generate doc.
    */
    function __wakeup(){
        $this->_init();
    }
    /**
    * init module 
    * @param mixed $c the default value is null
    */
    private function _init($c=null){
        $c_cfile = $c ?? $this->m_dir."/".self::MODULE_INITIALIZER_FNAME;
       
        $c_f = self::CONF_MODULE;
        // + | --------------------------------------------------------------------
        // + | $reg is a function used to register additional function 
        // + |         
        if (!is_file($file =  $this->m_dir."/".$c_f)){
            igk_die(sprintf("%s is missing in %s",$c_f, $this->m_dir));
        }
        $definition = (array)json_decode(file_get_contents($file));
        $s = '';
        $v_fc = (function(){
            // + | --------------------------------------------------------------------
            // + | MODULE: treat moduel.pinc definition 
            // + |            
            extract((function(){
                return ['reg'=>function($name, $callback){
                    $this->reg_function($name, $callback);
                }, '__file__'=>$this->getDeclaredFileName()];
            })());
             if (!empty(trim(func_get_arg(0)['code']))){
                eval("?>".func_get_arg(0)['code']);                 
             }              
            return isset(func_get_arg(0)['return']) ? eval(func_get_arg(0)['return']) : null;
        })->bindTo($this);
        try{ 
            $v_fclist = & $this->m_fclist;
            $v_is_debug = igk_is_debug(Debugs::balafon_module_loading);
            if ($__cache = \IGK\System\Modules\ModuleInitializer::Init($this, $c_cfile, $v_fclist)){
                $v_is_debug && igk_ilog($c_cfile, 'blf-module-loading');
                $data = call_user_func_array($v_fc, [$__cache]);
               
                // if (!empty(trim($__cache['code'])))
                //     eval("? >".$__cache['code']);               
                // $data = isset($__cache['return']) ? eval($__cache['return']) : null;
                $data = array_merge($data??[], $definition);
                $s = $__cache['code'];
            }
        }
        catch(\TypeError $error){
            igk_wln_e('lkjo', $__cache['code'], $error->getMessage());
            throw new ApplicationModuleInitException($this, 500, $error);            
        }
        catch(\Error $ex){
            igk_wln_e('lkjs');
            // catch fatal - error
            throw new ApplicationModuleInitException($this, 500, $ex);            
        }
        catch(\Throwable $ex){
            igk_wln_e('lkj');
            throw new ApplicationModuleInitException($this, 500, $ex);            
        }
        $this->m_src = $s;
        if ($data){
            $this->m_configs = Activator::CreateNewInstance(ModuleConfiguration::class, $data); 
        }
        // + | --------------------------------------------------------------------
        // + | unset source for production
        // + |
        unset($this->m_src);
    }
    /**
     * retrieve the module configuration
     * @return mixed 
     */
    public function getModuleConfig(){
        return $this->m_configs;
    }

    /**
    * auto generate doc.
    * @param * $configs
    */

    protected function _initconfig(& $configs){
        $configs["libdir"]= igk_io_collapse_path(IGK_LIB_DIR); 
    }

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    private function bindError($msg){
        $this->setParam(__METHOD__, $msg);
    }

    /**
    * auto generate doc.
    */
    public function getAppDocument(){
        return null;
    }

    /**
    * auto generate doc.
    * @param mixed $function the default value is null
    */

    public function getAppUri(?string $function=null):?string{
        $q="";
        if($this->Listener)
            $q="ctrl=".$this->Listener->Name;
        $u="n=".$this->Name.($q ? "&".$q: "")."".($function ? "&q=".$function: "");
        $s=base64_encode($u);
        return igk_getctrl(IGK_SESSION_CTRL)->getUri("invmodule&q=".$s);
    }

    /**
    * auto generate doc.
    */
    public function getCallee(){
        return igk_peek_env(__CLASS__."/callee");
    }
    /**
    * get the inline calling function
    */

    public function getCaller(){
        return $this->m_caller;
    }

    /**
    * auto generate doc.
    */
    public static function GetCanCreateFrameworkInstance(){
        return false;
    }

    /**
    * auto generate doc.
    */
    public function getCurrentDoc(){
        return $this->m_doc;
    }

    /**
    * auto generate doc.
    */
    public function getDeclaredDir():string{
        return $this->m_dir;
    }

    /**
    * auto generate doc.
    */
    public function getDeclaredFileName(){
        return realpath($this->getDeclaredDir()."/.module.pinc");
    }

    /**
    * Returns Lib Dir.
    */
    public function getLibDir(){
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER]);
    }
    /**
    * get module environment configuration
    */

    public function getEnvironmentConfigs(){
        /** @var string $c */
        static $_configs=null;
        if($_configs === null){
            $_configs=array();
        }
        $_hash=spl_object_hash($this);
        if(isset($_configs[$_hash])){
            return $_configs[$_hash];
        }
        if(igk_io_file_exists($c)){
            $c = realpath($this->m_dir."/Lib/.config.php");
            $config=array();
            include($c);
            $_configs[$_hash]=(object)$config;
        }
        return $_configs[$_hash];
    }

    /**
    * auto generate doc.
    */
    public function getListener(){
        return $this->m_listener ?? igk_ctrl_current_view_ctrl();
    }

    /**
    * auto generate doc.
    */
    public function getName(): string{
        return strtolower(str_replace("/", ".", igk_uri(substr($this->m_dir, strlen(igk_get_module_dir())))));
    }

    /**
    * auto generate doc.
    * @param mixed $register the default value is false
    * @return *
    */

    public function & getParam($n, $def=null, $register=false){
        $l=$this->Listener;
        $h=null;
        if($l){
            $h=$l->getParam($n, $def, $register);
        }
        return $h;
    }

    /**
    * auto generate doc.
    * @param mixed $c the default value is null
    */

    public function getUri($c=null){
        return $this->getAppUri($c);
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */

    public function methodExists($n){
        return isset($this->m_fclist[$n]);
    }
    /**
     * 
     * @return mixed 
     */
    public function & getReferenceModuleList(){
        return $this->m_fclist;
    }

    /**
    * auto generate doc.
    * @param mixed $fc
    */

    protected function reg_function($n, $fc){
        if (is_string($fc) && (strpos($fc, '@')!== false)){
            $fc = \IGK\Helper\PhpHelper::GetCallable($fc);
        }
        $this->m_fclist[$n]=$fc;
    }
    /**
    * attach to current document
    * @param mixed $doc
    */
    private function setCurrentDoc($doc){
        $this->m_doc=$doc;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setListener($v){
        $this->m_listener=$v;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setParam($n, $v){
        $l=$this->Listener;
        if($l){
            $l->setParam($this->Name."/{$n}", $v);
        }
    }

    /**
    * Sets.
    * @param mixed $name
    * @param mixed $value
    */
    public function set($name, $value){
        return $this->setEnvParam($name, $value);
    }

    /**
    * Returns.
    * @param mixed $name
    * @param null|mixed $default
    */
    public function get($name, $default=null){
        return $this->getEnvParam($name, $default);
    }

    /**
    * View.
    * @return BaseController
    */
    public function View(): BaseController{
        if ($this->methodExists(__FUNCTION__)){
            $fc = igk_getv($this->m_fclist, __FUNCTION__);
            $args = func_get_args();
            $fc(...$args); 
        }
        return $this;
    }
    /**
     * disable static call on module
     * @param mixed $name 
     * @param mixed $arguments 
     * @return null 
     */

    public static function __callStatic($name, $arguments)
    {        
        if(igk_environment()->isDev() && ($name=== ControllerMethods::register_autoload)){       
            igk_ilog("module app - invoke static method not allowed - ".$name);         
        }  
        return null; 
    }

    /**
    * Expose assets.
    */
    public function exposeAssets(){
        return ControllerExtension::exposeAssets($this);
    }

    /**
    * auto generate doc.
    * @param mixed $assets
    * @return never
    */

    public function resolveAssets($assets){ 
        return ControllerExtension::resolveAssets($this, $assets);
    }
    /**
     * use allway schema to update the user
     * @return true 
     */

    public function getUseDataSchema():bool{ 
        return true;
    }
    /**
     * retrieve db schema file 
     * @return string 
     * @throws IGKException 
     */

    public function getDataSchemaFile(){
        return ControllerExtension::getDataSchemaFile($this);
    }

    /**
    * Initializes Db From Schemas.
    */
    public function initDbFromSchemas(){
        return ControllerExtension::initDbFromSchemas($this);
    }

    /**
    * Loads Data And New Entries From Schemas.
    */
    public function loadDataAndNewEntriesFromSchemas(){
        return ControllerExtension::loadDataAndNewEntriesFromSchemas($this);
    }
    /**
     * all module can participate to init db by default
     * @return bool 
     */

    public function getCanInitDb():bool{
        return true;
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return sprintf("%s - [%s]", __CLASS__, $this->getName());
    }
    /**
     * resolve local class
     * @param string $name 
     * @return string|null 
     */

    public function resolveClass(string $name){
        $m = ControllerExtension::resolveClass($this, $name);
        return $m;
    }

    /**
    * Returns Setting Key.
    * @param ApplicationModuleController $ctrl
    */
    public static function GetSettingKey(ApplicationModuleController $ctrl){
        return sprintf('module://%s', trim($ctrl->getName(),'. '));
    }
}