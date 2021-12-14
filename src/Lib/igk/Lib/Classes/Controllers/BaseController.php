<?php

namespace IGK\Controllers;

use Exception;
use IGK\Helper\StringUtility as IGKString;
use IGK\Resources\R;
use IGK\System\Configuration\ControllerConfigurationData;
use IGK\System\Exceptions\ResourceNotFoundException;
use IGK\System\Http\Request;
use IGK\System\IO\Path;
use IGKEnvironment;
use IGKFv;
use IGKServer;
use ReflectionClass;
use function igk_resources_gets as __;


abstract class BaseController extends RootControllerBase {

    const CHILDS_FLAG=5;
    const CURRENT_VIEW= IGK_CURRENT_CTRL_VIEW;
    const ENV_PARAM_USER_SETTINGS=0x200;
    const IGK_ENV_PARAM_LANGCHANGE_KEY="langchanged";
    const MAIN_VIEW=9;
    const PAGE_VIEW_FLAG=4;
    const PARAMS_FLAG=7;
    const REG_VIEW_CHILD=11;
    const SHOW_CHILD=10;
    const VIEWCHILDS_FLAG=6;
    const VISIBILITY_FLAG=2;
    const WEBPARENT_FLAG=1;

    private static $sm_sysController;
  

    public function __construct(){        
    }
    protected function getActionHandler($name, $params=null){
        if (($name!= IGK_DEFAULT_VIEW) && preg_match("/".IGK_DEFAULT_VIEW."$/",$name)){
            $name = rtrim(substr($name,0, -strlen(IGK_DEFAULT_VIEW)), "/");
        }
        $ns = $this->getEntryNameSpace();
        $c = [];
        $t = [];
        if (!empty($ns)){
            $c[] = $ns;
        }
        $c[] = "Actions\\".ucfirst($name)."Action";
        $t[] = implode("\\", $c);
         
        if ($name != IGK_DEFAULT_VIEW){
            $t[] = implode("\\",array_filter(array_merge([$ns], ["Actions\\".ucfirst(IGK_DEFAULT_VIEW)."Action"])));
        }  
        while($cl = array_shift($t)){
            if (class_exists($cl)){
                return $cl;
            }
        } 
        return null;
    }

    ///<summary> registered entry namespace . for auto load class </summary>
    /**
    *  registered entry namespace . for auto load class
    */
    protected function getEntryNameSpace(){
        $ns = dirname(igk_io_dir(get_class($this)));
        if ($ns != "."){
            return str_replace("/", "\\", $ns);
        }
        return null;
    } 
    ///<summary></summary>
    /**
    * 
    */
    protected function _loadCtrlConfig(){
        $t=igk_sys_getdefaultctrlconf();
        $meth = "GetAdditionalConfigInfo";
        if(method_exists(get_class($this), $meth )){
            $s=get_class($this);
            $c=call_user_func(array($s, $meth));
            if(is_array($c)){
                foreach($c as $k=>$v){
                    if(is_object($v)){
                        $t[$k]=null;
                    }
                    else if(is_string($v) && isset($t[$v])){
                        $t[$v]=null;
                    }
                }
            }
        }
        return (object)$t;
    }
///<summary></summary>
    /**
    * 
    */
    protected function _renderViewFile(){ 
       
        $ctrl = $this;
        $params = null;
       
        extract($ctrl->getViewArgs());

        $f="";
        $v=$this->getCurrentView() ?? igk_die("current view is null. ". get_class($this));
        $c=strtolower(igk_getr("c", null));
        if($c == strtolower($this->getName())){
            $v=igk_getr("v", $v);
        } 
        $meth_exits = method_exists($this, $meth=$v);
        

        if(($meth_exits && $this->IsFuncUriAvailable($meth)) || (isset($params) && method_exists($this, $meth=IGK_DEFAULT_VIEW))){
            try {
                $params=isset($params) ? $params: [];
                $out = call_user_func_array(array($this, $meth), $params);
            }
            catch(Exception $ex){
                igk_html_output(500);
                igk_wln_if(igk_environment()->is("development"), "error : ", $ex->getMessage());
                igk_exit();
            }
            return;
        }
     
        if(!$meth_exits && !file_exists($f=igk_io_dir($this->getViewFile($v)))){
            //
           
            $find = $this->_resolview($f, $params);
           
            if(!$find){
                if(igk_is_conf_connected() && IGKServer::IsLocal()){
                    if(!igk_io_save_file_as_utf8($f, igk_get_defaultview_content($this), true)){
                        igk_ilog("can't create the file ".$f. " AT ".__LINE__);
                        igk_exit();
                    }
                }
                else{ 
                    $message=__("res.notfound_1", igk_io_collapse_path($f));  
                    if(!igk_get_contents($this, 404, [$message, 404])){
                        if(!igk_sys_env_production()){
                            $m="[IGK] - can't get resource ".$f. " AT ".__LINE__. " ruri:".igk_io_request_uri();
                            $m .= igk_show_trace();
                            igk_wln_e("uri:".$v, $m);
                        }
                        throw new ResourceNotFoundException($message, $f, 404);
                    }
                }
            }
            else{
                $f=$find;
            }
        }
		$vdir = $this->getViewDir();
        $tdir = igk_io_dir(implode("/", [$vdir, $v])); 
        if((empty($f) && file_exists($f=igk_io_dir($this->getViewFile($v)))) || file_exists($f)){
            try {
			//+ bind view
                if ( empty(strstr($f, $tdir)) && ((dirname($f) == $vdir) || !is_dir($tdir)))
                {
                    if($v != IGK_DEFAULT_VIEW){  
                        if ( $params && ((count($params)>=1) && isset($params[0]) && ($params[0]!==$v))){
                            array_unshift($params, $v);
                            $this->regSystemVars(null, null);
                            $this->setEnvParam(IGK_VIEW_ARGS, $params);
                        }
                    } 
                }  
                $this->_include_file_on_context($f);
            }
            catch(Exception $ex){
                igk_html_output(404);
                igk_dev_wln("error : ".$ex->getMessage());
                igk_exit();
            }
        } 
    }
     ///<summary>copy this fonction to allow file inclusion on the current context controller</summayr>
    /**
    * copy this fonction to allow file inclusion on the current context controller
    */
    protected function _include_file_on_context($file){
        $this->_include_constants();
        igk_reset_globalvars();
        $fname=igk_io_getviewname($file, $this->getViewDir());
        $rname=igk_io_view_root_entry_uri($this, $fname);
        $context = __FUNCTION__;
        extract($this->utilityViewArgs($fname, $file));
        extract($this->getSystemVars());  
        $this->setEnvParam("fulluri", $furi);
        $params=isset($params) ? $params: array();
  

        $query_options=$this->getEnvParam(IGK_VIEW_OPTIONS);
        $is_direntry=(count($params) == 0) && igk_str_endwith(explode('?', igk_io_request_uri())[0], '/');
        $this->bindNodeClass($t, $fname, strtolower((isset($css_def) ? " ".$css_def: null)));
       
        $doc->body["class"]="-custom-thumbnail";
        $doc->title = "IGKDEV";
     

        try {
            $viewargs=get_defined_vars();
            igk_set_env(IGKEnvironment::CURRENT_CTRL, $this);
            igk_set_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS, $viewargs); 
            extract($this->_get_extra_args($file));
            $targs = get_defined_vars();

            //+ | ----------------------------------------------------------------
            //+ | insert here a middleware to auto handle the view before include 
            //+ | ----------------------------------------------------------------
            // if ((igk_count($params)>0) && !key_exists(0, $params)){
            //     igk_ilog("somthing bad");
            //     igk_ilog($params);
            // }
            if ((igk_count($params)>0) && key_exists(0, $params) && ($handler = $this->getActionHandler($fname, $params[0]))){                                
                $handler::Handle($this, $fname, $params);       
            } 
            ob_start();
            $bckdir = set_include_path(dirname($file).PATH_SEPARATOR.get_include_path());
            igk_environment()->viewfile = 1;
            $response = igk_include_view_file($this, $file, $targs); 
       
            igk_environment()->viewfile = 0;
            set_include_path($bckdir);
            $out=ob_get_contents();
            ob_end_clean();


         
            // echo "try bind-------1\n\n";
            // $t->clearChilds();
            // $t->div(); // (new HtmlNode("quote")); // ->div()->Content = "Marto";

            // $s = $t->render();
            // echo "\n\nfinish: ".$s;
            // exit;

            if(!empty($out)){
                $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->Content=$out;
            }
            if ($response && (is_object($response) || is_array($response))){
                // + | Bind response
                igk_do_response($response);
                igk_exit();
            }
        }
        catch(\Exception $ex){
            if (!($code = $ex->getCode())){
                $code = 500;
            }
            igk_set_header($code);
            igk_show_exception($ex);
            igk_exit();
        }
    }  
    
    ///<summary>include constant</summary>
    /**
    * include constant
    */
    protected function _include_constants(){
        if(($f=$this->getConstantFile()) && file_exists($f))
            include_once($f);
        if(($f=$this->getDbConstantFile()) && file_exists($f))
            include_once($f);
        unset($f);
    }

     ///<summary></summary>
    ///<param name="file"></param>
    /**
    * 
    * @param mixed $file
    */
    protected function _get_extra_args($file){
        $data=[];
        if(igk_is_included_view($file)){
            $tab=igk_get_env(IGKEnvironment::CTRL_CONTEXT_SOURCE_VIEW_ARGS);
            $data["source_args"]=$tab[spl_object_hash($this)];
        }
        return $data;
    }
    ///<summary></summary>
    /**
    * @return string default name attached to this controller 
    */
    public function getName(){
        return strtolower(get_class($this));
    }
     ///<summary>get store parameter referece value</summary>
    ///<param name="register">get store parameter referece value</param>
    /**
    * get store parameter 
    * @return mixed objet reference value
    */
    public function & getParam($key, $default=null, $register=false){
        $m = $this->getM_();
        $c=& $m->getFlag($key, $default, $register);
        return $c;         
    } 
    ///<summary>get stored params keys</summary>
    /**
     * get stored params keys
     * @return array stored params keys
     */
    public function getParamKeys(){
        return array_keys($this->getFlagParams());
    }
    ///<summary>get all controller's parameters</summary>
    /**
    * get all controller's parameters
    */
    public function getParams(){
        return $this->getFlagParams();
    }
    ///override this method to show the controller view.
    /**
    */
    public function getDeclaredFileName(){
        $tab = & igk_environment()->createArray("reflect_info");
        $cl = get_class($this); 
        if ($c = igk_getv($tab, $cl)){
            return $c->filename;
        }
        $h=igk_sys_reflect_class($cl);
        $c = (object)[
            "filename" => $h->getFileName()
        ];
        $tab[$cl] = $c;
        return $c->filename;
    }
    ///<summary></summary>
    /**
    * @return string
    */
    public function getDeclaredDir(){
        return dirname($this->getDeclaredFileName());
    }
    public function getClassesDir(){ 
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER, IGK_CLASSES_FOLDER]);
    }
    public function getLibDir(){
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER]);
    }
    ///<summary>get view dir</summary>
    /**
    * get view dir
    */
    public function getViewDir(){
        return  $this->getDeclaredDir()."/".IGK_VIEW_FOLDER;
    }
    ///<summary>get style directory folder</summary>
    /**
    * 
    */
    public function getStylesDir(){
        return $this->getDeclaredDir()."/".IGK_STYLE_FOLDER;
    }
    ///<summary>get primary style file</summary>
    /**
    * get style dir
    */
    public function getPrimaryCssFile(){  

        return igk_io_dir($this->getStylesDir()."/". igk_getv($this->getConfigs(), "PrimaryStyle", "default.pcss"));
    }
    ///<summary></summary>
    /**
    * 
    */
    protected function getConfigFile(){
        return $this->getDataDir()."/".IGK_CTRL_CONF_FILE;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDataDir(){
        return $this->getDeclaredDir()."/".IGK_DATA_FOLDER;
    }
    ///<summary>get the constant file </summary>
    /**
    * get the constant file
    */
    public function getConstantFile(){
        return $this->getDeclaredDir()."/.constants.php.inc";
    }
     ///<summary></summary>
    /**
    * Db constant utility
    */
    public function getDbConstantFile(){
        return $this->getDeclaredDir()."/.db.constants.php";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getResourcesDir(){
        return $this->getDataDir()."/".IGK_RES_FOLDER;
    }
    ///<summary>get controlleur current configuration</summary>
    /**
    * get controlleur current configuration
    */
    public function getConfigs(){
        $cf = $this->getConfigFile();
        if (!($tab = igk_environment()->get("ConfigsEntries"))){
            $tab = array();
        }
        if ($m = igk_getv($tab, $cf)){
            return $m;
        }

        $c= igk_environment()->init($cf, function(){
            $config=new ControllerConfigurationData($this);
            $config->initConfigSetting($this->_loadCtrlConfig());
            return $config;
        });
        $tab[$cf] = $c;
        igk_environment()->set("ConfigsEntries",  $tab);
        return $c;
    }
     ///<summary></summary>
    /**
    * 
    */
    public function getLoader(){
        $l=$this->getEnvParam("loader");
        if($l == null){
            $l=new Loader($this, function (){
				return (object)["entryNS" =>$this->getEntryNamespace()];
			});
            $this->setEnvParam("loader", $l);
        }
        return $l;
    }
     ///<summary>utility view args</summary>
    /**
    * utility view args
    */
    protected function utilityViewArgs($fname, $file=null){
        $furi=$this->getAppUri($fname);
        $dir=dirname($file);
        $this->setCurrentView($fname, false);
        $cview=$this->getCurrentView();
        $entryuri=igk_io_view_entry_uri($this, $fname);
        return get_defined_vars();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getContentDir(){
        return igk_io_dir($this->getDeclaredDir().DIRECTORY_SEPARATOR.IGK_CONTENT_FOLDER);
    }
    ///<summary>Register controller view $params var</summary>
    ///<param name="args">Mixed, single value or array . if single value it will be converted into an array of single array element</param>
    ///<param name="options">query options</param>
    ///<note>passing null will reset the system vars</note>
    /**
    * Register controller view $params var
    * @param mixed $args Mixed, single value or array . if single value it will be converted into an array of single array element
    * @param mixed $options query options
    */
    public function regSystemVars($args=null, $options=null){
   
        if($args === null){
            $this->setEnvParam(IGK_VIEW_ARGS, null);
            igk_set_env(igk_ctrl_env_view_arg_key($this), null);
        }
        else{ 

            $g=$this->getEnvParam(IGK_VIEW_ARGS);
            if(is_array($args)){
                if(is_array($g)){
                    $args=array_merge($g, $args);
                }
            }
            $this->setEnvParam(IGK_VIEW_ARGS,  $args);
        }
        if(is_string($options) && !empty($options)){
            $options=igk_get_query_options($options);
        }
        $this->setEnvParam(IGK_VIEW_OPTIONS, $options);
    }
     ///<summary> handle function</summary>
    ///<note>In ajx context (session ajx context or function name end with _ajx script will stop execution after function is called</note>
    /**
    *  handle function
    */
    protected final function handle_func($c, $param, $doc, $exit=1, $redirectUri=null){
        $h=0;
        if(method_exists($this, $c)){
            if($this->IsFuncUriAvailable($c)){
                $h=1;
                if($param == null)
                    $param=array();
                else if(is_array($param) == false)
                    $param=array($param);
                $this->_include_constants();
                $this->_initView();
                $this->register_autoload();
                $this->bindNodeClass($this->targetNode, $c);
                igk_hook("action_start", $this, $c);
                call_user_func_array(array($this, $c), $param);
                igk_hook("action_complete", $this, $c);
                if(igk_is_ajx_demand() || IGKString::EndWith($c, IGK_AJX_METHOD_SUFFIX)){
                    igk_exit();
                }
            }
            else{
                $msg=$c." function not available. ";
                if($exit && !$this->HandleError(5406)){
                    igk_sys_error(IGK_ERR_FUNCNOTAVAILABLE);
                }
                if($redirectUri){
                    igk_set_header(403);
                    igk_navto($redirectUri);
                }
                else{
                    $this->setEnvParam("header_status", 403);
                    $this->setEnvParam("header_msg", $msg);
                }
            }
            if($exit)
                igk_exit();
        }
        return $h;
    }
    ///<sample>editor[/package/function/arg1/args2]</sample>
    /**
    */
    public function getViewFile($view, $checkfile=1){
        $extension=IGK_DEFAULT_VIEW_EXT;
        if($e=igk_getv(array_slice(func_get_args(), 2), 0))
            $extension=$e;
        if(empty($view))
            $view=IGK_DEFAULT_VIEW;
        $f=igk_html_uri($this->getCtrlFile(IGK_VIEW_FOLDER."/".$view));
        $f=rtrim($f, "/");
        $ext=$extension; 
        if(is_dir($f)){
				//window allow same file in folder
				if (file_exists($cf = $f."/".IGK_DEFAULT_VIEW_FILE)){
					$f = $cf;
				}else{
					$f = $f	.".".$extension;
                }
        }
        else{
            $ext=preg_match('/\.'.$ext. '$/i', $view) ? '': '.'.$ext;
            $f=$f.$ext;
            if(!empty($ext) && $checkfile){
                if(is_file($f)){
                    return $f;
                }
                else{
                    return dirname($f)."/".IGK_DEFAULT_VIEW.'.'.$extension;
                }
            }
        } 
        return $f;
    }
    ///<summary></summary>
    ///<param name="path"></param>
    /**
    * 
    * @param mixed $path
    */
    public function getCtrlFile($path){
        if(Path::getInstance()->realpath($path) == $path)
            return $path;
        return igk_io_dir(dirname($this->getDeclaredFileName()).DIRECTORY_SEPARATOR.$path);
    }
    ///<summary></summary>
    ///<param name="code"></param>
    /**
    * 
    * @param mixed $code
    */
    protected function getErrorViewFile($code){
        $viewdir = $this->getViewDir();
        $f = $viewdir."/error/".$code.".phtml";
        if(!file_exists($f)){
            return $f;
        }
        return null;
    }

     ///<summary>View Error</summary>
    ///<param name="ctrl"></param>
    ///<param name="code"></param>
    /**
    * 
    * @param mixed $ctrl
    * @param mixed $code
    */
    public static function GetErrorView($ctrl, $code){
        if(!is_object($ctrl) || !is_subclass_of(get_class($ctrl), __CLASS__)){
            igk_die("controller not valid: ".get_class($ctrl). " # ". is_subclass_of(get_class($ctrl), __CLASS__));
        }
        return $ctrl->getErrorViewFile($code);
    }
     ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return *
    */
    protected function & getM_(){
        $classname=get_class($this);
        if(($r=IGKFv::Get($classname)) === null){
            $c= & igk_app()->session->getRegisteredControllerParams($classname);
            if($c !== null){
                $r=IGKFv::Create($classname, $c);
                return $r;
            }
            $tab=array();
            $r=IGKFv::Create($classname, $tab);
            //igk_app()->session->registerControllerParams($classname, $tab);
        }
        return $r;
    }
    ///<summary>get the flag value</summary>
    /**
    * get the flag value
    */
    public function getFlag($code, $default=null){
        return $this->getM_()->getFlag($code, $default);
    }

    ///<summary></summary>
    /**
    * 
    */
    public function getCurrentView(){
        return $this->getFlag(self::CURRENT_VIEW, IGK_DEFAULT_VIEW);
    }
    ///<summary>set the current view</summary>
    ///<param name="options">extra option to pass to view</param>
    /**
    * set the current view
    * @param mixed $options extra option to pass to view
    */
    public function setCurrentView($view, $reload=true, $targetNode=null, $args=null, $options=null){
     
        $cview=$this->getCurrentView();
        if($cview != $view){
            $this->setFlag(self::CURRENT_VIEW, $view);
        }
 
        if($reload){
            $t=$this->getTargetNode();
            $bck=$targetNode && ($targetNode !== $t) ? $t: null;
            if($bck)
                $this->TargetNode=$targetNode;
            $this->regSystemVars($args, $options);
            $this->View(); 
            if($bck)
                $this->TargetNode=$bck;
        }
    }

     ///<summary></summary>
    /**
    * 
    */
    public function getTargetNode(){
        $b=$this->getEnvParam(IGK_CTRL_TG_NODE) ?? (function(){
            $g=$this->initTargetNode();
            $this->setEnvParam(IGK_CTRL_TG_NODE, $g);
            return $g;
        })();
        return $b;
    }
    ///<summary></summary>
    /**
    * 
    */
    protected function initTargetNode(){
        $tagName=igk_sys_getconfig("app_default_controller_tag_name", "div");
        $div=igk_createnode($tagName);
        $div["id"]=igk_css_str2class_name(strtolower($this->Name));
        $div["igk-type"]="controller";
        return $div;
    }
    ///<summary>override this method to show the controller view.</summary>
    /**
    * override this method to show the controller view.
    */
    public function View(){  
        $t=igk_getv($this->getSystemVars(), "t");         
        if($t){
            $this->ShowChildFlag=true;
            $this->_initView();
            $this->_renderViewFile();
        }
        else{
            igk_ilog("/!\\ TargetNode is null ".get_class($this));
        }
    }
     ///<summary>get system variables for this controller.</summary>
    /**
    * get system variables for this controller.
    */
    public function getSystemVars(){

        $ck=igk_ctrl_env_view_arg_key($this);
        $t=igk_get_env($ck);
        $c=$this->getEnvParam(IGK_VIEW_ARGS); 
        if($t !== null){
            return $t;
        }
        $t=array();
        $t["t"]=$this->getTargetNode();
        $t["ctrl"]=$this;
        if(isset($c["doc"])){
            $t["doc"]=$c["doc"];
        }
        else{
            $doc=$this->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY);
            if(!$doc){
                $doc=$this->getApp()->Doc;
            }
            $t["doc"]=$doc;
        }
        if($viewctx=$this->getParam(IGK_CTRL_VIEW_CONTEXT_PARAM_KEY)){
            $t["viewcontext"]=$viewctx;
        }
        if(igk_count($_REQUEST) > 0)
            $t=array_merge($t, array("request"=>Request::getInstance()));
        $tab=$this->getApp()->getControllerManager()->getControllers();
        if(is_array($tab)){
            $t["controllers"]=$tab;
        }
        if($this->getParam("func_get_args") != null){
            $t["func_get_args"]=$this->getParam("func_get_args");
        }
        if($c !== null){
             $t=array_merge($t, array("params"=>is_array($c) ? $c: array($c)));
        }
        igk_set_env($ck, $t);
        return $t;
    }
     ///<summary>init base style</summary>
    /**
    * 
    */
    protected function _initCssStyle(){ 
        igk_ctrl_bind_css_file($this);
    }
    ///<summary>Initialize view setting - before renderging </summary>
    /**
    * Initialize view setting - before renderging
    */
    protected function _initView(){
        R::RegLangCtrl($this);
        $this->_initCssStyle();
        $this->ShowChildFlag=true;
    }
    ///<summary>set the flag</summary>
    /**
    * set the flag
    */
    public function setFlag($code, $value){
        $this->getM_()->setFlag($code, $value);
    }
    ///<summary>reset the value of the current view</summary>
    /**
    * reset the value of the current view
    */
    protected function resetCurrentView($view=null){
        $this->setFlag(self::CURRENT_VIEW, $view);
    }
    ///<summary>check if this controller class is a system controller</summary>
    ///<param name="mixed">object|class name of a controller</summary>
    /**
    * check if this controller class is a system controller
    * @param mixed object|class name of a controller
    */
    public static function IsSysController($className){
        if(is_object($className)){
            $f=igk_html_uri($className->getDeclaredFileName());
            if(strstr($f, IGK_LIB_DIR)){
                return true;
            }
            return false;
        }
        return (igk_getv(self::$sm_sysController, $className) != null);
    }

    ///<summary></summary>
    ///<param name="view"></param>
    ///<param name="target"></param>
    ///<param name="forcecreation" default="false"></param>
    ///<param name="args" default="null"></param>
    /**
    * 
    * @param mixed $view
    * @param mixed $target
    * @param mixed $forcecreation the default value is false
    * @param mixed $args the default value is null
    */
    public function getViewContent($view, $target, $forcecreation=false, $args=null){
        $key= "ctrl/backupnode";
        $g=$this->getParam($key);
        if($g){
            $this->TargetNode=$g;
        }
        $bck=$this->TargetNode;
        $this->setParam($key, $bck);
        $v_view=$this->CurrentView;
        $this->TargetNode=$target;
        $this->getView($view, $forcecreation, $args);
        $this->TargetNode=$bck;
        $this->resetCurrentView($v_view);
        $this->setParam($key, null);
    }
     ///<summary>set the controller parameters</summary>
    /**
    * set the controller parameters
    */
    public function setParam($key, $value){ 
        $m=$this->getM_();
        $m->setFlag($key, $value); 
        return $this; 
    }

    ///<summary>call view layout without changing current view</summary>
    /**
    * call view layout without changing current view
    */
    public function getView($view=null, $forcecreation=false, $args=null, $options=null){
        extract($this->getSystemVars());
        $v=igk_io_dir($view != null ? $view: igk_getr("v", $view));
        $f=igk_realpath($v) === $v ? $v: $this->getViewFile($v);
        $this->regSystemVars(null);
        if(file_exists($f) || ($forcecreation && igk_io_save_file_as_utf8($f, IGK_STR_EMPTY))){
            $def=0;
            if(($args !== null) && !empty($args)){
                $def++;
            }
            if(($options != null) && !empty($options)){
                $def++;
            };
            if($def > 0)
                $this->regSystemVars($args, $options);
            $this->_initView();
            $this->_include_file_on_context($f);
            $this->regSystemVars(null);
        }
    }
}