<?php

namespace IGK\Controllers;

use Exception;
use IGK\Helper\StringUtility as IGKString;
use IGK\Helper\StringUtility;
use IGK\Resources\R;
use IGK\System\Configuration\ControllerConfigurationData;
use IGK\System\Exceptions\ResourceNotFoundException;
use IGK\System\Html\Dom\HtmlCtrlNode;
use IGK\System\Http\PageNotFoundException;
use IGK\System\Http\Request;
use IGK\System\IO\Path;
use IGKEnvironment;
use IGKEvents;
use IGKException;
use IGKFv;
use IGKServer;
use IIGKDataController;
use ReflectionClass;
use function igk_resources_gets as __;

/**
 * @package IGK\Controllers
 * @method static bool getCanInitDb() check if this controller entry can init database
 * @method static bool initDb() macros method. init controller database
 * @method static string name(string $path) macros method. get resolved key name
 * @method static void InitDataBaseModel() macros function
 * @method static InitDataFactory() macros function
 * @method static InitDataInitialization() macros function
 * @method static InitDataSeeder() macros function
 * @method static bool IsEntryController() macros function
 * @method static bool IsFunctionExposed() macros function
 * @method static bool IsUserAllowedTo() macros function - check if current user is allowed to 
 * @method static void asset() macros function
 * @method static string|null asset_content(path) macros function : \
 * get asset content if found in $controller->getDataDir()."/assets" by default
 * @method static string baseUri() macros function
 * @method static bindNodeClass() macros function
 * @method static string buri(string $path) macros function
 * @method static cache_dir() macros function
 * @method static checkUser() macros function
 * @method static string classdir() macros function get entry class directory
 * @method static string configDir() macros function get configuration directory
 * @method static string configFile() macros function get configuration file
 * @method static BaseController ctrl() macros function get controller instance
 * @method static void db_add_column() macros function
 * @method static void db_change_column() macros function
 * @method static \IIGKQueryResult db_query(string $query) macros function
 * @method static void db_rename_column() macros function
 * @method static void db_rm_column() macros function
 * @method static void dispatchToModelUtility() macros function
 * @method static bool dropDb($navigate=1, $force=0) macros function drop controller database model
 * @method static void furi() macros function
 * @method static string getAuthKey() macros function : get controller authentication key
 * @method static void getAutoresetParam() macros function
 * @method static string getBaseFullUri() macros function
 * @method static void getCacheInfo() macros function
 * @method static bool getCanInitDb() macros function
 * @method static bool getCanModify() macros function
 * @method static void getComponentsDir() macros function
 * @method static \IGKHtmlDoc getCurrentDoc() macros function
 * @method static object getDataAdapter() macros function data driver
 * @method static string getDataSchemaFile() macros function
 * @method static array getDataTableDefinition(string tablename) macros function
 * @method static void getEnvKey() macros function
 * @method static void getEnvParam() macros function
 * @method static void getEnvParamKey() macros function
 * @method static void getInitDbConstraintKey() macros function
 * @method static bool getIsVisible() macros function
 * @method static void getRouteUri() macros functiong
 * @method static void getTestClassesDir() macros function
 * @method static object getUser() macros function
 * @method static void array getViewArgs() macros function
 * @method static string hookName() macros function get hook name
 * @method static void initDbConstantFiles() macros function
 * @method static void initDbFromFunctions() macros function
 * @method static void initDbFromSchemas() macros function
 * @method static void libdir() macros function
 * @method static object loadDataAndNewEntriesFromSchemas() macros function
 * @method static void loadDataFromSchemas() macros function
 * @method static bool login(user, passwd, nav) macros function. try login with the user
 * @method static void logout() macros function
 * @method static void migrate() macros function
 * @method static object|null modelUtility() macros function
 * @method static void string name(?string path) macros function
 * @method static void notifyKey() macros function
 * @method static string ns(string $path) macros function
 * @method static void register_autoload() macros function register macros function
 * @method static void resolvClass() macros function
 * @method static void resolv_table_name() macros function
 * @method static void seed() macros function
 * @method static void setEnvParam(key, value) macros function
 * @method static void storeConfigSettings() macros function
 * @method static string uri() macros function 
 * @method static string loadMigrationFile() macros function 
 * @method Users checkUser(bool $check, ?string $redirectUri ) macros function check if user or navigate
 * @method static string getErrorViewFile(int code) macros function get controller error file
 * @method static mixed getConfig(string $name, default=null) macros function get config setting
 * @method static mixed js(string $name, default=null) macros function load inline js script
 * @method static mixed pcss(string $name, default=null) macros function load temp inline pcss
 */
abstract class BaseController extends RootControllerBase implements IIGKDataController
{

    const CHILDS_FLAG = 5;
    const CURRENT_VIEW = IGK_CURRENT_CTRL_VIEW;
    const ENV_PARAM_USER_SETTINGS = 0x200;
    const IGK_ENV_PARAM_LANGCHANGE_KEY = "langchanged";
    const MAIN_VIEW = 9;
    const PAGE_VIEW_FLAG = 4;
    const PARAMS_FLAG = 7;
    const REG_VIEW_CHILD = 11;
    const SHOW_CHILD = 10;
    const VIEWCHILDS_FLAG = 6;
    const VISIBILITY_FLAG = 2;
    const WEBPARENT_FLAG = 1;
    // + | activate this to disable action handling
    const NO_ACTION_FLAG = 11;

    const VIEW_ARGS = IGK_VIEW_ARGS;

    /**
     * 
     * @var mixed
     */

    private static $sm_sysController = [];

    /**
     * get action handler
     */
    protected function getActionHandler(string $name, $params = null)
    {
        if (($name != IGK_DEFAULT_VIEW) && preg_match("/" . IGK_DEFAULT_VIEW . "$/", $name)) {
            $name = rtrim(substr($name, 0, -strlen(IGK_DEFAULT_VIEW)), "/");
        }
        $ns = $this->getEntryNameSpace();
        $c = [];
        $t = [];
        if (!empty($ns)) {
            $c[] = $ns;
        }
        $m = "";
        $sep = "";
        foreach(explode("/", $name) as $r){
            $m .= $sep.StringUtility::CamelClassName(ucfirst($r));
            array_unshift($t, implode("\\", array_filter(array_merge($c, ["Actions\\".$m."Action"]))));    
            $sep="\\";
        } 

        if ($name != IGK_DEFAULT_VIEW) {
            $t[] = implode("\\", array_filter(array_merge([$ns], ["Actions\\" . ucfirst(IGK_DEFAULT_VIEW) . "Action"])));
        } 
        $classdir = $this->getClassesDir(); 
        $sublen = strlen($ns)+1;
      
        while ($cl = array_shift($t)) {
            $fcl = $cl;
            if (!empty($ns) && (strpos($cl, $ns."\\")===0)) {
                $fcl = substr($cl, $sublen);
            }         
            $f = igk_io_dir(implode("/", [$classdir, $fcl.".php"]));
            // igk_wln("try : ".$f);
            if (file_exists($f) && class_exists($cl)){
                return $cl;
            }
        }
        return null;
    }

     

    protected function getEntryNamespace(){
        if (strstr($this->getDeclaredDir(), IGK_LIB_DIR)){
            return \IGK::class;
        }
        $ns = dirname(igk_io_dir(get_class($this)));
        if ($ns != ".") {
            return str_replace("/", "\\", $ns);
        }
        return null; 
    }
    ///<summary></summary>
    /**
     * @return object 
     */
    protected function _loadCtrlConfig()
    {
         

        $t = igk_sys_getdefaultctrlconf();
        $meth = "GetAdditionalConfigInfo";
        if (method_exists(get_class($this), $meth)) {
            $s = get_class($this);
            $c = call_user_func(array($s, $meth));
            if (is_array($c)) {
                foreach ($c as $k => $v) {
                    if (is_object($v)) {
                        $t[$k] = null;
                    } else if (is_string($v) && isset($t[$v])) {
                        $t[$v] = null;
                    }
                }
            }
        }
        return (object)$t;
    }
      ///<summary>reset the current view file request</summary>
      protected function _resolview($f, ?array $params=[]){
          return false;
        //   var_dump($params);
        //   igk_wln_e(__FILE__.":".__LINE__, "file : ".$f, $params);
        // $view_dir=$this->getViewDir();
        // $dfile=dirname($f);
        // $qfile=$dfile;
        // $find=0; 
        // if ($params===null){
        //     $params = [];
        // }
        // while(!$find && ($qfile != $view_dir)){
        //     $qfile=dirname($qfile);
        //     if(file_exists($s=$qfile."/".IGK_DEFAULT_VIEW_FILE)){
        //         $find=$s;
        //         $ln=strlen($view_dir) + 1;
        //         $v= ltrim(dirname(substr($s, $ln)), '.');                
        //         $p=array_merge(explode("/", igk_html_uri(substr($dfile, $ln + strlen($v)))), $params);
        //         // var_dump($p);
        //         // igk_wln_e(
        //         //     compact("f", "ln", "v", "dfile", "view_dir", "s", "p")
        //         // );




        //         $this->setFlag(self::CURRENT_VIEW, $v);
        //         $options=$this->getEnvParam(IGK_VIEW_OPTIONS);
        //         $this->regSystemVars(null, null);
        //         $this->regSystemVars($p, $options);
        //     }
        // }
        // return $find;
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function _renderViewFile()
    {
        $ctrl = $this;
        $params = null;
        extract($ctrl->getViewArgs());
        $v_handle =false;
        $f = "";
        $v = $this->getCurrentView() ?? igk_die("current view is null. " . get_class($this));
        $c = strtolower(igk_getr("c", ""));
        if ($c == strtolower($this->getName())) {
            $v = igk_getr("v", $v);
        } 
        $meth_exits = method_exists($this, $meth = $v);
        if (($meth_exits && $this->IsFuncUriAvailable($meth)) || (isset($params) && method_exists($this, $meth = IGK_DEFAULT_VIEW))) {
            try {
                $v_handle = "method";
                $params = isset($params) ? $params : [];
                return call_user_func_array(array($this, $meth), $params);
            } catch (Exception $ex) {
                igk_set_header(500);
                igk_wln_if(igk_environment()->is("development"), "error : ", $ex->getMessage());
                igk_exit();
            }
            return;
        }
        if ($params===null){
            $params = [];
        } 
 
        if (!$meth_exits && !file_exists($f = igk_io_dir($this->getViewFile($v, 1, $params)))) {
            //
            $v_handle = "file";  
            if (!$find) {
                if (igk_is_conf_connected() && IGKServer::IsLocal()) {
                    if (!igk_io_save_file_as_utf8($f, igk_get_defaultview_content($this), true)) {
                        igk_ilog("can't create the file " . $f . " AT " . __LINE__);
                        igk_exit();
                    }
                } else {
                    $message = __("res.notfound_1", igk_io_collapse_path($f));
                    if (!igk_get_contents($this, 404, [$message, 404])) {
                        if (!igk_sys_env_production()) {
                            $m = "[IGK] - can't get resource " . $f . " AT " . __LINE__ . " ruri:" . igk_io_request_uri();
                            $m .= igk_show_trace();
                            igk_wln_e("uri:" . $v, $m);
                        }
                        throw new ResourceNotFoundException($message, $f, 404);
                    }
                }
            } else {
                $f = $find;
            }
        }    
        if (file_exists($f)) {
            try {  
                // + | -------------------------------------------             
                // + | bind view
                // + | 
                $v_handle = "bindfile";               
                $this->regSystemVars(null, null);
                $this->setEnvParam(self::VIEW_ARGS, $params);
                $this->_include_file_on_context($f);
            } catch (Exception $ex) {
                throw $ex; 
            }
        }
        if ($v_handle === false){
            throw new PageNotFoundException(__("View {$v} Not Handle ")); 
        }
    }
    ///<summary>copy this fonction to allow file inclusion on the current context controller</summayr>
    /**
     * copy this fonction to allow file inclusion on the current context controller
     */
    protected function _include_file_on_context($file)
    {
        $this->_include_constants();
        igk_reset_globalvars();
        $fname = igk_io_getviewname($file, $this->getViewDir());
        $rname = igk_io_view_root_entry_uri($this, $fname);
        $context = __FUNCTION__;
        extract($this->utilityViewArgs($fname, $file));   
        // extract param view parameter
        extract($this->getSystemVars());
        
        $this->setEnvParam("fulluri", $furi);
        $params = isset($params) ? $params : array();   
        $query_options = $this->getEnvParam(IGK_VIEW_OPTIONS);
        $is_direntry = (count($params) == 0) && igk_str_endwith(explode('?', igk_io_request_uri())[0], '/');
        $this->bindNodeClass($t, $fname, strtolower((isset($css_def) ? " " . $css_def : "")));

        $doc->body["class"] = "-custom-thumbnail";
        $doc->title = "IGKDEV";
        $ob_level = ob_get_level();


        try {
            $viewargs = get_defined_vars();
            igk_set_env(IGKEnvironment::CURRENT_CTRL, $this);
            igk_set_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS, $viewargs);
            extract($this->_get_extra_args($file));
            $targs = get_defined_vars();

            //+ | ----------------------------------------------------------------
            //+ | handle action: insert here a middleware to auto handle the view before include 
            //+ |   

            if (!$this->getEnvParam(self::NO_ACTION_FLAG) && (igk_count($params) > 0) && key_exists(0, $params) && ($handler = $this->getActionHandler($fname, $params[0]))) {                
                $handler::Handle($this, $fname, $params);
            } 

            // + | ----------------------------------------------------------------
            // + | check if view already loaded:
            // + | do not include view file in case file already beeing include by the loader
            
            $g = ($loader = $this->getLoader())? $loader->loaded_files() : null;
            if ($g && in_array($file, $g)){
                if (!empty($buffer = $this->_output )){
                    $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->Content = $buffer; 
                } 
                return;
            } 

            ob_start();
            $bckdir = set_include_path(dirname($file) . PATH_SEPARATOR . get_include_path());
            igk_environment()->viewfile = 1;
            $response = igk_include_view_file($this, $file, $targs);
            igk_environment()->viewfile = 0;
            set_include_path($bckdir);
            $out = ob_get_contents();
            ob_end_clean(); 

            if (!empty($out)) {
                $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->Content = $out;
            }
            if ($response && (is_object($response) || is_array($response))) {
                // + | Bind response
                \IGK\System\Http\Response::HandleResponse($response);
                igk_exit();
            }


        } catch (\Exception $ex) {
            if (ob_get_level()>0){
                igk_ob_clean();
            }            
           throw $ex;
        }
    }

    ///<summary>include constant</summary>
    /**
     * include constant
     */
    protected function _include_constants()
    {
        if (($f = $this->getConstantFile()) && file_exists($f))
            include_once($f);
        if (($f = $this->getDbConstantFile()) && file_exists($f))
            include_once($f);
        unset($f);
    }

    ///<summary></summary>
    ///<param name="file"></param>
    /**
     * 
     * @param mixed $file
     */
    protected function _get_extra_args($file)
    {
        $data = [];
        if (igk_is_included_view($file)) {
            $tab = igk_get_env(IGKEnvironment::CTRL_CONTEXT_SOURCE_VIEW_ARGS);
            $data["source_args"] = $tab[spl_object_hash($this)];
        }
        return $data;
    }
    ///<summary></summary>
    /**
     * @return string default name attached to this controller 
     */
    public function getName()
    {
        return strtolower(get_class($this));
    }
    ///<summary>get store parameter referece value</summary>
    ///<param name="register">get store parameter referece value</param>
    /**
     * get store parameter 
     * @return mixed objet reference value
     */
    public function &getParam($key, $default = null, $register = false)
    {
        $m = $this->getM_();
        $c = &$m->getFlag($key, $default, $register);
        return $c;
    }
    ///<summary>get stored params keys</summary>
    /**
     * get stored params keys
     * @return array stored params keys
     */
    public function getParamKeys()
    {
        return array_keys((array)$this->getParams());
    }
    ///<summary>get all controller's parameters</summary>
    /**
     * get all controller's parameters
     */
    public function getParams()
    {
        return $this->getM_();
    }
    ///override this method to show the controller view.
    /**
     */
    public function getDeclaredFileName()
    {
        $tab = &igk_environment()->createArray("reflect_info");
        $cl = get_class($this);
        if ($c = igk_getv($tab, $cl)) {
            return $c->filename;
        }
        // * init local path
        $h = igk_sys_reflect_class($cl);
        $c = (object)[
            "filename" => Path::LocalPath($h->getFileName())
        ];
        $tab[$cl] = $c;
        return $c->filename;
    }
    ///<summary></summary>
    /**
     * @return string
     */
    public function getDeclaredDir():string
    {
        return dirname($this->getDeclaredFileName());
    }
    public function getClassesDir()
    {
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER, IGK_CLASSES_FOLDER]);
    }
    public function getLibDir()
    {
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER]);
    }
    ///<summary>get view dir</summary>
    /**
     * get view dir
     */
    public function getViewDir()
    {
        return ControllerPaths::Gets($this)->viewDir; 
    }
    ///<summary>get style directory folder</summary>
    /**
     * 
     */
    public function getStylesDir()
    {
        return ControllerPaths::Gets($this)->stylesDir;
    }
    ///<summary>get primary style file</summary>
    /**
     * get style dir
     */
    public function getPrimaryCssFile()
    {

        return igk_io_dir($this->getStylesDir() . "/" . igk_getv($this->getConfigs(), "PrimaryStyle", "default.pcss"));
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function getConfigFile()
    {
        return $this->getDataDir() . "/" . IGK_CTRL_CONF_FILE;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getDataDir()
    {
        return $this->getDeclaredDir() . "/" . IGK_DATA_FOLDER;
    }
    ///<summary>get the constant file </summary>
    /**
     * get the constant file
     */
    public function getConstantFile()
    {
        return $this->getDeclaredDir() . "/.constants.php.inc";
    }
    ///<summary></summary>
    /**
     * Db constant utility
     */
    public function getDbConstantFile()
    {
        return $this->getDeclaredDir() . "/.db.constants.php";
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getResourcesDir()
    {
        return $this->getDataDir() . "/" . IGK_RES_FOLDER;
    }
    ///<summary>get controlleur current configuration</summary>
    /**
     * get controlleur current configuration
     * @return IControllerConfigurationData 
     */
    public function getConfigs()
    {
        $key = IGK_ENV_CONFIG_ENTRIES; 
        $cf = $this->getConfigFile();  
        if (!($tab = igk_environment()->get($key))) {
            $tab = array();
        }
        if ($m = igk_getv($tab, $cf)) {
            return $m;
        }
        if (empty($cf)) {
            igk_wln_e("configuration file is empty ", $cf, $this);
        }
        $c = new ControllerConfigurationData($this);       
        if (file_exists($cf)){ 
            $c->initConfigSetting($this->_loadCtrlConfig());
        }
        $tab[$cf] = $c;
        igk_environment()->set($key,  $tab);
        return $c;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getLoader()
    {
        $l = $this->getEnvParam("loader");
        if ($l == null) {
            $l = new Loader($this, function () {
                return (object)["entryNS" => $this->getEntryNamespace()];
            });
            $this->setEnvParam("loader", $l);
        }
        return $l;
    }
    ///<summary>utility view args</summary>
    /**
     * utility view args
     */
    protected function utilityViewArgs($fname, $file = null)
    {
        $this->setCurrentView($fname, false);
        $furi = $this->getAppUri($fname);
        $dir = dirname($file);
        $cview = $this->getCurrentView();
        $entryuri = igk_io_view_entry_uri($this, $fname);
        return get_defined_vars();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getContentDir()
    {
        return igk_io_dir($this->getDeclaredDir() . DIRECTORY_SEPARATOR . IGK_CONTENT_FOLDER);
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
    public function regSystemVars($args = null, $options = null)
    {
       
        if ($args === null) {
            $this->setEnvParam(self::VIEW_ARGS, null);
            igk_set_env(igk_ctrl_env_view_arg_key($this), null);
        } else {

            $g = $this->getEnvParam(self::VIEW_ARGS);
            if (is_array($args)) {
                if (is_array($g)) {
                    $args = array_merge($g, $args);
                }
            }
            $this->setEnvParam(self::VIEW_ARGS,  $args);            
        }
        if (is_string($options) && !empty($options)) {
            $options = igk_get_query_options($options);
        }
        $this->setEnvParam(IGK_VIEW_OPTIONS, $options);
    }
     
    ///<sample>editor[/package/function/arg1/args2]</sample>
    /**
     * @param string $view extension
     * @param string $checkfile _exist
     * @param ?array & $param extra view extension
     * @param string &extra view extension
     */
    public function getViewFile($view, $checkfile = 1, & $param=null)
    { 
        $extension = IGK_DEFAULT_VIEW_EXT;
        $_viewdir  = $this->getViewDir();

        if ($e = igk_getv(array_slice(func_get_args(), 3), 0))
            $extension = $e;
        
        
        if (empty($view))
            $view = IGK_DEFAULT_VIEW;
        else if ($rp = realpath($view))
        {
            // + check that the file depend on controller 
            if (strpos($rp, realpath($_viewdir))===0){
                return $view;
            } 
        }
        $f = igk_html_uri(rtrim($_viewdir. "/" . $view, '/'));
        $ext = $extension; 
        if (is_dir($f)) {
            //window allow dir and file with the same name
            if (file_exists($cf = $f . "/" . IGK_DEFAULT_VIEW_FILE)) {
                $f = $cf;
            } else {
                $f = $f . "." . $extension;
            }
        } else { 
            $ext_regex = '/\.' . $ext . '$/i';
            $ext = preg_match( $ext_regex , $view) ? '' : '.' . $ext;
            $f = $f . $ext;
            if (!empty($ext) && $checkfile){
                $s = 1;
                $_views = array_filter(explode("/", $view));
                while( $s && (count($_views)> 0) && ($f!= $_viewdir)){
                    if (  preg_match( $ext_regex, $f) && is_file($f)) {
                        return $f;
                    } else {
                        $bname = basename($f);
                        $f = dirname($f); 
                        if (($bname != IGK_DEFAULT_VIEW_FILE) && (file_exists($c = $f."/".IGK_DEFAULT_VIEW_FILE))){                    
                            if (!in_array($bname,[IGK_DEFAULT_VIEW]))
                            {
                                array_unshift($param, array_pop($_views));
                            }
                            return $c;
                             
                        }else{
                            array_unshift($param, array_pop($_views));                            
                        } 
                    }
                }
                if ($s){
                    return $f."/" . IGK_DEFAULT_VIEW . '.' . $extension;
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
    public function getCtrlFile($path)
    {
        if (Path::getInstance()->realpath($path) == $path)
            return $path;
        return igk_io_dir(dirname($this->getDeclaredFileName()) . DIRECTORY_SEPARATOR . $path);
    }    
    
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return *
     */
    protected function &getM_()
    {
        $classname = get_class($this);
        if (($r = IGKFv::Get($classname)) === null) {
            $c = &igk_app()->session->getRegisteredControllerParams($classname);
            if ($c !== null) {
                $r = IGKFv::Create($classname, $c);
                return $r;
            }
            $tab = array();
            $r = IGKFv::Create($classname, $tab); 
        }
        return $r;
    }
    ///<summary>get the flag value</summary>
    /**
     * get the flag value
     */
    public function getFlag($code, $default = null)
    {
        return $this->getM_()->getFlag($code, $default);
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getCurrentView()
    {
        return $this->getEnvParam(self::CURRENT_VIEW, IGK_DEFAULT_VIEW);
    }
    ///<summary>set controller current view</summary>
    ///<param name="options">extra option to pass to view</param>
    /**
     * set controller current view
     * @param mixed $view 
     * @param bool $reload force reload if $view is the same
     * @param mixed $targetNode passed target node
     * @param mixed $args argument to attach to view 
     * @param mixed $options extra option
     * @return void 
     * @throws IGKException 
     * @throws ResourceNotFoundException 
     * @throws PageNotFoundException 
     */
    public function setCurrentView($view, $reload = true, $targetNode = null, $args = null, $options = null)
    {

        $cview = $this->getCurrentView();
        if ($cview != $view) {
            $this->setEnvParam(self::CURRENT_VIEW, $view);
        }
        if ($reload) {
            $t = $this->getTargetNode();
            $bck = $targetNode && ($targetNode !== $t) ? $t : null;
            if ($bck)
                $this->setTargetNode($targetNode);
            $this->regSystemVars($args, $options); 
            $this->View();
            if ($bck)
                $this->getTargetNode($bck);
        }
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getTargetNode()
    {
        $b = $this->getEnvParam(IGK_CTRL_TG_NODE) ?? (function () {
            $g = $this->initTargetNode();
            $this->setEnvParam(IGK_CTRL_TG_NODE, $g);
            return $g;
        })();
        return $b;
    }
    ///<summary></summary>
    /**
     * 
     */
    protected function initTargetNode()
    {
        $tagName = igk_sys_getconfig("app_default_controller_tag_name", "div");
        $div = new HtmlCtrlNode($this, $tagName);
        $div["id"] = igk_css_str2class_name(strtolower($this->getName()));
        return $div;
    }
    /**
     * get the visibility of this controller view node. 
     * @return true 
     */
    protected function getIsVisible()
    {
        return true;
    }

    ///<summary>override this method to show the controller view.</summary>
    /**
     * override this method to show the controller view.
     */
    public function View()
    {
        // + | ------------------------------------------------
        // + | View contains mandary variables fields. 
        // + | t = the target node . 
        // + | ctrl = current controller 
        // + | fname = entry file name
        // + | doc = current document
        // + | controller and target node must match visibility
        $t = igk_getv($this->getSystemVars(), "t");
        $visible = $this->getIsVisible();
        if ($t) {
            $t->setIsVisible($visible);
            if ($visible) {
                $this->_initView();
                $this->_renderViewFile();
            }
        } else {
            igk_ilog("/!\\ TargetNode is null " . get_class($this));
        }
    }
    ///<summary>get system variables for this controller.</summary>
    /**
     * get system variables for this controller.
     */
    public function getSystemVars()
    {

        $ck = igk_ctrl_env_view_arg_key($this);
        $t = igk_get_env($ck);
        $c = $this->getEnvParam(self::VIEW_ARGS);
        if ($t !== null) {
            return $t;
        }
        $t = array();
        $t["t"] = $this->getTargetNode();
        $t["ctrl"] = $this;
        if (isset($c["doc"])) {
            $t["doc"] = $c["doc"];
        } else {
            $doc = $this->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY);
            if (!$doc) {
                $doc = igk_app()->getDoc();
            }
            $t["doc"] = $doc;
        }
        if ($viewctx = $this->getEnvParam(IGK_CTRL_VIEW_CONTEXT_PARAM_KEY)) {
            $t["viewcontext"] = $viewctx;
        }
        if (igk_count($_REQUEST) > 0)
            $t = array_merge($t, array("request" => Request::getInstance()));
        $tab = $this->getApp()->getControllerManager()->getControllers();
        if (is_array($tab)) {
            $t["controllers"] = $tab;
        }
        if ($this->getParam("func_get_args") != null) {
            $t["func_get_args"] = $this->getParam("func_get_args");
        }
        if ($c !== null) {
            $t = array_merge($t, array("params" => is_array($c) ? $c : array($c)));
        }
        igk_set_env($ck, $t);
        return $t;
    }
    
    ///<summary>Initialize view setting - before renderging </summary>
    /**
     * Initialize view setting - before renderging
     */
    protected function _initView()
    {
        R::RegLangCtrl($this);
        ControllerExtension::bindCssStyle($this);
    }
    ///<summary>set the flag</summary>
    /**
     * set the flag
     */
    public function setFlag($code, $value)
    {
        $this->getM_()->setFlag($code, $value);
    }
    ///<summary>reset the value of the current view</summary>
    /**
     * reset the value of the current view
     */
    protected function resetCurrentView($view = null)
    {
        $this->setFlag(self::CURRENT_VIEW, $view);
    }
    ///<summary>check if this controller class is a system controller</summary>
    ///<param name="mixed">object|class name of a controller</summary>
    /**
     * check if this controller class is a system controller
     * @param mixed object|class name of a controller
     */
    public static function IsSysController($className)
    {
        if (is_object($className)) {
            $f = igk_html_uri($className->getDeclaredFileName());
            if (strstr($f, IGK_LIB_DIR)) {
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
    public function getViewContent($view, $target, $forcecreation = false, $args = null)
    {
        $key = "ctrl/backupnode";
        $g = $this->getParam($key);
        if ($g) {
            $this->setTargetNode($g);
        }
        $bck = $this->TargetNode;
        $this->setParam($key, $bck);
        $v_view = $this->CurrentView;
        $this->setTargetNode($target);
        $this->getView($view, $forcecreation, $args);
        $this->setTargetNode($bck);
        $this->resetCurrentView($v_view);
        $this->setParam($key, null);
    }
    ///<summary>set the controller parameters</summary>
    /**
     * set the controller parameters
     */
    public function setParam($key, $value)
    {
        $m = $this->getM_();
        $m->setFlag($key, $value);
        return $this;
    }

    ///<summary>call view layout without changing current view</summary>
    /**
     * call view layout without changing current view
     */
    public function getView($view = null, $forcecreation = false, $args = null, $options = null)
    {
        extract($this->getSystemVars());
        $v = igk_io_dir($view != null ? $view : igk_getr("v", $view));
        $f = igk_realpath($v) === $v ? $v : $this->getViewFile($v);
        $this->regSystemVars(null);
        if (file_exists($f) || ($forcecreation && igk_io_save_file_as_utf8($f, IGK_STR_EMPTY))) {
            $def = 0;
            if (($args !== null) && !empty($args)) {
                $def++;
            }
            if (($options != null) && !empty($options)) {
                $def++;
            };
            if ($def > 0)
                $this->regSystemVars($args, $options);
            $this->_initView();
            $this->_include_file_on_context($f);
            $this->regSystemVars(null);
        }
    }

    ///<summary></summary>
    /**
     * 
     */
    public function getCurrentPageFolder()
    {
        return igk_app()->getCurrentPageFolder();
    }

    use ControllerUriTrait;


    ///<summary>view complete.</summary>
    /**
     * view complete.
     */
    protected function _onViewComplete()
    {
        if ((($x = $this->getFlag(self::REG_VIEW_CHILD)) != null) && is_array($x)) {
            foreach ($x as $v) {
                $m = $v->func;
                $v->ctrl->Invoke($m, $this);
            }
        }
        // igk_invoke_session_event(IGKEvents::VIEWCOMPLETE, array($this, null));
        igk_hook(IGKEvents::VIEWCOMPLETE, array("ctrl" => $this));
    }

    ///<summary>include view on contex</summary>
    /**
     * include view on contex
     */
    protected function _include_view_file($view, $args = null)
    {
        $v_file = file_exists($view) ? $view : $this->getViewFile($view);
        if (file_exists($v_file) === true) {
            $d = null;
            if ($args !== null) {
                $d = $this->getSystemVars();
                $this->regSystemVars(null);
                $this->regSystemVars($args);
            }
            $this->_include_file_on_context($v_file);
            if ($d)
                $this->regSystemVars($d);
        }
    }

    ///<summary> get default data adapter name</summary>
    /**
     * get default data adapter name
     */
    public function getDataAdapterName()
    {
        return igk_sys_getconfig("default_dataadapter", IGK_MYSQL_DATAADAPTER);
    }

    ///<summary></summary>
    /**
     * return controller table info
     */
    public function getDataTableInfo()
    {
        if ($this->getUseDataSchema()) {
            $tb = igk_getv($this->loadDataFromSchemas(), "tables");
            return $tb;
        }
    }
    /**
     * default table name
     * @return null|string 
     */
    public function getDataTableName()
    {
        // override this to handle management of a spécific table 
    }

    ///<summary></summary>
    ///<param name="className"></param>
    /**
     * 
     * @param mixed $className
     */
    public static function RegSysController($className)
    {
        if (self::$sm_sysController == null)
            self::$sm_sysController = array();
        if (class_exists($className)) {
            self::$sm_sysController[$className] = $className;
        }
    }

    ///<summary></summary>
    /**
     * 
     */
    protected function getUseDataSchema()
    {
        return !self::IsSysController(get_class($this)) && igk_getv($this->getConfigs(), "clDataSchema");
    }
    public function setTargetNode($node)
    {
        $this->setEnvParam(IGK_CTRL_TG_NODE, $node);
        return $this;
    }
}
