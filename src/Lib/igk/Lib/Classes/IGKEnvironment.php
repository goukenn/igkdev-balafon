<?php

  
use IGK\Controllers\BaseController;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\Helper\IO ;
use function igk_getv as getv;

///<summary>use to manage Server Environment</summary>
/**
* use to manage Server Environment
*/
final class IGKEnvironment implements \ArrayAccess{
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

    // | define environment reserver key constant
    const INIT_APP = 'INIT_APP';
    const DEBUG = 'DEBUG';
    const CTRL_CONTEXT_SOURCE_VIEW_ARGS=self::CURRENT_CTRL + 2;
    const CTRL_CONTEXT_VIEW_ARGS=self::CURRENT_CTRL + 1;
    const CURRENT_CTRL=0xE0;
    const VIEW_CURRENT_ACTION=self::CURRENT_CTRL+3;
    const VIEW_HANDLE_ACTIONS=self::CURRENT_CTRL+4;
    const VIEW_INC_VIEW= self::CURRENT_CTRL+5;
    const VIEW_CURRENT_VIEW_NAME= self::CURRENT_CTRL+6;
    const IGNORE_LIB_DIR = "sys://lib/ignoredir";
    const AUTO_LOAD_CLASS = "auto_load_class";

    public function getEnvironments(){
        return $this->m_envs;
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
        $this->OffsetSet("basedir", $basedir);
        return $this;
    }

    public function getEnvironmentPath(){
        return [
            "%app%"=>igk_io_applicationdir(),
            "%project%"=>igk_io_projectdir(),
            "%lib%"=>IGK_LIB_DIR,
            "%basedir%"=>igk_io_basedir(),
            "%packages%"=>igk_io_packagesdir(),
            "%modules%"=>igk_get_module_dir(),
            "%viewcaches%"=>$this->getViewCacheDir()
        ];
    }
    /**
     * return view cache directory. 
     * @return string cache directory 
     */
    public function getViewCacheDir(){
        $dir = igk_io_cachedir()."/views";
        if (defined("IGK_VIEW_CACHE_DIR")){
            $dir = constant("IGK_VIEW_CACHE_DIR");
        }
        !IO::CreateDir($dir) && die("view cache not created");
        return $dir;
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
        $this->OffsetSet("basedir", $logfile);
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
        $this->$name = $tab;
    }
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){
        $t=[];
        foreach($_SERVER as $k=>$v){
            if(preg_match("/^IGK_/i", $k)){
                $t[$k]=$v;
            }
        }
        $this->m_envs=$t;
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
            $this->OffsetSet($n, $v);
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
        if (method_exists($this, $fc = "get".$var)){
            $t = $this->$fc();
        } else {
            if (array_key_exists($var, $this->m_envs)){
                $t = & $this->m_envs[$var];
            }
        }
        if ($t===null)
            $t = $default;
    
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
    * @return *
    */
    public static function & getInstance(){
        !($c= self::$sm_instance) && ($c = self::$sm_instance=new IGKEnvironment());
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
    ///<summary></summary>
    /**
    * 
    */
    public function name(){
        return igk_server()->ENVIRONMENT;
    }

    use \IGK\System\Polyfill\ArrayAccessSelfTrait; 

    ///<summary></summary>
    ///<param name="i"></param>
    /**
    * 
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
        $r = "";
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
        // igk_ilog("value : ". json_encode(compact("key", "value")));
        // return;

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
        if (is_array($c = $this->get($key))){
            array_pop($c);
            $this->set($key, $c);
        }
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
     /**
     * create array to the key 
     * @param mixed $key 
     * @return mixed 
     */
    public function & createArray($key){
        $c = $this->get($key);
        if (!$c){
            $c = [];
            $this->m_envs[$key] = & $c;
        } 
        return $c;
    }
}