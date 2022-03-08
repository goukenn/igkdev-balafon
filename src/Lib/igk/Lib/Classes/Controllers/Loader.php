<?php

namespace IGK\Controllers;

///<summary>represent internal core loader</summary>

use Closure;
use IGK\System\Exceptions\LoadArticleException;
use IGK\System\Http\IResponse;
use IGK\System\Http\WebResponse;
use IGK\System\IO\FileSystem;
use IGKCaches;
use IGKException;

/**
* represent internal core loader
*/
class Loader implements IResponse {
    private $_controller;
    private $_output;
	private $_listener;
    private $_cache_fs;
    private $loader_load_files = [];

    public function loaded_files(){
        return $this->loader_load_files;
    }
	public function output() {         
        $m = $this->_controller->_output.$this->_output;
        $this->_controller->_output = "";
        $this->_output = "";
        return (new WebResponse($m))->output();
        
    }
    //+ store callback to call protected function info provide by the controller
    ///<summary>dispatch call to controller</summary>
    /**
    * dispatch call to controller
    * @return mixed|void
    */
    public function __call($n, $args){
        if(method_exists($this->_controller, $n)){
            return call_user_func_array(array($this->_controller, $n), $args);
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
    * 
    * @param mixed $ctrl
    */
    public function __construct($ctrl, $listener){
        $this->_controller=$ctrl;
        $this->_output="";
		$this->_listener = $listener;
        $this->_cache_fs = FileSystem::Create(igk_environment()->getViewCacheDir());
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function __get($n){
        if(method_exists($this, $m="get".$n)){
            return call_user_func_array(array($this, $m), array());
        }
        else{
            return $this->_controller->$n;
        }
    }
    ///<summary></summary>
    ///<param name="file"></param>
    ///<param name="data"></param>
    /**
    * 
    * @param mixed $file
    * @param mixed $data
    */
    private function _inc_file($file, $data){
        $fc = Closure::fromCallable(function(){
            /**
             * $ctrl passing to include
             */
            extract(array_merge(func_get_arg(1), ["ctrl"=>$this])); 
            include(func_get_arg(0));
        })->bindTo($this->_controller);
 
        $fc($file, $data); 
    }
    ///<summary></summary>
    ///<param name="file"></param>
    ///<param name="args" default="null"></param>
    ///<param name="render" default="1"></param>
    /**
    * 
    * @param mixed $file
    * @param mixed $raw data to pass
    * @param mixed $render the default value is 1
    */
    public function article($file, $raw=null, $render=1){

        if (empty($f= realpath($file)))
            $f = $this->_controller->getArticle($file);
        if(!file_exists($f)){
            return false;
        }       
        $n = IGKCaches::Compile2($this->_controller, IGKCaches::article_filesystem(), $f, $raw, $render);      
        return $n;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function clear(){
        $this->_controller->_output="";
    }
    ///<summary>check an resolve view file</summary>
    /**
    * check an resolve view file
    */
    public function file_exists($view){
        $f=stream_resolve_include_path($view);
        if(!empty($f))
            return $f;
        if(file_exists($view))
            return realpath($view);
        if(!empty($c=$this->_controller->getViewFile($view))){
            return $c;
        }
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigs(){
        return $this->_controller->getConfigs();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getLoader(){
        return $this;
    }
    ///<summary>retreive controller output buffer</summary>
    /**
    * retreive controller output buffer
    */
    public function getOutput(){
        return $this->_controller->_output;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getUser(){
        return $this->_controller->User;
    }
    ///<summary> use to load model utility class</summary>
    /**
    *  use to load model class
    */
    public function & model($name, $refname=null, $forceloading=false){
        $n=$refname ? $refname: $name;
        $igk_c=$this->_controller;
        $cl=$name;
		$cl_c = get_class($igk_c);
        ($m = igk_get_env($key="sys://instance/model/".$cl_c)) || ($m = array());
        if(isset($m[$n])){
            return $m[$n];
        }
		if ( !((!$forceloading && (strpos($name, "\\")!==false) && class_exists($cl, true)) )|| !class_exists($cl, $forceloading))
		{
			$meth = "GetModelClassName";
			if(method_exists($cl_c, $meth)){
				$cl=call_user_func_array( array($cl_c, $meth), array($name));
			}else {
				$ns = "";
				if ($g_fc= $this->_listener){
					$d = $g_fc();
					$ns = $d->entryNS;
				} else {
					// try to get entry ns if method is public is public
					$ns = $igk_c->getEntryNamespace();
				}
				$cl = $ns."\\ModelUtilities\\".ucfirst($name)."ModelUtility";
			}
		}
        if(!class_exists($cl)){
            throw new IGKException("ModelUtility [$cl] not found.");
        }
        $m[$n]=new $cl($igk_c);
        igk_set_env($key, $m);
        return $m[$n];
    }
    ///<summary> include view file</summary>
    /**
    *  include view file
    */
    public function view($file, $data=array(), $render=0){
        if(file_exists($f=$this->_controller->getViewFile($file))){
            $file=$f;
        }
        else{
            if(!file_exists($file)){
                $file=dirname(__FILE__)."/Views/".$file.".".IGK_DEFAULT_VIEW_EXT;
            }
        }
        if(!file_exists($file))
             return $this;
        $this->loader_load_files[$file] = $file;
        $bck = set_include_path(dirname($file).PATH_SEPARATOR. get_include_path());
        //+ unset the file to load        
        unset($data["file"]);
        $data=array_merge($this->_controller->getSystemVars(), array(
            "context"=>"loader_view",
            "file"=>$file,
            "dir"=>dirname($file),
            "fname"=>igk_io_getviewname($file, $this->_controller->getViewDir())
        ), $data);
        ob_start();
        igk_environment()->viewfile = 1;
        $o = $this->_inc_file($file, $data);
        igk_environment()->viewfile = null;
        $o .= ob_get_contents();
        ob_end_clean();
        set_include_path($bck);        
        if($render)
            echo $o;
        else{
            $this->_controller->_output .= $o;
        }
		return $this;
    
    }

    /**
     * bind article
     * @param mixed $file 
     * @param array $data 
     * @param int $render 
     * @return $this 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function bind($file, $data=array(), $render=0){
        $n = igk_create_node("NoTagNode");
        $n->addArticle($this->_controller, $file, $data);
        $o = $n->render();
        if($render)
            echo $o;
        else{
            $this->_controller->_output .= $o;
        }
        return $this;
    }
    public function loadComponent($file, $t, $args=null){ 
        $fc = Closure::fromCallable(function($file, $t, $args=null){
            if ($args)
            extract($args);  
            ob_start();
            include($file); 
            $this->_output .= ob_get_clean();
        })->bindTo($this->_controller); 
        return $fc($file, $t, $args);
    }
    public function include($file, $viewargs=null){ 
        if (file_exists($file)){
            $fc = Closure::fromCallable(function($file, $args){
                if ($args)
                    extract($args);  
                ob_start();
                include($file); 
                $this->_output .= ob_get_clean();
            })->bindTo($this->_controller);  
            $fc($file, $viewargs);
        }
    }
}

