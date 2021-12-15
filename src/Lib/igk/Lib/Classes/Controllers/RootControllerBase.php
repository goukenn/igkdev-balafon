<?php

namespace IGK\Controllers;

use Closure;
use IGKApp;
use IGKObject;
use ReflectionFunction;

///<summary>represent a root controller entry</summary>
/**
 * represent a root controller entry
 * @method macroKeys from default extension 
 * @method initDb from default extension 
 * @method getDb from default extension 
 * @method resetDb from default extension 
 * @method getMacro from default extension 
 */
abstract class RootControllerBase extends IGKObject{
	static $macros;

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
        return  !empty($m = strstr($this->getDeclaredDir(), IGK_LIB_DIR));
    }
	public static function IsSystemController( RootControllerBase $controller){
		return $controller->getIsSystemController();
	}

    public static function __callStatic($name, $arguments)
	{
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
				"getMacro"=>function($name) {
					return  igk_getv(self::$macros, $name);
				}
			];
		} 
		
		$c = igk_getctrl(static::class); 
		
		if (isset(self::$macros[$name])){
			$fc = Closure::fromCallable(self::$macros[$name]);
			$fc = $fc->bindTo(null, static::class);
			$ref = (new ReflectionFunction($fc));		
			if (($ref->getNumberOfParameters()>0) && ($t = $ref->getParameters()[0]->getType()) ){
				if (($t == self::class) || is_subclass_of($t->getName(), self::class)){
					array_unshift($arguments, $c);
				}
			}
			return $fc(...$arguments);
		} 
		
		//if ($name == "getComponentsDir"){
			// method is probably protected
		if (!igk_environment()->{static::class.'/bypass_method'} && method_exists($c, $name)){
			//invoke in controller context
			return $c::Invoke($c, $name, $arguments);
		}
		// if (igk_is_debug()){
		// 	igk_trace();
		// 	igk_wln_e("try to call envo ", static::class, parent::class, get_called_class());
		// }
		// 	igk_wln("cmethod ", method_exists($c, $name));
		// 	igk_wln_e("ok");
		// }
		array_unshift($arguments, $c); 

		return ControllerExtension::$name(...$arguments); 
	}
	public function __call($name, $argument){
        return static::__callStatic($name, $argument);
    }
	abstract function View();

	/** @return mixed  */
	public function getApp(){ return IGKApp::getInstance(); }



	 ///<summary>getfull uri</summary>
    /**
    * getfull uri
    */
    public function getAppUri($function=null){
        $app=igk_app();
        if($app->SubDomainCtrl === $this){
            $g=$app->SubDomainCtrlInfo->clView;
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
            $d=igk_createnode("div");
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
        return $this->getArticleBindingContent($name, igk_db_select_all(igk_getctrl($targetCtrlName)));
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
        return igk_io_get_article($name, $dir);
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
}