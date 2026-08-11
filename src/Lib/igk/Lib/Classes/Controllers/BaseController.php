<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BaseController.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;

use Exception;
use IGK\Actions\ActionResolutionInfo;
use IGK\Actions\Dispatcher;
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
use IGK\System\Controllers\Helper\ViewModuleHelper;
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
use IGK\System\Uri;
use IGK\System\ViewDataArgs;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\WinUI\IViewLayoutLoader;
use IGK\Constants;
use IGK\IDataController;
use IGK\System\EntryClassResolution;
use IGK\System\IInjectedArgHost;
use IGKEnvironment;
use IGKEvents;
use IGKException;
use ReflectionException;
use ReflectionFunction;
use function igk_resources_gets as __;

require_once IGK_LIB_CLASSES_DIR . '/System/IInjectedArgHost.php';
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
 * @method static \IGK\IQueryResult db_query(string $query) macros function
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
 * @method static mixed getDataTableDefinition(?string $tablename=null) macros function
 * @method static void getEnvKey() macros function
 * @method static mixed getEnvParam() macros function
 * @method static void getEnvParamKey() macros function
 * @method static void getInitDbConstraintKey() macros function
 * @method static bool getIsVisible() macros function
 * @method static void getRouteUri() macros functiong
 * @method static void getTestClassesDir() macros function
 * @method static ?\IGK\System\Database\IUserProfile getUser() macros function user model
 * @method static ?\IGK\System\Database\IUserProfile getUserProfile() macros get use profile 
 * @method static array getViewArgs() macros function
 * @method static string hookName() macros function get hook name
 * @method static void initDbConstantFiles() macros function
 * @method static void initDbFromFunctions() macros function
 * @method static void initDbFromSchemas() macros function
 * @method static void libdir() macros function
 * @method static object|\IGK\Database\DbSchemaLoadEntriesFromSchemaInfo loadDataAndNewEntriesFromSchemas() macros function load data and update the datable with entries
 * @method static mixed|\IGK\Database\IDbSchemaInfo loadDataFromSchemas() macros function load data from schema file. do not modify the database
 * @method static bool login(mixed $user, ?string $password, bool $nav=true, bool $rememberme=false) macros function. try login with the user
 * ## params
 *      - `$user` : user login or object info that describe a use connection
 *      - `$password`: if $user is a string(user's login) this will be an user's password.
 *      - `nav`: true to force redirection on success
 *      - `rememberme`: true to store connexions - cookies 
 * 
 * @method static void logout() macros function
 * @method static void migrate() macros function
 * @method static ?\IGK\Models\ModelBase model(string $modelName) macros function search for model by name. 
 *  - `modelName`: name of the model
 * @method static object|null modelUtility() macros function 
 * @method static void notifyKey() macros function
 * @method static string ns(string $path) macros function
 * @method static static register_autoload() macros function register macros function
 * @method static ?string resolveClass(string $path) macros function resolve class. return null if not exists
 * @method static void resolveAssets(array<string> $asset_list) macros function resolve class. return null if not exists * 
 * @method ?string asset(string $path, bool $must_exist=true) macros function resolve controller assets  * 
 * @method static string resolveTableName(string $real_table_name) macros function resolve to entry table
 * @method static void seed() macros function
 * @method static void setEnvParam($key, $value) macros function
 * @method static void storeConfigSettings() macros function
 * @method static string uri(?string $path, ?bool $full=true, ?bool $force_app_access=false) macros function 
 * @method static string loadMigrationFile() macros function 
 * @method bool checkUser(bool $redirect, ?string $redirectUri=null ) macros function check if user or navigate
 * @method static string getErrorViewFile(int $code) macros function get controller error file
 * @method static mixed getConfig(string $name, mixed $default=null) macros function get config setting
 * @method static mixed js(string $name, $default=null) macros function load inline js script
 * @method static mixed pcss(string $name, $default=null) macros function load temp inline pcss
 * @method static mixed getViews(bool $withHiddenFile, bool $recursive=false) macros function load temp inline pcss
 * @method static mixed getActionHandler(string $name, ActionResolutionInfo $action_resolution, ?array $params =null) macros function load temp inline pcss
 * @method static array getCachedDataTableDefinition() macros function get cached datable table definitions 
 */
/**
 * auto generate doc.
 * @package IGK\Controllers
 */
abstract class BaseController extends RootControllerBase implements IDataController, IInjectedArgHost
{
    /**
     * Constant: childs flag.
     * @var mixed
     */
    const CHILDS_FLAG = 5;
    /**
     * Constant: current view.
     * @var mixed
     */
    const CURRENT_VIEW = IGK_CURRENT_CTRL_VIEW;
    /**
     * Constant: env param user settings.
     * @var mixed
     */
    const ENV_PARAM_USER_SETTINGS = 0x200;
    /**
     * Constant: igk env param langchange key.
     * @var mixed
     */
    const IGK_ENV_PARAM_LANGCHANGE_KEY = "langchanged";
    /**
     * Constant: igk env param setup lang.
     * @var mixed
     */
    const IGK_ENV_PARAM_SETUP_LANG = "ctrl://setup_lang_in_request";
    /**
     * Constant: main view.
     * @var mixed
     */
    const MAIN_VIEW = 9;
    /**
     * Constant: page view flag.
     * @var mixed
     */
    const PAGE_VIEW_FLAG = 4;
    /**
     * Constant: params flag.
     * @var mixed
     */
    const PARAMS_FLAG = 7;
    /**
     * Constant: reg view child.
     * @var mixed
     */
    const REG_VIEW_CHILD = 11;
    /**
     * Constant: show child.
     * @var mixed
     */
    const SHOW_CHILD = 10;
    /**
     * Constant: viewchilds flag.
     * @var mixed
     */
    const VIEWCHILDS_FLAG = 6;
    /**
     * Constant: visibility flag.
     * @var mixed
     */
    const VISIBILITY_FLAG = 2;
    /**
     * Constant: webparent flag.
     * @var mixed
     */
    const WEBPARENT_FLAG = 1;
    // + | activate this to disable action handling
    /**
     * Constant: no action flag.
     * @var mixed
     */
    const NO_ACTION_FLAG = 11;
    /**
     * Constant: view args.
     * @var mixed
     */
    const VIEW_ARGS = IGK_VIEW_ARGS;
    /**
     * constant: view options
     */
    const VIEW_OPTION = IGK_VIEW_OPTIONS;
    /**
     * Constant: view extra args.
     * @var mixed
     */
    const VIEW_EXTRA_ARGS = IGK_VIEW_EXTRA_ARGS;
    /**
     * auto generate doc.
     * @var mixed
     */
    private static $sm_sysController = [];
    /**
     * auto generate doc.
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
    /**
     * call handle view file - action 
     */
    protected function _renderViewFile()
    {
        // + | --------------------------------------------------------------------
        // + | core view - action - treatment
        // + |  
        $ctrl = $this;
        /**
         * @var mixed 
         */
        $params = null;
        $find = false;
        $allowed_view = true;
        list(
            $no_auto_response_on_missing_view,
            $view_protect_request_function_name
        ) = igk_extract(
            $ctrl->getConfigs(),
            'no_auto_response_on_missing_view|view_protect_request_function_name'
        );
        extract($ctrl->getViewArgs());
        $v_handle = false;
        $f = "";
        $v = $this->getCurrentView() ?? igk_die("current view is null. " . get_class($this));
        $c = strtolower(igk_getr("c", ""));
        if (is_file($v)) {
            $f = $v;
        }
        if ($c == strtolower($this->getName())) {
            // + | override the views
            $v = igk_getr("v", $v);
        }
        if (empty($v)) {
            if (!$ctrl->getEnvParam(BaseController::IGK_ENV_PARAM_SETUP_LANG)) {
                igk_die('empty view not allowed');
            }
            $v = IGK_DEFAULT;
        }
        $meth = StringUtility::FuncName($v);
        if ($view_protect_request_function_name) {
            if (!preg_match('/^[a-zA-Z]/', $meth)) {
                array_unshift($params, $v);
                $meth = IGK_DEFAULT;
            }
        }
        $meth_exits = method_exists($this, $meth);
        $v_second_meth = false;
        if (($meth_exits && $this->IsFuncUriAvailable($meth)) || ($v_second_meth = (isset($params) && method_exists($this, $meth = IGK_DEFAULT_VIEW)))) {
            try {
                $v_handle = "method";
                $params = isset($params) ? $params : [];
                if ($v_second_meth) {
                    array_unshift($params, $v);
                }
                $arguments = $params;
                $arguments = Dispatcher::GetInjectArgs(new \ReflectionMethod($this, $meth), $params, $this);
                return call_user_func_array(array($this, $meth), $arguments);
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
            !igk_io_file_exists($f = ($this->getViewFile($v, 0, $params)), true)
        ) {
            // + | resolved but file is not present - try handle action 
            $ffname = IGK_DEFAULT_VIEW;
            if ($params) {
                $cc =  igk_getv($params, 0);
                if ($cc == IGK_DEFAULT_VIEW) {
                    array_shift($params);
                } else {
                }
            }
            $this->setEnvParam(self::VIEW_ARGS, $params);
            $handle_response = $this->handleAction($ffname, $params, $action_handler);
            if (!$no_auto_response_on_missing_view && igk_do_response($handle_response)) {
                return;
            }
            $v_handle = "missing:file";
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
        if ($allowed_view && igk_io_file_exists($f, true)) {
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
     * @param ?string $fname
     * @return IViewLayoutLoader
     */
    protected function createViewLoader(?string $fname = null): ?IViewLayoutLoader
    {
        $ctrl = ViewHelper::CurrentCtrl();
        if ($ctrl === $this) {
            $n = $fname ?? ViewHelper::GetViewArgs("fname");
            if ($n) {
                $n = ViewHelper::TreatViewNameForClassDefinition($n);
                $p = sprintf(EntryClassResolution::WinUI_ViewLayoutFormat, ucfirst($n)); 
                if (($cl = $this->resolveClass($p)) && is_subclass_of($cl, IViewLayoutLoader::class)) {
                    return new $cl($this);
                }
            }
        }
        return new ViewLayoutLoader($this);
    }
    /**
     * get the view loader
     * @param ?string $fname
     * @throws IGKException
     * @throws ArgumentTypeNotValidException
     * @throws ReflectionException
     * @return null|IViewLayoutLoader
     */
    protected function getViewLoader(?string $fname = null)
    {
        if ($l = $this->getEnvParam(ControllerEnvParams::ViewLoader)) {
            return $l;
        }
        $l = $this->createViewLoader($fname);
        !$l && igk_die("failed to create view loader");
        $this->setEnvParam(ControllerEnvParams::ViewLoader, $l);
        return $l;
    }
    /**
     * handle action
     * @param string $fname
     * @param array $params
     * @param mixed & $handler
     * @param mixed $is_ajx
     * @param mixed $is_view
     * @throws IGKException
     * @throws ArgumentTypeNotValidException
     * @throws ReflectionException
     * @return mixed
     */
    protected function handleAction(string $fname, array $params, &$handler = null, $is_ajx = null, $is_view = null)
    {
        $srv = igk_server();
        $is_ajx = $is_ajx ?? (($srv->CONTENT_TYPE == "application/json") || igk_is_ajx_demand());
        $is_view = $is_view ?? igk_getr('view') ?? Request::getInstance()->requestView();
        if (
            !$this->getEnvParam(self::NO_ACTION_FLAG) &&
            ($handler = $this->getActionHandler($fname, $rep = new ActionResolutionInfo, $params, $is_ajx))
        ) {
            $params = $rep->params ?? $params;
            $r = ActionHelper::DoHandle($this, $handler, $fname, $params, $rep, [
                'method' => $srv->REQUEST_METHOD,
                'is_ajx' => $is_ajx,
                'is_view' => $is_view
            ]);
            return $r;
        }
    }
    /**
     * Config entries.
     * @param mixed $fname
     */
    protected function _config_entries($fname)
    {
        $conf = $this->configFile(ConfigFiles::views);
        $redirect_request = null;
        if (igk_io_file_exists($conf, true)) {
            /**
             * @var mixed
             */
            $inc = function () {
                return include(func_get_arg(0));
            };
            $conf = $inc($conf);
            $def = Activator::CreateNewInstance(static::viewConfigClass(), $conf);
            $entry = $def->default_dir_entry;
            if ($entry == $fname) {
                if (Request::getInstance()->method('GET')) {
                    ViewHelper::ForceDirEntry($this, $fname, $redirect_request);
                }
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
    protected function _include_func_helpers()
    {
        include_once(IGK_LIB_DIR . "/Lib/functions-helpers/view.php");
    }
    /**
     * copy this fonction to allow file inclusion on the current context controller
     * @param string $file
     */
    protected final function _include_view(string $file)
    { 
        $response = null;
        $this->_include_func_helpers();
        $this->_include_constants();
        igk_reset_globalvars();
        $viewargs = (array)ViewEnvironmentArgs::CreateContextViewArgument($this, $file, __FUNCTION__);
        igk_set_env(IGKEnvironment::CURRENT_CTRL, $this);
        if (empty($viewargs['layout']))
            $viewargs['layout'] = $this->getViewLoader(($viewargs['fname']));
        igk_set_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS, $viewargs);
        extract($viewargs);
        igk_hook(IGKEvents::HOOK_INIT_INC_VIEW, [
            'ctrl' => $this,
            'file' => $file
        ]);
        // + | --------------------------------------------------------------------
        // + | action  and action_handler object
        // + |
        $action = $action_handler = null;
        try {
            // + | binding environment 
            $this->_config_entries($fname);
            try {
                $middle = $this->configFile(ConfigFiles::middlewares);
                if ($middle && igk_io_cache_file_exists($middle)) {
                    $cm = include($middle);
                    if ($fc = igk_getv($cm, '/' . $fname)) {
                        if (!$fc($fname, $this)) {
                            throw new \IGKException('not allowed', 500);
                        }
                    }
                }
                $handle_response = $this->handleAction($fname, $params, $action_handler);

                $i = igk_environment()->action_handler_instance;
                if ($handle_response && igk_sys_support_trait($i, ApiActionTrait::class)) {
                    igk_exit();
                }
                if ($i && ($redirect = $i->redirect ?? igk_getr('redirect'))) {
                    igk_navto($redirect);
                }
                $action = $i;
            } catch (\Exception $ex) {
                // + | handler failed or throw an exception. 
                // + | method no present
                if (!igk_io_cache_file_exists($file)) {
                    if (igk_environment()->isDev()) {
                        igk_set_header(500);
                        Logger::danger("[BLF] - error - " . $ex->getMessage());
                        ExceptionUtils::ShowException($ex);
                        igk_exit();
                    }
                    igk_ilog("/!\\ Action Handler failed ::" . $ex->getMessage(), null, 0, false);
                }
                $viewargs['error'] = $ex->getMessage();
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
            $viewargs['action'] = $action;
            igk_set_env(IGKEnvironment::CTRL_CONTEXT_VIEW_ARGS, $viewargs);
            ob_start();
            $bckdir = set_include_path(dirname($file) . PATH_SEPARATOR . get_include_path());
            igk_environment()->viewfile = 1;
            igk_set_env('igk_view_handle_actions', null);
            $response = $layout->include($file, $viewargs);
            $g = igk_get_env('igk_view_handle_actions');
            if (($tg = igk_view_handle_missing_params()) && ($params = igk_getv($tg, 'params'))) {
                $this::viewError($tg['code'], igk_getv($tg, 'params'));
            }
            igk_environment()->viewfile = null;
            set_include_path($bckdir);
            $out = ob_get_contents();
            if (($level = ob_get_level()) == 0) {
                igk_dev_wln_e(__FILE__ . ":" . __LINE__,  "missing.... level ", $level, $file);
            }
            ob_end_clean();
            if (!empty($out)) {
                $t->addSingleNodeViewer(IGK_HTML_NOTAG_ELEMENT)->setContent($out);
            }
            if ($this->getEnvParam(ControllerParams::REPLACE_URI)) {
                // + | replace to entry uri if not a default controller 
                $uri = '';
                if (!igk_ctrl_is_default_controller($this)) {
                    $s = $fname;
                    if (basename($s) == IGK_DEFAULT_VIEW) {
                        $s = dirname($s);
                    }
                    $uri = $s == '.' ? null : $uri;
                }
                $g = $this->getAppUri($uri);
                if ($uri && ($g != igk_io_baseuri($uri))) {
                    $t->replace_uri($g);
                }
            }
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
    /**
     * Get view data args.
     */
    protected function _getViewDataArgs()
    {
        $rep = $this->getEnvParam(ControllerEnvParams::ActionViewResponse);
        $cp = [];
        if (!is_bool($rep)) {
            $cp = $rep ?? [];
        }
        return new ViewDataArgs($cp);
    }
    /**
     * include constant
     */
    protected function _include_constants()
    {
        if (($f = $this->getConstantFile()) && igk_io_file_exists($f, true))
            include_once($f);
        if (($f = $this->getDbConstantFile()) && igk_io_file_exists($f, true))
            include_once($f);
        unset($f);
    }
    /**
     * auto generate doc.
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
    /**
     * auto generate doc.
     * @return string default name attached to this controller
     */
    public function getName(): string
    {
        return strtolower(get_class($this));
    }
    /**
     * get store parameter
     * @param mixed $key
     * @param mixed $default
     * @param mixed $register
     * @return mixed objet reference value
     */
    public function &getParam($key, $default = null, $register = false)
    {
        $param = &$this->getM_();
        $o = isset($param[$key]) ? $param[$key] : $default;
        return $o;
    }
    /**
     * get stored params keys
     * @return array stored params keys
     */
    public function getParamKeys()
    {
        return array_keys((array)$this->getParams());
    }
    /**
     * get all controller's parameters
     */
    public function getParams()
    {
        return $this->getM_();
    }
    /**
     * auto generate doc.
     */
    public function getDeclaredFileName()
    {
        $tab = &igk_environment()->createArray("reflect_info");
        $cl = get_class($this);
        if ($c = igk_getv($tab, $cl)) {
            return $c->filename;
        }
        $h = igk_sys_reflect_class($cl);
        $c = (object)[
            "filename" => Path::LocalPath($h->getFileName())
        ];
        $tab[$cl] = $c;
        return $c->filename;
    }
    /**
     * auto generate doc.
     * @return string
     */
    public function getDeclaredDir(): string
    {
        return dirname($this->getDeclaredFileName());
    }
    /**
     * Returns Classes Dir.
     */
    public function getClassesDir()
    {
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER, IGK_CLASSES_FOLDER]);
    }
    /**
     * Returns Lib Dir.
     */
    public function getLibDir()
    {
        return implode("/", [$this->getDeclaredDir(), IGK_LIB_FOLDER]);
    }
    /**
     * get configs directory 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function getConfigsDir()
    {
        return Path::Combine($this->getDeclaredDir(), IGK_CONF_FOLDER);
    }
    /**
     * get view dir
     */
    public function getViewDir()
    {
        return ControllerPaths::Gets($this)->viewDir;
    }
    /**
     * auto generate doc.
     */
    public function getStylesDir()
    {
        return ControllerPaths::Gets($this)->stylesDir;
    }
    /**
     * get style dir
     */
    protected function getPrimaryCssFile()
    {
        if ($prima_file = $this->getConfig("PrimaryStyle", Constants::DEFAULT_THEME_STYLE)) {
            return Path::Combine($this->getStylesDir(), $prima_file);
        }
        return implode("/", [$this->getStylesDir(), Constants::DEFAULT_THEME_STYLE]);
    }
    /**
     * auto generate doc.
     */
    protected function getConfigFile()
    {
        return $this->getDataDir() . "/" . IGK_CTRL_CONF_FILE;
    }
    /**
     * auto generate doc.
     */
    public function getDataDir()
    {
        return $this->getDeclaredDir() . "/" . IGK_DATA_FOLDER;
    }
    /**
     * get the constant file
     */
    public function getConstantFile()
    {
        return $this->getDeclaredDir() . "/.constants.php.inc";
    }
    /**
     * Db constant utility
     */
    public function getDbConstantFile()
    {
        return $this->getDeclaredDir() . "/.db.constants.php";
    }
    /**
     * auto generate doc.
     */
    public function getResourcesDir()
    {
        return $this->getDataDir() . "/" . IGK_RES_FOLDER;
    }
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
        $c->initConfigSetting($this->_loadCtrlConfig());
        $tab[$cf] = $c;
        igk_environment()->set($key,  $tab);
        return $c;
    }
    /**
     * auto generate doc.
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
    /**
     * utility view args
     * @param string $fname
     * @param ?string $file
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
    /**
     * auto generate doc.
     */
    public function getContentDir()
    {
        return igk_dir($this->getDeclaredDir() . DIRECTORY_SEPARATOR . IGK_CONTENT_FOLDER);
    }
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
    /**
     * resolve view files and update parameters
     * @param string $view extension
     * @param string $checkfile _exist
     * @param mixed & $param
     * @param mixed $ajx_demand
     * @return string view file path
     */
    public function getViewFile(string $view, $checkfile = 1, &$param = null, $ajx_demand = null)
    {
        $detect = function ($f, $d, $exts) {
            while (count($exts) > 0) {
                $ex = array_shift($exts);
                if (is_file($cf = Path::Combine($f, $d . '.' . $ex))) {
                    return $cf;
                }
            }
            return false;
        };
        $extension = IGK_DEFAULT_VIEW_EXT;
        $_viewdir = $this->getViewDir();
        $ajx_demand = $ajx_demand ?? igk_is_ajx_demand();
        if ($e = igk_getv(array_slice(func_get_args(), 3), 0))
            $extension = $e;
        $exts = [
            $extension
        ];
        if ($ajx_demand) {
            array_unshift($exts, "ajx." . $extension);
        }
        if ($param === null) {
            $param = [];
        } else if (!is_array($param)) {
            $param = [$param];
        }
        if (empty($view))
            $view = IGK_DEFAULT_VIEW;
        else if ($rp = realpath($view)) {
            // + check that the file depend on controller 
            if (strpos($rp, realpath($_viewdir)) === 0) {
                return $view;
            }
        }
        $f = igk_uri(rtrim(Path::Combine($_viewdir, $view), '/'));
        // + | igk_wln_e(__FILE__.":".__LINE__ ,  $view, $exts);
        // + | get fname to UNIX PATH
        $f = IO::GetUnixPath("/" . $view, false, $_viewdir) ?? $f;
        $v_path_resolv = [];
        if (is_dir($f)) {
            // + | is ajx file detection or not 
            $pl = null;
            while (count($param) > 0) {
                $d = $param[0];
                if ($cf = $detect($f, $d, $exts)) {
                    $f = $cf;
                    array_shift($param);
                    return $f;
                } else {
                    if (is_dir($dd =  Path::Combine($f, $d))) {
                        $f = $dd;
                        $pl = array_shift($param);
                        $v_path_resolv[] = $pl;
                        continue;
                    }
                    break;
                }
            }
            if ($cf = FileHandler::ResolveFile($f, 'default', FileHandler::FILE_CONTEXT_VIEW)) {
                return $cf;
            }
            // + | window allow dir and file with the same name
            if (is_file($cf = $detect($f, IGK_DEFAULT, $exts))) {
                return $cf;
            } else {
                // + | add extension
                $tf = $f . "." . $extension;
                if (is_file($tf)) {
                    return $tf;
                }
                if ($v_path_resolv) {
                    $view .= '/' . implode('/', $v_path_resolv);
                }
            }
        }
        $v_cf = ViewHelper::ResolveViewFile($_viewdir, $view, $f, $checkfile, $param);
        return $v_cf;
    }
    /**
     * auto generate doc.
     * @param mixed $path
     */
    public function getCtrlFile($path)
    {
        if (Path::getInstance()->realpath($path) == $path)
            return $path;
        return igk_dir(dirname($this->getDeclaredFileName()) . DIRECTORY_SEPARATOR . $path);
    }
    /**
     * auto generate doc.
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
    }
    /**
     * get the flag value
     * @param mixed $code
     * @param mixed $default
     */
    public function getFlag($code, $default = null)
    {
        return $this->getM_()->getFlag($code, $default);
    }
    /**
     * auto generate doc.
     */
    public function getCurrentView()
    {
        return $this->getEnvParam(self::CURRENT_VIEW, IGK_DEFAULT_VIEW);
    }
    /**
     * set controller current view
     * @param mixed $view 
     * @param bool $reload force reload if $view is the same
     * @param mixed $targetNode passed target node
     * @param mixed $args argument to attach to view 
     * @param mixed $options extra option
     * @return mixed
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
                $this->setTargetNode($bck);
        }
    }
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
    /**
     * init target node 
     */
    protected function initTargetNode(): ?HtmlNode
    {
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
    /**
     * invoke view logic. \
     * override this method to customize your view logic.
     * @return self
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
     * @return void|array 
     */
    protected function _initRequiredModules()
    {
        $v_key = ApplicationModuleHelper::SYS_ENV_KEY;
        $v_modules = igk_get_env($v_key) ?? [];
        $v_cl = get_class($this);
        if (isset($v_modules[$v_cl])) {
            return;
        }
        $load = 1;
        $config_file = Path::Combine($this->getDeclaredDir(), Constants::PROJECT_CONF_FILE);
        if ($data = json_decode(file_get_contents($config_file))) {
            $required = (array)igk_conf_get($data, 'required');
            $required && ApplicationModuleHelper::ImportRequiredModule($required, $this);
            if ($required) {
                $load = $required;
            }
            if ($project = (array)igk_conf_get($data, 'dependOn')) {
                $this->_initCheckRequireProject($project);
            }
        }
        $v_modules[$v_cl] = $load;
        igk_set_env($v_key, $v_modules);
        return $data;
    }
    /**
     * Init check require project.
     * @param mixed $project
     */
    protected function _initCheckRequireProject($project)
    {
        if (!$project) {
            return;
        }
        while (count($project)) {
            $q = array_shift($project);
            if (!($c = igk_getctrl($q, false))) {
                igk_die('missing : ' . $q);
            }
        }
    }
    /**
     * get global project configuration settings
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    protected function _globalConfigSettings()
    {
        $g = self::IsSysController($this);
        if (!$g) {
            $config_file = Path::Combine($this->getDeclaredDir(), Constants::PROJECT_CONF_FILE);
            if ($data = json_decode(file_get_contents($config_file))) {
                return $data;
            }
        }
        return null;
    }
    /**
     * Create view env args.
     */
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
        $view_env_arg->modules = new ViewModuleHelper(igk_environment()->require_modules());
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
        if (igk_environment()->is('development') || igk_count($_REQUEST) > 0) {
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
        igk_hook(IGKEvents::HOOK_INIT_VIEW, ['ctrl' => $this]);
    }
    /**
     * set environment param flags the flag
     * @param mixed $code
     * @param mixed $value
     */
    public function setFlag($code, $value)
    {
        $this->setEnvParam($code, $value);
    }
    /**
     * reset the value of the current view
     * @param mixed $view
     */
    protected function resetCurrentView($view = null)
    {
        $this->setParam(self::CURRENT_VIEW, $view);
    }
    /**
     * check if this controller class is a system controller
     * @param object|string $className of a controller
     */
    public static function IsSysController($className)
    {
        if (is_object($className) && ($className instanceof BaseController)) {
            $f = realpath($className->getDeclaredFileName());
            return igk_str_startwith($f, IGK_LIB_DIR);
        }
        return (igk_getv(self::$sm_sysController, $className) != null);
    }
    /**
     * auto generate doc.
     * @param string $view
     * @param mixed $target
     * @param mixed $forcecreation
     * @param mixed $args the default value is null
     */
    public function getViewContent(string $view, $target, $forcecreation = false, $args = null)
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
    /**
     * set the controller parameters
     * @param mixed $key
     * @param mixed $value
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
    /**
     * call view layout without changing current view
     * @param mixed $view
     * @param mixed $forcecreation
     * @param mixed $args
     * @param mixed $options
     */
    public function getView($view = null, $forcecreation = false, $args = null, $options = null)
    {
        extract($this->getSystemVars());
        $v = igk_dir($view != null ? $view : igk_getr("v", $view));
        $f = igk_realpath($v) === $v ? $v : $this->getViewFile($v);
        $this->regSystemVars(null);
        if (igk_io_file_exists($f, true) || ($forcecreation && igk_io_save_file_as_utf8($f, IGK_STR_EMPTY))) {
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
    /**
     * auto generate doc.
     */
    public function getCurrentPageFolder()
    {
        return igk_app()->getCurrentPageFolder();
    }
    use ControllerUriTrait;
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
    /**
     * include view on contex
     * @param string $view
     * @param mixed $args
     */
    protected function _include_view_file(string $view, $args = null)
    {
        $v_file = igk_io_cache_file_exists($view) ? $view : $this->getViewFile($view);
        if (igk_io_cache_file_exists($v_file) === true) {
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
    /**
     * get default data adapter name
     */
    public function getDataAdapterName(): string
    {
        return igk_sys_getconfig("default_dataadapter", IGK_MYSQL_DATAADAPTER);
    }
    /**
     * auto generate doc.     
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
        return null;
    }
    /**
     * auto generate doc.
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
    /**
     * auto generate doc.
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
    /**
     * Sets Target Node.
     * @param mixed $node
     */
    public function setTargetNode($node)
    {
        $this->setEnvParam(IGK_CTRL_TG_NODE, $node);
        return $this;
    }
}
