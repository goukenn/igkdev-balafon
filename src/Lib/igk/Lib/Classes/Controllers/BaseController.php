<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BaseController.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Controllers;

use Exception;
use IGK\Actions\ActionResolutionInfo;
use IGK\Actions\Traits\ApiActionTrait;
use IGK\Helper\ActionHelper;
use IGK\Helper\Activator;
use IGK\Helper\ApplicationModuleHelper;
use IGK\Helper\ExceptionUtils;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper;
use IGK\System\Models\IModelDefinitionInfo;
use IGK\Resources\R;
use IGK\Server;
use IGK\System\Configuration\ControllerConfigurationData;
use IGK\System\Console\Logger;
use IGK\System\Database\SchemaMigrationInfo;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\ResourceNotFoundException;
use IGK\System\Helper;
use IGK\System\Html\Dom\HtmlCtrlNode;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Http\PageNotFoundException;
use IGK\System\Http\Request;
use IGK\System\IO\FileHandler;
use IGK\System\IO\Path;
use IGK\System\Modules\ModuleManager;
use IGK\System\Uri;
use IGK\System\ViewDataArgs;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\WinUI\IViewLayoutLoader;
use IGKConstants;
use IGKEnvironment;
use IGKEvents;
use IGKException;
use IGKFv;
use IIGKDataController;
use ReflectionClass;
use ReflectionException;

use function igk_resources_gets as __;

/**
 * @package IGK\Controllers
 * @method static void article(string $articlePath, array $data) marcos function . bind article with data. 
 * @method static bool getCanInitDb() check if this controller entry can init database
 * @method static bool initDb(bool $force) macros method. init controller database
 * @method static string name(string $path) macros method. get resolved key name
 * @method static void InitDataBaseModel(array $definition, bool $force=false, bool $clean=false) macros function
 * @method static InitDataFactory() macros function
 * @method static InitDataInitialization() macros function
 * @method static InitDataSeeder() macros function
 * @method static bool IsEntryController() macros function
 * @method static bool IsFunctionExposed() macros function
 * @method static bool IsUserAllowedTo() macros function - check if current user is allowed to 
 * @method static ?string asset(string $path, bool $exit=true) macros function resolve asset path to uri if asset exists.\
 *  $exist to check that the file must be present or not before resolved
 * @method static string|null asset_content(string $path) macros function : \
 * get asset content if found in $controller->getDataDir()."/assets" by default
 * @method static string baseUri() macros function
 * @method static bindNodeClass() macros function
 * @method static string buri(string $path) macros function
 * @method static cache_dir() macros function
 * @method static string classdir() macros function get entry class directory
 * @method static string configDir() macros function get configuration directory
 * @method static string configFile(string $config_path) macros function get configuration file from config dir
 * @method static BaseController ctrl(bool $register_autoload=false) macros function get controller instance
 * @method static void db_add_column() macros function
 * @method static void db_change_column() macros function
 * @method static \IIGKQueryResult db_query(string $query) macros function
 * @method static void db_rename_column() macros function
 * @method static void db_rm_column() macros function
 * @method static void dispatchToModelUtility() macros function
 * @method static bool dropDb($navigate=1, $force=0) macros function drop controller database model
 * @method static void furi() macros function
 * @method static string getAuthKey(string $extrakey) macros function : get controller authentication key - not the same as authName macros
 * @method static string authName(string $name) macros function : get controller authentication Name  = controller@keyname use for authorization
 * @method static void getAutoresetParam() macros function
 * @method static string getBaseFullUri() macros function
 * @method static void getCacheInfo() macros function
 * @method static bool getCanInitDb() macros function
 * @method static bool getCanModify() macros function
 * @method static void getComponentsDir() macros function
 * @method static \IGKHtmlDoc getCurrentDoc() macros function
 * @method static object getDataAdapter() macros function data driver
 * @method static string getDataSchemaFile() macros function
 * @method static ?\IGK\System\Database\DbSchemaMigrationInfo getDataTableDefinition(?string $tablename=null) macros function
 * @method static void getEnvKey() macros function
 * @method static mixed getEnvParam() macros function
 * @method static void getEnvParamKey() macros function
 * @method static void getInitDbConstraintKey() macros function
 * @method static bool getIsVisible() macros function
 * @method static void getRouteUri() macros functiong
 * @method static void getTestClassesDir() macros function
 * @method static ?\IGK\System\Database\IUserProfile getUser() macros function get controller user profile
 * @method static void array getViewArgs() macros function
 * @method static string hookName() macros function get hook name
 * @method static void initDbConstantFiles() macros function
 * @method static void initDbFromFunctions() macros function
 * @method static void initDbFromSchemas() macros function
 * @method static void libdir() macros function
 * @method static object|\IGK\Database\DbSchemaLoadEntriesFromSchemaInfo loadDataAndNewEntriesFromSchemas() macros function load data and update the datable with entries
 * @method static mixed|\IGK\Database\IDbSchemaInfo loadDataFromSchemas() macros function load data from schema file. do not modify the database
 * @method static bool login(mixed $user, ?string $password, bool $nav=true, bool $rememberme=false) macros function. try login with the user
 * @method static void logout() macros function
 * @method static void migrate() macros function
 * @method static ?\IGK\Models\ModelBase model(string $modelName model name) macros function search for model by name. 
 * @method static object|null modelUtility() macros function 
 * @method static void notifyKey() macros function
 * @method static string ns(string $path) macros function
 * @method static static register_autoload() macros function register macros function
 * @method static ?string resolveClass(string $path) macros function resolve class. return null if not exists
 * @method static void resolveAssets(array<string> $asset_list) macros function resolve class. return null if not exists * 
 * @method ?string asset(string $path, bool $must_exist=true) macros function resolve controller assets  * 
 * @method static string resolveTableName(string $real_table_name) macros function resolve to entry table
 * @method static void seed() macros function
 * @method static void setEnvParam(key, value) macros function
 * @method static void storeConfigSettings() macros function
 * @method static string uri(?string $path) macros function 
 * @method static string loadMigrationFile() macros function 
 * @method bool checkUser(bool $redirect, ?string $redirectUri=null ) macros function check if user or navigate
 * @method static string getErrorViewFile(int code) macros function get controller error file
 * @method static mixed getConfig(string $name, mixed $default=null) macros function get config setting
 * @method static mixed js(string $name, default=null) macros function load inline js script
 * @method static mixed pcss(string $name, default=null) macros function load temp inline pcss
 * @method static mixed getViews(bool $withHiddenFile, bool $recursive=false) macros function load temp inline pcss
 * @method static mixed getActionHandler(string $name, ActionResolutionInfo $action_resolution, ?array $params =null) macros function load temp inline pcss
 * @method static array getCachedDataTableDefinition() macros function get cached datable table definitions 
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
    const VIEW_EXTRA_ARGS = VIEW_EXTRA_ARGS;

    /**
     * 
     * @var mixed
     */

    private static $sm_sysController = [];


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
    // protected function _resolview($f, ?array $params = [])
    // {
    //     return false;       
    // }
    ///<summary></summary>
    /**
     * 
     */
    protected function _renderViewFile()
    {

        $ctrl = $this;
        $params = null;
        $find = false;
        $allowed_view = true;
        extract($ctrl->getViewArgs());
        $v_handle = false;
        $f = "";
        $v = $this->getCurrentView() ?? igk_die("current view is null. " . get_class($this));
     
        if (empty($v)){
            igk_die('empty view not allowed');
        }
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
        if ($params === null) {
            $params = [];
        } 
        // + | -------------------------------------------------------
        // + | no method exists in controller and view file not exists
        // + |
        if (
            !$meth_exits && ($allowed_view = $this->_isAllowedView($v)) &&
            !file_exists($f = ($this->getViewFile($v, 0, $params)))
        ) { 
            //
            $v_handle = "file";
            if (!$find) {
                if (igk_is_conf_connected() && Server::IsLocal()) {
                    if (!igk_io_save_file_as_utf8($f, igk_get_defaultview_content($this), true)) {
                        igk_ilog("can't create the file " . $f . " AT " . __LINE__);
                        igk_exit();
                    }
                } else {
                    $message = __("res.notfound_1", igk_io_collapse_path($f));
                    if (!igk_get_contents($this, 404, [$message, 404])) {
                        if (!igk_sys_env_production()) {
                            $m = "<b>[IGK] - can't get resource " . $f . " AT " . __LINE__ . " ruri:" . igk_io_request_uri() . "</b>";
                            $m .= "<div>" . igk_show_trace() . "</div>";
                            $this::showError($m, "View resource not found");
                        }
                        throw new ResourceNotFoundException($message, $v, 404);
                    }
                }
            } else {
                $f = $find;
            }
        } 

        if ($allowed_view && file_exists($f)) { 
            try {
                // + | -------------------------------------------             
                // + | bind view
                // + | 
                $v_handle = "bindfile";
                $this->setEnvParam(self::VIEW_ARGS, $params);
                $this->_include_view($f); 
            } catch (Exception $ex) {
                throw $ex;
            }
        }
        if ($v_handle === false) {
            throw new PageNotFoundException(__("View {$v} Not Handle "));
        }
    }
    /**
     * check if view path is allowed
     * @param string $view 
     * @return bool 
     */
    protected function _isAllowedView(string $view): bool
    {
        $allowed_view = true;
        if (!$this->{ControllerEnvParams::AllowHiddenView}) {
            foreach (explode("/", $view) as $n) {
                if (strpos($n, ".") === 0) {
                    $allowed_view = false;
                    break;
                }
            }
        }
        return $allowed_view;
    }
    /**
     * create view loader 
     * @return IViewLayoutLoader
     */
    protected function createViewLoader(): ?IViewLayoutLoader
    {
        if (ViewHelper::CurrentCtrl() === $this) {
            //by default create a layout per view 
            if ($n = ViewHelper::GetViewArgs("fname")) {
                $p = "/WinUI/Views/" . ucfirst($n) . "ViewLoader";
                if (($cl = $this->resolveClass($p)) && is_subclass_of($cl, IViewLayoutLoader::class)) {
                    return new $cl($this);
                }
            }
        }
        return new ViewLayoutLoader($this);
    }
    /**
     * get the view loader
     * @return null|IViewLayoutLoader
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function getViewLoader()
    {

        if ($l = $this->getEnvParam(ControllerEnvParams::ViewLoader)) {
            return $l;
        }
        $l = $this->createViewLoader();
        !$l && igk_die("failed to create view loader");
        $this->setEnvParam(ControllerEnvParams::ViewLoader, $l);
        return $l;
    }
    /**
     * handle action 
     * @param string $fname 
     * @param array $params 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function handleAction(string $fname, array $params, &$handler = null)
    {
        // igk_trace();
        // igk_wln_e(__FILE__.":".__LINE__ , "try action handler....", $fname, igk_view_args('query_options'));
        // igk_dev_wln_e(__FILE__.":".__LINE__ , "handling, ", $fname, $params, "action flag:", $this->getEnvParam(self::NO_ACTION_FLAG));
        //+ | -----------------------------------------------------------------------------
        //+ | handle action: insert here a middleware to auto handle the view before include 
        //+ |   
        
        if (
            !$this->getEnvParam(self::NO_ACTION_FLAG) &&
            ($handler = $this->getActionHandler($fname,$rep = new ActionResolutionInfo, $params))
            ) {
              $params = $rep->params ?? $params; // 
            $srv = igk_server(); 
            $r =  ActionHelper::DoHandle($this, $handler, $fname, $params, $rep,[
                'method'=>$srv->REQUEST_METHOD,
                'is_ajx'=>($srv->CONTENT_TYPE == "application/json") || igk_is_ajx_demand(), // is_ajx
            ]);            
            

            
            return $r;
        }
    }
    protected function _config_entries($fname)
    {
        $conf = $this->configFile('views');
        $redirect_request = null;
        if (file_exists($conf)) {
            $inc = function () {
                return include(func_get_arg(0));
            };
            $conf = $inc($conf);
            $def = Activator::CreateNewInstance(static::viewConfigClass(), $conf);
            $entry = $def->default_dir_entry;
            if ($entry == $fname) {
                ViewHelper::ForceDirEntry($this, $fname, $redirect_request);
            } else {
                if (in_array($fname, $def->is_dir_entry)) {
                    ViewHelper::ForceDirEntry($this, $fname, $redirect_request);
                } else {
                    if ($entry) {
                        ViewHelper::CurrentDocument()->setBaseUri($this->getAppUri($entry));
                    }
                }
            }
            if ($redirect_request) {
                $_POST = $redirect_request;
                $_REQUEST = array_merge($_REQUEST, $redirect_request[ViewHelper::REDIRECT_PARAM_NAME]);
            }
        }
    }
    /**
     * default include function helper
     * @return void 
     */
    protected function _include_func_helpers(){
        include_once(IGK_LIB_DIR."/Lib/functions-helpers/view.php");

    }
    ///<summary>copy this fonction to allow file inclusion on the current context controller</summayr>
    /**
     * copy this fonction to allow file inclusion on the current context controller
     */
    protected final function _include_view(string $file)
    {
        $response = null;
        $this->_include_func_helpers();
        $this->_include_constants();
        igk_reset_globalvars();
        $viewargs = (array)ViewEnvironmentArgs::CreateContextViewArgument($this, $file, __FUNCTION__);
        igk_set_env(IGKEnvironment::CURRENT_CTRL, $this);
        igk_set_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS, $viewargs);
        extract($viewargs);
        igk_hook(IGKEvents::HOOK_INIT_INC_VIEW, [
            'ctrl'=>$this,
            'file'=>$file
        ]); 

        $action_handler = null;
        try {
            // + | binding environment 
            $this->_config_entries($fname);
            try {
                $handle_response = $this->handleAction($fname, $params, $action_handler);
                $i = igk_environment()->action_handler_instance;
                if ($handle_response && igk_sys_support_trait($i, ApiActionTrait::class)) {
                    igk_exit();
                }
                if ($i && ($redirect = $i->redirect ?? igk_getr('redirect'))) {
                    igk_navto($redirect);
                }
            } catch (\Exception $ex) {
                // + | handler failed or thro an exception. 
                // + | method no present
                if (igk_environment()->isDev()) {
                    igk_dev_ilog(implode('|',[
                        "exception raise : " . $ex->getMessage(),
                        $ex->getFile(),
                        $ex->getLine()
                    ]));
                    igk_set_header(500); 
                    Logger::danger("[BLF] - path error: " . $ex->getMessage());
                    ExceptionUtils::ShowException($ex);
                    igk_exit();
                }
                igk_ilog("/!\\ Action Handler failed ::" . $ex->getMessage(), null, 0, false);
            }
            // + | ----------------------------------------------------------------
            // + | check if view already loaded:
            // + | do not include view file in case file already beeing include by the loader

            $g = ($loader = $this->getLoader()) ? $loader->loaded_files() : null;
            if ($g && in_array($file, $g)) {
                if (!empty($buffer = $this->_output)) {
                    $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->Content = $buffer;
                }
                return;
            }

            $viewargs['data'] = $this->_getViewDataArgs();
            $viewargs['user'] = $this->getUser();
            $viewargs['action_handler'] = $action_handler;

            igk_set_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS, $viewargs);
            ob_start();
            $bckdir = set_include_path(dirname($file) . PATH_SEPARATOR . get_include_path());
            igk_environment()->viewfile = 1;
            $response = $this->getViewLoader()->include($file, $viewargs);
            igk_environment()->viewfile = null;
            set_include_path($bckdir);
            $out = ob_get_contents();

            if (($level=  ob_get_level())==0){
                igk_wln_e("missing.... level ", $level, $file);
            }
            ob_end_clean();
            if (!empty($out)) {
                $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->setContent($out);
            }
            if ($this->getEnvParam(ControllerParams::REPLACE_URI)) {
                // + | replace to entry uri if not a default controller 
                $uri = ''; 
                if (!igk_ctrl_is_default_controller($this))
                    $uri = dirname($fname);
                $g = $this->getAppUri($uri); 
                if ($g != igk_io_baseuri($uri)){
                    $t->replace_uri($this->getAppUri($uri));
                }
            }
            // disable parameter view response
            if (!$this->getEnvParam(ControllerEnvParams::NoDoViewResponse) &&  $response && (is_object($response) || is_array($response))) {
                // + | Bind response               
                \IGK\System\Http\Response::HandleResponse($response);
                igk_exit();
            }
        } catch (\Exception $ex) {
            if (ob_get_level() > 0) {
                igk_ob_clean();
            }
            throw $ex;
        }
        return $response;
    }

    protected function _getViewDataArgs(){
        $rep = $this->getEnvParam(ControllerEnvParams::ActionViewResponse);
        $cp = [];
        if (!is_bool($rep)){
            $cp = $rep ?? [];
        }
        return new ViewDataArgs($cp);
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
            $data["source_args"] = $tab ? igk_getv($tab, spl_object_hash($this)) : null;
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
        $param = &$this->getM_();
        $o = isset($param[$key]) ? $param[$key] : $default;
        return $o;
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
    public function getDeclaredDir(): string
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
    protected function getPrimaryCssFile()
    {
        // $prima_file = $this->getConfig("PrimaryStyle", "default.pcss");

        return implode("/", [$this->getStylesDir(), "default.pcss"]); // $this->getConfig("PrimaryStyle", "default.pcss")]);
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
        //if (file_exists($cf)) {
        $c->initConfigSetting($this->_loadCtrlConfig());
        //}
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
    protected function utilityViewArgs(string $fname, ?string $file = null)
    {
        $this->setCurrentView($fname, false);
        $furi = $this->getAppUri($fname);
        $dir = $file ? dirname($file) : null;
        $cview = $this->getCurrentView();
        $entry_uri = igk_io_view_entry_uri($this, $fname);
        return get_defined_vars();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getContentDir()
    {
        return igk_dir($this->getDeclaredDir() . DIRECTORY_SEPARATOR . IGK_CONTENT_FOLDER);
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
            // clean system vars
            igk_set_env(igk_ctrl_env_view_arg_key($this), null);
        } else {
            $g = $this->getEnvParam(self::VIEW_ARGS);
            if (is_array($args)) {
                if (is_array($g)) {
                    $args = array_filter(array_merge($g, $args));
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
     * resolve view files and update parameters
     * @param string $view extension
     * @param string $checkfile _exist
     * @param ?array $param extra view extension
     * @param string &extra view extension
     * @return string view file path
     */
    public function getViewFile(string $view, $checkfile = 1, &$param = null)
    {
        $extension = IGK_DEFAULT_VIEW_EXT;
        $_viewdir = $this->getViewDir();
        if ($param === null) {
            $param = [];
        } else if (!is_array($param)) {
            $param = [$param];
        }
        if ($e = igk_getv(array_slice(func_get_args(), 3), 0))
            $extension = $e;
        if (empty($view))
            $view = IGK_DEFAULT_VIEW;
        else if ($rp = realpath($view)) {
            // + check that the file depend on controller 
            if (strpos($rp, realpath($_viewdir)) === 0) {
                return $view;
            }
        }

        $f = igk_uri(rtrim(Path::Combine($_viewdir, $view), '/'));
        // + | get fname to UNIX PATH
        $f = IO::GetUnixPath("/" . $view, false, $_viewdir) ?? $f;
        if (is_dir($f)) {
            //+ | from directory handle ViewContext by extension 
         
            if ($cf = FileHandler::ResolveFile($f, 'default', FileHandler::FILE_CONTEXT_VIEW)){
                return $cf;
            }
            


            //window allow dir and file with the same name
            if (is_file($cf = $f . "/" . IGK_DEFAULT_VIEW_FILE)) {
                return $cf;
            } else {
                // add extension
                $f = $f . "." . $extension;
                if (is_file($f)) {
                    return $f;
                }
            }
        }
        $v_cf = ViewHelper::ResolveViewFile($_viewdir, $view, $f, $checkfile, $param);
        return $v_cf;
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
        return igk_dir(dirname($this->getDeclaredFileName()) . DIRECTORY_SEPARATOR . $path);
    }

    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
     * @return *
     */
    protected function &getM_()
    {
        $param = &igk_app()->getSession()->getControllerParams();
        $cl = static::class;
        if (!isset($param[$cl])) {
            $param[$cl] = [];
        }
        $g = &$param[$cl];
        return $g;

        // $classname = get_class($this);
        // if (($r = IGKFv::Get($classname)) === null) {
        //     $c = & igk_app()->getSession()->getRegisteredControllerParams($classname);
        //     if ($c !== null) {
        //         $r = IGKFv::Create($classname, $c);
        //         return $r;
        //     }
        //     $tab = array();
        //     $r = IGKFv::Create($classname, $tab);
        // }
        // return $r;
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
            if (is_null($options) &&  ($path = igk_server()->REQUEST_URI)) {
                // + | --------------------------------------------------------------------
                // + | parse query options
                // + |
                $options = (new Uri($path))->getOptions();
            }

            $this->regSystemVars($args, $options);
            $this->View();
            if ($bck)
                $this->getTargetNode($bck);
        }
    }

    ///<summary>get initialize target node </summary>
    /**
     * get initialize target node 
     */
    public function getTargetNode(): ?HtmlNode
    {
        $b = $this->getEnvParam(IGK_CTRL_TG_NODE) ?? (function () {
            $g = $this->initTargetNode();
            $this->setEnvParam(IGK_CTRL_TG_NODE, $g);
            return $g;
        })();
        return $b;
    }
    ///<summary>init target node</summary>
    /**
     * init target node 
     */
    protected function initTargetNode(): ?HtmlNode
    {
        // igk_debug_wln_e(__FILE__.":".__LINE__,  "init target node .....");
        $tagName = igk_sys_getconfig("app_default_controller_tag_name", "div");
        $div = new HtmlCtrlNode($this, $tagName);
        $div["id"] = igk_css_str2class_name(strtolower($this->getName()));
        return $div;
    }
    /**
     * get the visibility of this controller view node. 
     * @return bool
     */
    protected function getIsVisible(): bool
    {
        return true;
    }

    ///<summary>override this method to show the controller view.</summary>
    /**
     * invoke view logic. \
     * override this method to customize your view logic.
     * @return static
     */
    public function View(): BaseController
    {
        // + | ------------------------------------------------
        // + | View contains mandary variables fields. \IGK\System\ViewEnvironmentArgs
        // + | t = the target node . 
        // + | ctrl = current controller 
        // + | fname = entry file name
        // + | doc = current document
        // + | controller and target node must match visibility
        $v_available = $this->getIsVisible();
        $t = $this->getTargetNode();
        if ($t) {
            $t->setIsVisible($v_available);
            if ($v_available) { 
                $this->_initView();
                $this->_renderViewFile();
            }
        } else {
            igk_ilog("/!\\ TargetNode is null " . get_class($this));
        }
        return $this;
    }
    /**
     * initialize require module
     */
    protected function _initRequiredModules(){
        $v_key = ApplicationModuleHelper::SYS_ENV_KEY;
        $v_modules = igk_get_env($v_key) ?? [];
        $v_cl = get_class($this);
        if (isset($v_modules[$v_cl])){
            return;
        }
        $load = 1;
        $config_file = Path::Combine( $this->getDeclaredDir(), IGKConstants::PROJECT_CONF_FILE);
        if ($data = json_decode(file_get_contents($config_file))){
            $required = (array)igk_conf_get($data,'required');
            $required && ApplicationModuleHelper::ImportRequiredModule($required, $this);
            if ($required){
                $load = $required;
            }
        } 
        $v_modules[$v_cl] = $load;
        igk_set_env($v_key, $v_modules);

    }
    protected function _createViewEnvArgs()
    {
        return new \IGK\System\ViewEnvironmentArgs;
    }
    /**
     * document to render 
     * @param mixed $doc document to reset 
     * @return void 
     */
    protected function setCurrentDocument(?HtmlDocumentNode $doc = null)
    {
        $this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
        return $this;
    }
    ///<summary>get system variables for this controller.</summary>
    /**
     * init system's view variables for this controller.
     */
    public function getSystemVars()
    {
        $ck = igk_ctrl_env_view_arg_key($this);
        $t = igk_get_env($ck);
        if ($t !== null) {
            return $t;
        }
        $view_env_arg = $this->_createViewEnvArgs();
        $view_env_arg->modules = &igk_environment()->require_modules();
        $c = $this->getEnvParam(self::VIEW_ARGS);
        $view_env_arg->t = $this->getTargetNode();
        $view_env_arg->ctrl = $this;
        if ($idoc = igk_getv($c, "doc")) {
            igk_die("not implement");
            $view_env_arg->doc = $idoc;
        } else {
            $doc = $this->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY);
            if (!$doc) {
                $doc = igk_app()->getDoc();
            }
            $view_env_arg->doc = $doc;
        }
        if ($viewctx = $this->getEnvParam(IGK_CTRL_VIEW_CONTEXT_PARAM_KEY)) {
            $view_env_arg->viewcontext = $viewctx;
        }
        if (igk_count($_REQUEST) > 0) {
            $view_env_arg->request = Request::getInstance();
        }
        if (!is_null($func_args = $this->getParam("func_get_args"))) {
            $view_env_arg->func_get_args = $func_args;
        }
        if ($c !== null) {
            $view_env_arg->params = is_array($c) ? $c : array($c);
        }
        $t = (array)$view_env_arg;
        igk_set_env($ck, $t);
        return $t;
    }

    ///<summary>Initialize view setting - before rendering </summary>
    /**
     * Initialize view setting - before rendering
     */
    protected function _initView()
    {
        // + | --------------------------------------------------------------------
        // + | register lang
        // + |

        R::RegLangCtrl($this);
        // + | --------------------------------------------------------------------
        // + | bind style
        // + |

        $this->bindCssStyle();

        // + | --------------------------------------------------------------------
        // + | bind modules with current document
        // + |

        // $modules = igk_environment()->getModulesManager()->getAutoloadModules();
        // if ($modules && ($doc = $this->getCurrentDoc())) {
        //     foreach ($modules as $mod) {
        //         ModuleManager::InitDoc($doc, $mod);
        //     }
        // }
        // igk_dev_wln('autoloads modules', $modules);

        igk_hook(IGKEvents::HOOK_INIT_VIEW, ['ctrl'=>$this]);
        // igk_trace(); 
        // igk_dev_wln_e("autoload ...", __METHOD__);
    }
    ///<summary>set the flag</summary>
    /**
     * set environment param flags the flag
     */
    public function setFlag($code, $value)
    {
        $this->setEnvParam($code, $value);
    }
    ///<summary>reset the value of the current view</summary>
    /**
     * reset the value of the current view
     */
    protected function resetCurrentView($view = null)
    {
        $this->setParam(self::CURRENT_VIEW, $view);
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
            $f = igk_uri($className->getDeclaredFileName());
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
        $m = &$this->getM_();
        if (is_null($value) && $key) {
            unset($m[$key]);
        } else {
            $m[$key] = $value;
        }
        return $this;
    }

    ///<summary>call view layout without changing current view</summary>
    /**
     * call view layout without changing current view
     */
    public function getView($view = null, $forcecreation = false, $args = null, $options = null)
    {
        extract($this->getSystemVars());
        $v = igk_dir($view != null ? $view : igk_getr("v", $view));
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
            $this->_include_view($f);
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
        if ((($x = $this->getEnvParam(self::REG_VIEW_CHILD)) != null) && is_array($x)) {
            foreach ($x as $v) {
                $m = $v->func;
                $v->ctrl->Invoke($m, $this);
            }
        }
        igk_hook(IGKEvents::VIEWCOMPLETE, array("ctrl" => $this));
    }

    ///<summary>include view on contex</summary>
    /**
     * include view on contex
     */
    protected function _include_view_file(string $view, $args = null)
    { 
        $v_file = file_exists($view) ? $view : $this->getViewFile($view);
        if (file_exists($v_file) === true) {
            $d = null;
            if ($args !== null) {
                $d = $this->getSystemVars();
                $this->regSystemVars(null);
                $this->regSystemVars($args);
            }
            $this->_include_view($v_file);
            if ($d)
                $this->regSystemVars($d);
        }
    }

    ///<summary> get default data adapter name</summary>
    /**
     * get default data adapter name
     */
    public function getDataAdapterName(): string
    {
        return igk_sys_getconfig("default_dataadapter", IGK_MYSQL_DATAADAPTER);
    }

    ///<summary></summary>
    /**
     * @return ?IModelDefinitionInfo controller's table info
     */
    public function getDataTableInfo(): ?IModelDefinitionInfo
    { 
        $tb = null;
        if ($this->getUseDataSchema()) {
            $def = $this->getDataTableDefinition(null);
            // + | multi definition info 
            if (!($def instanceof IModelDefinitionInfo)) {
                $def = Activator::CreateNewInstance(SchemaMigrationInfo::class, $def, true);
            }
            $tb = $def;
        }
        return $tb;
    }
    /**
     * default table name
     * @return null|string 
     */
    public function getDataTableName(): ?string
    {
        // override this to handle management of a spécific table 
        return null;
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
    public function getUseDataSchema(): bool
    {
        if (self::IsSysController($this)) {
            if ($this instanceof SysDbController) {
                return true;
            }
            return false;
        }
        return $this->getConfig(IGK_CTRL_CNF_USE_DATASCHEMA, false);
    }
    public function setTargetNode($node)
    {
        $this->setEnvParam(IGK_CTRL_TG_NODE, $node);
        return $this;
    }
}
