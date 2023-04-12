<?php
namespace IGK\Controllers;

use Error;
use Exception;
use IGK\Helper\IO; 
use IGK\ApplicationLoader;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\Exceptions\ApplicationModuleInitException;
use IGKException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\IO\Path;
use Throwable;
use TypeError;

// @author: C.A.D. BONDJE DOUE
// @licence: IGKDEV - Balafon @ 2019
// @Description: Use to add extra module to system. that module include function declared on .module.pinc file with the $reg array
 
///<summary>represent application module class </summary>
/**
* represent application module class
* @method function initDoc($doc, ...$args) initialize document
*/
final class ApplicationModuleController extends BaseController{
    const INIT_METHOD = "initDoc";
    const CONF_MODULE = "balafon.module.json";
    const MODULE_INITIALIZER_FNAME = ".module.pinc";
    private $m_dir;
    private $m_doc;
    private $m_fclist;
    private $m_listener;
    private $m_src;             // source code 
    private $m_initializer;     // used to extend module class properties
    private $m_configs;         // configuration 
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
    public function __debugInfo()
    {
        return null;
    }
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
    public function supportMethod($method){
        return is_callable(igk_getv($this->m_fclist, $method));
    }

    public function initClass($classname){
        if (class_exists($classname)){
            $this->m_initializer = new $classname();
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="args"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $args
    */
    function __call($n, $args){

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
            $o=call_user_func_array($fc, $args);
            $dc=igk_pop_env(__CLASS__."/callee");
            return $o;
        } 
        // igk_die("/!\\ function {$n} not define");
        return null;
    }
    
    
    protected function getModuleKey($name=""){
        $s = "module://".$this->name;
        if (!empty($name = trim($name, "/"))){
            $s.= "/".$name;
        }
        return $s;
    }
    public function setEnvParam($name, $value){
        igk_set_env($this->getModuleKey($name), $value); 
        return $this;
    }
    public function getEnvParam($name, $default=null){
        return igk_get_env($this->getModuleKey($name), $default); 
    }
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
    public function getAssetsDir(){
        if ($fc=igk_getv($this->m_fclist, __FUNCTION__)){
            return $this->__call(__FUNCTION__, []);
        }
        return Path::Combine($this->getDeclaredDir(),"/Data/assets");
    }
    ///<summary></summary>
    ///<param name="dir"></param>
    /**
    * 
    * @param mixed $dir
    */
    public function __construct($dir){
        parent::__construct();
        $this->m_dir=IO::GetDir($dir);
        $this->mm_fclist=array();
        // $tf = $dir."/Lib/".self::;
        // $c=realpath($tf);
        // if(!file_exists($c)){
        //     $configs=array();
        //     $this->_initconfig($configs);
        //     $o="<?php\n";
        //     if(count($configs) > 0){
        //         foreach($configs as $k=>$m){
        //             $o .= "\$config[\"{$k}\"] = \"{$m}\";\n";
        //         }
        //         igk_io_w2file($tf, $o);
        //     }
        // } 
        $this->_initModuleClasses();
        $c=realpath($dir."/.module.pinc");
        if(file_exists($c)){
            $this->_init($c);
        }  
    }
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
                // igk_wln("try load ".$n . " ".$this->getName());
                $fc = "";
                if (!empty($entry_ns) && (strpos( $n, $entry_ns)===0)){
                    $cl = ltrim(substr($n, strlen($entry_ns)), "\\");
                    if (file_exists($fc = igk_dir($libdir."/".$cl.".php"))){
                        include($fc);
                        if (!class_exists($n, false) && !interface_exists($n, false) && !trait_exists($n, false)){               
                            igk_die("file loaded but {$n}, interface or trait not exists");
                        }
                        return 1;
                    }
                    if (defined("IGK_TEST_INIT")){
                        $pos = $entry_ns."\\Tests\\";
                        if (strpos($n, $pos)=== 0){ 
                            $cl = ltrim(substr($n, strlen($pos)), "\\");
                            if (file_exists($fc = $this->getTestClassesDir()."/".$cl.".php")){
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
    ///<summary></summary>
    /**
    * 
    */
    function __sleep(){
        $this->m_fclist=array();
        $this->m_src=null;
        return array("m_dir");
    }
    ///<summary></summary>
    /**
    * 
    */
    function __wakeup(){
        $this->_init();
    }
    ///<summary></summary>
    ///<param name="c" default="null"></param>
    /**
    * 
    * @param mixed $c the default value is null
    */
    private function _init($c=null){
 
        $s=igk_io_read_allfile($c ?? $this->m_dir."/".self::MODULE_INITIALIZER_FNAME);
        // + | --------------------------------------------------------------------
        // + | $reg is a function used to register additional function 
        // + |         
        if (!is_file($file =  $this->m_dir."/".self::CONF_MODULE)){
            igk_die(sprintf("%s is missing in %s",self::CONF_MODULE, $this->m_dir));
        }
        $definition = (array)json_decode(file_get_contents($file));
        try{ 
            extract((function(){
                return ['reg'=>function($name, $callback){
                    $this->reg_function($name, $callback);
                }];
            })());
            $data = eval("?>".$s);
            $data = array_merge($data??[], $definition);
        }
        catch(TypeError $error){
            throw new ApplicationModuleInitException($this, 500, $error);            
        }
        catch(Error $ex){
            // catch fatal - error
            throw new ApplicationModuleInitException($this, 500, $ex);            
        }
        catch(Throwable $ex){
            throw new ApplicationModuleInitException($this, 500, $ex);            
        }
        $this->m_src = $s;
        if ($data){
            $this->m_configs = $data; 
        }
        // + | --------------------------------------------------------------------
        // + | unset source for production
        // + |
        
        unset($this->m_src);
    }
    ///<summary></summary>
    ///<param name="configs" ref="true"></param>
    /**
    * 
    * @param  * $configs
    */
    protected function _initconfig(& $configs){
        $configs["libdir"]= igk_io_collapse_path(IGK_LIB_DIR); 
    }
    ///<summary></summary>
    ///<param name="msg"></param>
    /**
    * 
    * @param mixed $msg
    */
    private function bindError($msg){
        $this->setParam(__METHOD__, $msg);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getAppDocument(){
        return null;
    }
    ///<summary></summary>
    ///<param name="c" default="null"></param>
    /**
    * 
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
    ///<summary></summary>
    /**
    * 
    */
    public function getCallee(){
        return igk_peek_env(__CLASS__."/callee");
    }
    ///<summary>get the inline calling function</summary>
    /**
    * get the inline calling function
    */
    public function getCaller(){
        return $this->m_caller;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function GetCanCreateFrameworkInstance(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCurrentDoc(){
        return $this->m_doc;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDeclaredDir():string{
        return $this->m_dir;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDeclaredFileName(){
        return realpath($this->getDeclaredDir()."/.module.pinc");
    }
    public function getLibDir(){
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER]);
    }
    ///<summary>get module environment configuration</summary>
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
        if(file_exists($c)){
            $c = realpath($this->m_dir."/Lib/.config.php");
            $config=array();
            include($c);
            $_configs[$_hash]=(object)$config;
        }
        return $_configs[$_hash];
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getListener(){
        return $this->m_listener ?? igk_ctrl_current_view_ctrl();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getName(){
        return strtolower(str_replace("/", ".", igk_uri(substr($this->m_dir, strlen(igk_get_module_dir())))));
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="def" default="null"></param>
    ///<param name="register" default="false"></param>
    ///<return refout="true"></return>
    /**
    * 
    * @param mixed $n
    * @param mixed $def the default value is null
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
    ///<summary></summary>
    ///<param name="c" default="null"></param>
    /**
    * 
    * @param mixed $c the default value is null
    */
    public function getUri($c=null){
        return $this->getAppUri($c);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function methodExists($n){
        return isset($this->m_fclist[$n]);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="fc"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $fc
    */
    protected function reg_function($n, $fc){
        if (is_string($fc) && (strpos($fc, '@')!== false)){
            $fc = \IGK\Helper\PhpHelper::GetCallable($fc);
        }
        $this->m_fclist[$n]=$fc;
    }
    ///<summary></summary>
    ///<param name="doc"></param>
    /**
    * attach to current document
    * @param mixed $doc
    */
    private function setCurrentDoc($doc){
        $this->m_doc=$doc;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setListener($v){
        $this->m_listener=$v;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function setParam($n, $v){
        $l=$this->Listener;
        if($l){
            $l->setParam($this->Name."/{$n}", $v);
        }
    }
    public function set($name, $value){
        return $this->setEnvParam($name, $value);
    }
    public function get($name, $default=null){
        return $this->getEnvParam($name, $default);
    }

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
        return null; 
    }
    /**
     * use allway schema to update the user
     * @return true 
     */
    public function getUseDataSchema():bool{ 
        return true;
    }
    public function getDataSchemaFile(){
        return ControllerExtension::getDataSchemaFile($this);
    }
    /**
     * all module can participate to init db by default
     * @return bool 
     */
    public function getCanInitDb():bool{
        return true;
    }
    public function __toString()
    {
        return sprintf("%s - [%s]", __CLASS__, $this->getName());
    }
}
