<?php

namespace IGK\Controllers;

use Closure;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGKApp;
use IGKObject;
use IGKType;
use ReflectionFunction;

///<summary>represent a root controller entry</summary>
/**
 * represent a root controller entry
 * @method static macroKeys() macros: get registrated macros key
 * @method static initDb() macros: init Controller database
 * @method static mixed getDb() macros: get data adapter
 * @method static bool resetDb(bool $navigate=true , bool $force = true) from default extension reset controller attached database
 * @method static object getMacro() from default extension 
 * @method static mixed invokeMacro($name, $args) . from context force macros invocation if method is present.
 * @method static null|callable getMacro($name, $args) . from context force macros invocation if method is present.
 */
abstract class RootControllerBase extends IGKObject{
	static $macros;

    public function __construct(){        
    }
    public function __debugInfo()
    {
        return [];
    }
    /**
     * controller auto load class entry
     * @param mixed $n 
     * @return int 
     */
    protected function auto_load_class($n){        
        $entryNS=$this->getEntryNameSpace() ?? "";
        $classdir = $this->getClassesDir();
        if (defined('IGK_TEST_INIT')){
            $classdir = [
                $classdir, $this->getTestClassesDir()
            ];
        }         
        return igk_auto_load_class($n, $entryNS, $classdir);
    }
   
	///<summary></summary>
    /**
    * 
    */
    protected final function getIsSystemController(){       
        return  !empty(strstr($this->getDeclaredDir(), IGK_LIB_DIR));
    }
	public static function IsSystemController( RootControllerBase $controller){
		return $controller->getIsSystemController();
	}
    /**
     * return registrated macro function 
     */
    public final static function getMacro($name){
        return igk_getv(self::$macros, $name);
    }

    public static function __callStatic($name, $arguments)
	{
        $c = null; 
        $v_macro  = 0; 
		if (self::$macros===null){
			self::$macros = [
				"macrosKeys"=>function(){
					return array_keys(self::$macros);
				},
				"initDb"=>function(RootControllerBase $controller, $force=false){
					return include(IGK_LIB_DIR."/Inc/igk_db_ctrl_initdb.pinc"); 
				},
				"resetDb"=>function(RootControllerBase $controller, $navigate=true, $force=false){              
				 	return include(IGK_LIB_DIR."/Inc/igk_db_ctrl_resetdb.pinc");
				},
				"getDb"=>function(){
					return null;
				},				 
			];
		}  
        if ($name == "invokeMacros"){
            $c = $arguments[1];
            $name = $arguments[0];
            $v_macro = 1;
            $arguments = array_slice($arguments, 2) ?? [];
        }
		$c = $c ? $c : igk_getctrl(static::class); 
		
		if (isset(self::$macros[$name])){
			$fc = Closure::fromCallable(self::$macros[$name]);
			$fc = $fc->bindTo(null, static::class);
			$ref = (new ReflectionFunction($fc));		
			if (($ref->getNumberOfParameters()>0) && ($t = $ref->getParameters()[0]->getType()) ){
                $t = IGKType::GetName($t);
				if (($t == self::class) || is_subclass_of($t, self::class)){
					array_unshift($arguments, $c);
				}
			}
			return $fc(...$arguments);
		} 
		
		//if ($name == "getComponentsDir"){
			// method is probably protected
		if (!$v_macro && !igk_environment()->{static::class.'/bypass_method'} && method_exists($c, $name)){
			//invoke in controller context 
			return $c::Invoke($c, $name, $arguments);
		}	
        // + | invoke controller extension method
		array_unshift($arguments, $c); 
        if (method_exists(ControllerExtension::class, $name)){
		    return ControllerExtension::$name(...$arguments); 
        } else {
            if (igk_environment()->is("DEV")){
                igk_die("method [$name] not found");
            }
            throw new \IGK\System\Exceptions\ActionNotFoundException($name);
        }
	}
	public function __call($name, $argument){
        //by pass method properted call
        if (method_exists($this, $name) && (in_array(strtolower($name), ["initcomplete"]))){
            return call_user_func([$this, $name], $argument);
        }
        return static::__callStatic($name, $argument);
    }

    public function __get($name){
        if(method_exists($this, $fc = "get".ucfirst($name))){ 
      
            return call_user_func(array($this, $fc), array_slice(func_get_args(), 1));
        }
        return $this->getEnvParam($name);
    }
    public function __set($name, $value){
        if (!$this->_setIn($name, $value)){   
           $this->setEnvParam($name, $value);
        }
        return $this;
    }

	abstract function View();


    ///<summary>get application manager instance</summary>
	/**
     * get application manager instance
     *  @return IGKApp  
     * */
	public function getApp(){ return IGKApp::getInstance(); }

    /**
     * return system document
     * @return mixed 
     */
    public function getDoc(){
        return $this->getApp()->getDoc();
    }

	 ///<summary>getfull uri</summary>
    /**
    * getfull uri
    */
    public function getAppUri($function=null){ 
        if(SysUtils::GetSubDomainCtrl() === $this){
            $g= igk_app()->SubDomainCtrlInfo->clView;
            if(!empty($function) && (stripos($g, $function) === 0)){
                $function=substr($function, strlen($g));
            }
        }
        if($function)
            return rtrim(igk_io_baseuri(), '/')."/".$function;
        return igk_io_baseuri();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    /**
    * 
    * @param mixed $name
    */
    public function getArticle($name){
        return $this->getArticleInDir($name, $this->getArticlesDir());
    }
    ///<summary>get the article binding content</summary>
    /**
    * get the article binding content
    */
    public function getArticleBindingContent($name, $entries, $prebuild=true){
        if(is_object($entries) && ($entries->RowCount > 0)){
            $d=igk_create_node("div");
            igk_html_binddata($this, $d, $name, $entries);
            return $d->render();
        }
        return IGK_STR_EMPTY;
    }
    ///<summary>get the article binding content with name. of the target controller</summary>
    /**
    * get the article binding content with name. of the target controller
    */
    public function getArticleBindingContentW($name, $targetCtrlName){
        die(__METHOD__.": Not implement");
        //return $this->getArticleBindingContent($name, igk_db_select_all(igk_getctrl($targetCtrlName)));
    }
    ///<summary>get article content</summary>
    ///<param name="name" > name of the article</param>
    ///<param name="evalExpression">demand for eval expression .default is true</param>
    ///<param name="row">row used info to eval expression</param>
    /**
    * get article content
    * @param mixed $name  name of the article
    * @param mixed $evalExpression demand for eval expression .default is true
    * @param mixed $row row used info to eval expression
    */
    public function getArticleContent($name, $evalExpression=true, $row=null){
        if(file_exists($f=$name) || file_exists($f=$this->getArticle($name))){
            $out=IGK_STR_EMPTY;
            $out=igk_io_read_allfile($f);
            if($evalExpression){
                $out = igk_html_treat_content($out, $this, $row)->render();
                
            }
            return $out;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="fullname"></param>
    /**
    * 
    * @param mixed $fullname
    */
    public function getArticleFull($fullname){
        return igk_io_dir($this->getArticlesDir()."/".$fullname);
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="dir"></param>
    /**
    * 
    * @param mixed $name
    * @param mixed $dir
    */
    public function getArticleInDir($name, $dir){        
        return IO::GetArticleInDir($dir, $name); 
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getArticlesDir(){  
        return igk_io_dir($this->getDeclaredDir()."/".IGK_ARTICLES_FOLDER);
    }
        ///<summary></summary>
    /**
    * 
    */
    public function getScriptsDir(){
        return $this->getDeclaredDir()."/".IGK_SCRIPT_FOLDER;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getBaseUri(){
        return $this->getEnvParam("fulluri") ?? $this->getAppUri($this->currentView);
    }

    protected function initComplete(){        
    }

   
    /***
     * create controller an 
     */
    public static function CreateInstanceAndInit($n, callable $init){
        if (!class_exists($n, false) || !is_subclass_of($n, self::class)){
            return null;
        }
        $o = new $n();
        if ($init($o, $n)){
            $o->InitComplete();
        }
        return $o;
    }
}