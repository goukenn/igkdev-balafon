<?php

  
use IGK\Controllers\BaseController;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\Helper\IO ;
use IGK\Resources\R;
use IGK\System\IO\FileSystem;

use function igk_getv as getv;

///<summary>use to manage Server Environment</summary>
/**
* use to manage Server Environment
* @property string $subdomainctrl current subdomain controller
* @property string $basectrl base controller
* @property int    $querydebug activate of not the query debug
* @property array  $db_adapter get registered data adapters
* @property bool $no_lib_cache no library cache
* @property null|array $extra_config extra configuration file
* @property bool $handle_ctrl_request flag that handle controller request . subdomain usage
* @property bool $isAJXDemand flag that handle controller request . subdomain usage
* @property bool $no_handle_error flag that allow environment to handle exception
*/
final class IGKEnvironment extends IGKEnvironmentConstants{
    private static $sm_instance;
    /**
     * environment properties
     * @var array
     */
    private $m_envs; 
    // | default FOUR ENVIRONMENT TYPE
    private static $env_keys = [
        "DEV"=>"development",
        "TST"=>"testing",
        "ACC"=>"acceptance",
        "OPS"=>"production"
    ]; 
    
    public function getEnvironments(){
        return $this->m_envs;
    }
    public function getLocale(){
        return R::GetLocale();
    }
    /**
     * get true if environment is unix type
     */
    public function isUnix(){       
        return in_array(strtolower(PHP_OS), ["unix","linux", "darwin"]);
    }

    public function getPhpCoreVersion(){
        static $version;
        if ($version == null){
            list($major, $minor) = explode(".",  PHP_VERSION);
            $version = $major.".".$minor; 
        }
        return $version;

    }

    public function write_debug(string $message){ 
        $d = & $this->createArray("debug_load");
        $d[] = "<span>".(count($d) + 1)."</span> ".$message; 
    } 
    
    public function peek($n){
       
        $tab = $this->get($n);
        if (is_array($tab) && (($c = igk_count($tab)) > 0)) {
            $r = $tab[$c - 1];
            return $r;
        }
        return null;
    }
    /**
     * init this environment with a callable
     */
    public function init($key, callable $callback){
        $c = $this->get($key);
        if($c == null){
            $c=$callback();
            $this->set($key, $c);
        }
        return $c;
    }
    /**
     * get the environment base directory
     * @return mixed 
     */
    public function getBaseDir(){
        return getv($this->m_envs, "basedir");
    }
    /**
     * get environment basedirectory
     */
    public function setBaseDir($basedir){
        $this->set("basedir", $basedir);
        return $this;
    }

    public function getEnvironmentPath(){
        return array_filter([
            "%lib%"=>IGK_LIB_DIR,
            "%app%"=>igk_io_applicationdir(),
            "%project%"=>igk_io_projectdir(),
            "%basedir%"=>igk_io_basedir(),
            "%packages%"=>igk_io_packagesdir(),
            "%modules%"=>igk_get_module_dir(),
            "%viewcaches%"=>igk_is_cmd()? null : $this->getViewCacheDir()
        ]);
    }
    /**
     * return view cache directory. 
     * @return string cache directory 
     */
    public function getViewCacheDir(){
        return IGKCaches::view()->path;
    }

    /*
    * get the environment base directory
    * @return mixed 
    */
   public function getLogFile(){
       return getv($this->m_envs, "logfile");
   }
   /**
    * get environment basedirectory
    */
   public function setLogFile($logfile){
        $this->set("logfile", $logfile);
        return $this;
   }

    /**
     * create an environment class instance
     * @param mixed $classname class name declaration
     * @return mixed 
     * @throws Exception 
     */
    public function createClassInstance($classname){
        $b = $this->instances;
        if ($b===null){
            $b = [];
        }
        if (!isset($b[$classname])){
            $b[$classname] = new $classname();
        }
        return getv($b, $classname);
    }
    /**
     * return resolved enviromnent key
     * @param mixed $n 
     * @return int|string|false 
     */
    public static function ResolvEnvironment($n){        
        if (($index = array_search(strtolower($n), self::$env_keys))===false){
            return "DEV";
        }
        return $index;
    }
    

    public function setArray($name, $key, $value){
        $tab = $this->get($name);
        if (!is_array($tab)){
            if ($tab!==null) die("property name already contains a non array value");
            $tab = array();
        }
        $tab[$key] = $value;
        $this->set($name,  $tab);
        return $this;
    }
    public function getArray($name, $key, $default=null){
        $b = $this->get($name);
        if (is_array($b)){
            return igk_getv($b, $key, $default);
        }
        return $default;
    }
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        $this->_prepareServerEnvironment();
    }
    /**
     * prepare server environment
     * @return void 
     */
    private function _prepareServerEnvironment(){
        $t=[];
        foreach($_SERVER as $k=>$v){
            if (strpos($k, "IGK_")===0){
                $t[$k]= $v;
            } 
        }
        $this->m_envs=$t;
    }
    public function getToday(){
        return date("Y-m-d");
    }
    public function __debugInfo()
    {
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function & __get($n){      
        return $this->get($n);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
   public function __isset($v){
		return array_key_exists($v, $this->m_envs);
	}
    public function setNo_cache($value){
        $this->m_envs["no_cache"] = $value;
        return $this;
    }
    public function getNo_cache(){
        return igk_getv($this->m_envs, "no_cache");
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if (method_exists($this, $fc = "set".$n)){
            $this->$fc($v);            
        } else{
            $this->set($n, $v);
        }
		return $this;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __sleep(){
        igk_die("Sleep Environment: Operation Not allowed ".__CLASS__);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __wakeup(){}
    ///<summary></summary>
    ///<param name="var"></param>
    /**
    * 
    * @param mixed $var
    */
    public function & get($var, $default=null){
		$t = null;
        if (empty($var)){
            return $default;
        }
        if (method_exists($this, $fc = "get".$var)){
            $t = $this->$fc();
        } else {
            if (array_key_exists($var, $this->m_envs)){
                $t = & $this->m_envs[$var];
            }
        }
        if ($t===null)
        {
            if (($default !== null) && igk_is_callable($default)) {
                if (($m = call_user_func_array($default, array()))!== null){
                    $this->m_envs[$var] = $m;
                    return $m;
                }
            }
            $t = $default;
        }
		return $t;
    }
    ///<summary>create a environment class </summary>
    public static function GetClassInstance($classname){
        static $instance;
        if ($instance ===null)
            $instance = [];
        if (isset($instance[$classname])){
            return $instance[$classname];
        }
        $c = new $classname();
        $instance[$classname] = $c;
        return $c;

    } 
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return self environment instance
    */
    public static function getInstance(){
        !($c= self::$sm_instance) && ($c = self::$sm_instance=new self());
        return $c;
    }

    public function is_mod_enabled($module){
        return igk_apache_module($module);  
    }

    ///<summary></summary>
    /**
    * 
    */
    public function getVars(){
        return $this->m_envs;
    }
    ///<summary>check wether environment is on environment mode</summary>
    ///<remark>default environment mode is *development</summary>
    /**
    * check wether environment is on environment mode
    */
    public function is($env_mode){         
        if(array_key_exists($env_mode, self::$env_keys)){
            $env_mode = self::$env_keys[$env_mode];
        }
        return IGKServer::getInstance()->ENVIRONMENT == $env_mode;
    }
    /**
     * helper
     * @return bool 
     */
    public function isDev():bool{
        return $this->is("DEV");
    }
    ///<summary>get if environment is in debug mode</summary>
    /**
     * get if environment is in debug mode
     * @return boolean
     */
    public function isDebug(){
        return defined('IGK_DEBUG') ? constant('IGK_DEBUG') : igk_environment()->get(self::DEBUG);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function IsWebApp(){
        return $this->get("IGK_APP") == "WEBAPP";
    }
    public function context(){
        return $this->get("app_type", IGKAppType::web);
    }
    ///<summary>environment full name</summary>
    /**
    * environment full name
    */
    public function name(){
        return IGKServer::getInstance()->ENVIRONMENT;
    } 
    ///<summary>environment short name</summary>
    /**
    * environment short name
    */
    public function keyName(){
        return self::ResolvEnvironment(IGKServer::getInstance()->ENVIRONMENT);
    }
    ///<summary></summary>
    ///<param name="i"></param>
    /** 
    * @param mixed $i
    */
    protected function _access_offsetExists($i):bool{
        return isset($this->m_envs[$i]);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    ///<return refout="true"></return>
    /**
    * 
    * @param mixed $v
    * @return *
    */
    protected function _access_offsetGet($v){
        if (isset($this->m_envs[$v])){
            $n=& $this->m_envs[$v];
            if ($n)
                return $n;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="i"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $i
    * @param mixed $v
    */
    protected function _access_offsetSet($i, $v):void{
        if($v === null)
            unset($this->m_envs[$i]);
        else
            $this->m_envs[$i]=$v;

    }
    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
    * @param mixed $i
    */
    protected function _access_offsetUnset($i):void{
        unset($this->m_envs[$i]);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function __serialize(){
        die("not allowed ".__CLASS__);
    }
    ///<summary>set localy variable</summary>
    /**
    * set localy variable
    */
    public function set($k, $v){        
        if($v === null){
            unset($this->m_envs[$k]);
        }
        else
            $this->m_envs[$k]=$v;
    }
    public function checkInArray($key, $value){
        return is_array($t = $this->$key) && key_exists($value, $t);
    }
    public function unsetInArray($key, $value){
        if (is_array($t = $this->$key)){
            unset($t[$value]); 
        }
        $this->$key = $t;
    }
    public function setInArray($key, $value){
        if (!is_array($t = $this->$key)){
            $t = [];            
        }
        $t[$value] = 1;
        $this->$key = $t;
 
    }
    public function bypass_method(BaseController $ctrl, ?bool $bypass){
        $this->set(get_class($ctrl).'/bypass_method', $bypass);   
    }

    public function get_file($file, $ext=".phtml"){ 
        $n = ".".strtolower($this->name()); 
        foreach([$n, ""] as $k){
            if (file_exists($f = $file.$k.$ext)){
                return $f;
            }
        }
        return null;
    }

    /**
     * push data in environment data valiable
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     */
    public function push($key, $value){
        
        $c = $this->get($key);
        if (!$c){
            $c = [];
        } 
        if (!is_array($c)){ 
            throw new EnvironmentArrayException($key);
        }
        array_push($c, $value);
        $this->set($key, $c);
    }
    /**
     * pop environment array variable
     * @param string $key key to get
     * @return void 
     */
    public function pop($key){
        $o = null;
        if (is_array($c = $this->get($key))){
            $o = array_pop($c);
            $this->set($key, $c);
        }
        return $o;
    }
    /**
     * get the last environment storage
     * @param mixed $key 
     * @return mixed 
     * @throws IGKException 
     */
    public function last($key){
        if (is_array($c = $this->get($key))){
            return igk_getv($c, count($c)-1);
        }
    }
    /**
     * get the first environment storage
     * @param mixed $key 
     * @return mixed 
     * @throws IGKException 
     */
    public function first($key){
        if (is_array($c = $this->get($key))){
            return igk_getv($c, 0);
        }
    }
    ////<summary>get instance of create array </summary>
     /**
     * get instance of create array 
     * @param mixed $key 
     * @return mixed 
     */
    public function & createArray($key){
        $c = & $this->get($key);
        if (!$c){
            $c = [];
            $this->m_envs[$key] = & $c;
        } else {
            if (!is_array($c)){
                die(__("Present value is not registrated as an array:[{0}]."));
            }

        }
        return $c;
    }

    /**
     * check if a value is present in array. registrated in invironment
     * @param mixed $key 
     * @param mixed $value 
     * @return bool 
     */
    public function isInArray($key, $value){
        $c = $this->createArray($key);
        return in_array($value, $c);
    }
    /**
     * call before init app to load extra configuration file
     * @param array $config 
     * @return void 
     */
    public function setConfigFiles(array $config){
        $this->setArray("extra_config", "configFiles", $config);
    }

    /**
     * get if allowed to resolv SQL data type 
     * @return bool 
     */
    public function getResolvSQLType(){
        return !defined("IGK_TEST_INIT");
    }
}