<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKEvents.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\Actions\Dispatcher;
use IGK\HookOptions;
use IGK\IHookOptions;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Security\Authentications\Traits\UserCommunityAuthenticationTrait;

require_once IGK_LIB_CLASSES_DIR.'/System/Security/Authentications/Traits/UserCommunityAuthenticationTrait.php';
/**
 * represent app - system - controller - public hook
 * Hooking system 
 * each callback must have 
 * - args that hold argument for the hook
 * - handle to inform that and callback already handle the hooking system
 * - $ref* to pass reference object hook object of reference value
 * - output the global event output
 */
class IGKEvents extends IGKObject
{
    use UserCommunityAuthenticationTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    const ON_BEFORE_EXIT = "sys://event/onbeforeexit";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_SESS_START = "sys_session_start";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_SYS_INIT_CONFIG = 'sys://init_config';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_PREPROCESS_COMMAND_LINE = 'sys://cli/preprocess-command-line';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_INIT_WEB_APP_LIBRARY = 'sys://webapplication/init_library';
    /**
     * reset uset authentications 
     */
    const HOOK_USER_RESET_AUTH = 'sys://user/reset_auth';
    // + | --------------------------------------------------------------------
    // + | Application constant 
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_APP_SHUTDOWN = 'app_shutdown';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_APP_PRESENTATION = 0xa01;

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_APP_BOOT = "sys://app_boot";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_APP_SETTING_RESET = "app_setting_reset";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_APP_CLEAN_CACHE = 'sys://cache/clear';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_LANG_CHANGED = 'sys://lang/changed';
    // + | --------------------------------------------------------------------
    // + | command event constant : 
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_COMMAND = 'sys_commnand';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_INIT_APP = "sys://hook/init_app";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_BEFORE_INIT_APP = "sys://hook/before_init_app";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_AFTER_INIT_APP = "sys://hook/after_init_app";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CACHE_RES_CREATED = "CacheResourceCreated";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CSS_REG = "css_class_reg";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_TERMINATE = "sys_.terminate";
    // + | --------------------------------------------------------------------
    // + | DB QUERY
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_DATA_ENTRY = "db_dataentry";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_INIT_START = "db_init_start";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_INIT_COMPLETE = "db_init_complete";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_INIT_ENTRIES = "db_init_entries";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_TABLECREATED = "db_table_created";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_POST_GROUP = "db_post_db_create_database_list";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_CACHES_INITIALIZED = "db_cache_initialized";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_INSERT = 'db_data_inserted';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_BEFORE_RENDER_DOC = "html_before_render_doc";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_AFTER_RENDER_BODY = "html_after_render_body";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_BODY = "html_body";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_FOOTER = "html_footer";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_HEAD = "html_head";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_META = "html_meta";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_PRE_FILTER_ATTRIBUTE = "html_prefilter_attribute";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_HTML_LOADING_CONTEXT_REGISTER = 'html_context_register';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_PAGEFOLDER_CHANGED = "sys_pagefolder";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_SCRIPTS = "html_load_scripts";
    // + | --------------------------------------------------------------------
    // + | USER MANAGEMENT HOOK
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_ADDED = "sys_user_added";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_EXISTS = "sys_user_exists";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_LOGIN = "sys_user_login";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_LOGOUT = "sys_user_logout";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_ACTIVATED = "sys_user_status_changed";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_DELETE = "sys_user_delete"; // just make it inactive

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_DROP = "sys_user_drop"; // full user removed

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_CLEAN = 'sys_user_clean'; // clean user data for database

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_FIND_USER = 'sys_user_find_user_by_value';
    // + | --------------------------------------------------------------------
    // + | DB HOOK
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_START_DROP_TABLE = 'sys://db/startdroptable';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_RENAME_COLUMN = 'sys://db/rename_column';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DB_MIGRATE = 'sys://db/migrate'; // event: ['ctrl'=>$ctrl,'type'=>'init', 'data'=>$r]

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_ACTION_WILL_DO_ACTION = 'sys://action/willDoAction';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_ACTION_DO_ACTION = 'sys://action/doAction';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_ACTION_DID_ACTION = 'sys://action/didDoAction';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_MK_LINK = "generateLink";

    /**
    * auto generate doc.
    * @var mixed
    */
    const USER_PWD_CHANGED = "user pwd changed";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_MODEL_INIT = "db_init_model_macros";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_AUTLOAD_CLASS = "sys_autoload_class";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_VIEW_MODE_CHANGED = "config_view_mode_changed";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CONFIG_CTRL = "config_get_configs";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DEBUGGER_VIEW = "debugger_view";
    // + | --------------------------------------------------------------------
    // + | CONTROLLER HOOK
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CONTROLLER_INIT_COMPLETE = "on_controller_init_complete";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CONTROLER_LOADED = 'on_controller_loaded';
    // + | --------------------------------------------------------------------
    // + | VIEW HOOK
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_INIT_VIEW =  'on_init_view';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_INIT_INC_VIEW = 'on_init_inc_view';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_FORCE_VIEW = "doc_force_view";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_AJX_END_RESPONSE = "ajx_end_reponse";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_LOG_APPEND = "sys_log_append_msg";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_INSTALL_SITE = "sys_install_site";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_INIT_GLOBAL_MATERIAL_FILTER = "sys_init_gobal_material_filter";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_MAIL_REGISTER = "sys_hook_mail_register";

    /**
    * auto generate doc.
    * @var mixed
    */
    const P_SUBDOMAIN_PRIORITY = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    const P_SESSION_PRIORITY = 100;

    /**
    * auto generate doc.
    * @return IHookOptions
    */
    public static function CreateHookOptions(): IHookOptions
    {
        return new HookOptions();
    }
    /**
     * css context bind controller styles sheet on init
     */
    const HOOK_BIND_CTRL_CSS = "css_bind_ctrl_style";

    /**
    * auto generate doc.
    * @var mixed
    */
    const ENV_KEY = "sys://hooks";

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_DOM_PROPERTY_CHANGED = "dom_property_changed";
    /**
     * filter node . update property or replace with output response.
     */
    const FILTER_CREATED_NODE = "post_filter_node";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILTER_DB_SCHEMA_INFO = "filter_db_schema_info";
    /**
     * filter node creation
     */
    const FILTER_PRE_CREATE_ELEMENT = "pre_filter_node";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILTER_POST_CREATE_ELEMENT = "post_filter_node";

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILTER_CONFIG_MENU = 'filter_config_menu';

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILTER_AUTH_TYPE = 'filter_user_auth_type';
    /**
     * build command event
     */
    const BUILD_ASSETS = 'build_assets';
    /**
     * command Hooks
     */
    const COMMAND_HELP_OPTIONS_HOOK = 'command:help/options';

    /**
    * auto generate doc.
    * @var mixed
    */
    const COMMAND_HELP_HOOK = 'command:help';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_MIDDLEWARE_ACTION = 'MiddleWareAction';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CHECK_MIDDLEWARE_ACCESS_TOKEN = 'MiddleWareAction:/CheckAccessToken';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_ON_MODULE_ADDED = 'command:/module/added';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_USER_AUTHENTICATE = 'sys:/user/authenticate';

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_AUTH_USER_BY_COMMUNITY = 'authentication://community-request';

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILTER_LIST_AUTH_TYPE = 'filter://authentication_type';

    /**
     * raise when a new document created
     */
    const HOOK_NEW_DOC_CREATED = 'sys://document_created';

    // + | --------------------------------------------------------------------
    // + | winui menu hook
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_WINUI_SETTING_MENU = 'sys://user/settings/menu';


    // + | --------------------------------------------------------------------
    // + | module hook
    // + |

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_MODULE_DID_INIT_MODULE = 'sys://module/didInitModule';

    /**
    * auto generate doc.
    * @var mixed
    */
    const VIEWCOMPLETE = 0x1;

    /**
    * auto generate doc.
    * @var mixed
    */
    const HOOK_CRUNJOB = 'on_do_cronjob';

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_methods;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_name;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_owner;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_singlemethod;
    /**
     * register hook callback
     * @param string $hookKey 
     * @param mixed $callback 
     * @return void 
     */

    public static function UnregComplete(string $hookKey, $callback)
    {
        $m = function ($e) use ($callback, &$m, $hookKey) {
            if ($callback($e) === false) return;
            igk_unreg_hook($hookKey, $m);
        };
        igk_reg_hook($hookKey, $m);
    }
    /**
     * 
     * @param mixed $owner
     * @param mixed $name
     * @param mixed $single the default value is false
     */

    public function __construct($owner, $name, $single = false)
    {
        $this->m_owner = $owner;
        $this->m_methods = array();
        $this->m_singlemethod = $single;
        $this->m_name = $name;
    }
    /**
     * display value
     */

    public function __toString()
    {
        return __CLASS__ . "[" . $this->m_name . ";for[" . get_class($this->m_owner) . "]]";
    }
    /**
     * register a class method to this
     * @param  mixed $class or callable
     * @param mixed $method if class method is a name
     */

    public function add($class, $method = null)
    {
        if ($this->m_singlemethod) {
            if ($this->getCount() >= 1) {
                $this->Clear();
            }
        }
        $_info = null;
        $_info = IGKAppMethod::Create($class, $method, $this);
        if ($_info) {
            if (!$_info->IsRegistered($this->m_methods, $this)) {
                $this->m_methods[] = $_info;
                $_info->setParentEvent($this);
                return $_info;
            } else {
                return null;
            }
        } else {
            igk_die("can't add event info is null.[== " . $this->m_name . " " . $method);
        }
        return null;
    }
    /**
     * invoke resgistrated method
     */

    public function Call($sender, $args)
    {
        if ($this->m_methods) {
            foreach ($this->m_methods as $v) {
                $v->Invoke($sender, $args);
            }
        }
    }
    /**
     * 
     */

    public function Clear()
    {
        $this->m_methods = array();
    }
    /**
     * enumerate registrated methods
     */

    public function enumerateMethod($callback)
    {
        foreach ($this->m_methods as $k) {
            $callback($k);
        }
    }
    ///get the number of method in this events
    /**
     */

    public function getCount()
    {
        return count($this->m_methods);
    }
    /**
     * 
     */

    public function getInfo()
    {
        return $this->__toString() . " count # " . igk_count($this->m_methods);
    }
    /**
     * 
     */

    public function getIsDebugging()
    {
        return igk_get_env("sys://event/isdebugging/" . $this->m_name, 0);
    }
    /**
     * 
     */

    public function getMethodCount()
    {
        return igk_count($this->m_methods);
    }
    /**
     * 
     */

    public function getName()
    {
        return $this->m_name;
    }
    /**
     * 
     */

    public function getOwner()
    {
        return $this->m_owner;
    }
    /**
     * 
     * @param mixed $class
     * @param mixed $method
     */

    public function remove($class, $method)
    {
        for ($i = 0; $i < count($this->m_methods); $i++) {
            $k = $this->m_methods[$i];
            if ($k->match($class, $method)) {
                $meth = $this->m_methods[$i];
                unset($this->m_methods[$i]);
                $this->m_methods = array_values($this->m_methods);
                $k->setParentEvent(null);
                return 1;
            }
        }
        return 0;
    }
    /**
     * 
     * @param mixed $obj
     * @param mixed $name the default value is IGK_FUNC_KEY
     */

    public function removeObject($obj, $name = IGK_FUNC_KEY)
    {
        $tab = array();
        $r = 0;
        for ($i = 0; $i < count($this->m_methods); $i++) {
            $meth = $this->m_methods[$i];
            if ($meth->matchParam($name, $obj)) {
                $r = 1;
                $meth->setParentEvent(null);
                continue;
            }
            $tab[] = $meth;
        }
        $this->m_methods = $tab;
        return $r;
    }
    /**
     * 
     * @param mixed $v
     */

    public function setIsDebugging($v) {}
    /**
     * register hooks
     * @param mixed $name 
     * @param callable|string|array $callback 
     * @param int $priority 
     * @return void 
     */

    public static function reg_hook(string $name, $callback, $priority = 10, $injectable = true)
    {
        $hooks = &igk_environment()->createArray(self::ENV_KEY);
        if (!isset($hooks[$name])) {
            $hooks[$name] = (object)array("list" => array(), "changed" => 1);
        }
        $hooks[$name]->list[] = (object)array(
            "priority" => $priority,
            "callback" => $callback,
            "injectable" => $injectable,
        );
        $hooks[$name]->changed = 1;
    }
    /**
     * raise hook
     * @param string $name 
     * @param array $args 
     * @param ?\IGK\IHookOptions|array|object $options require IHoopOptions to bybass option behaviour
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public static function hook(string $name, $args = array(), $options = null)
    {
        if (defined('IGK_DEBUG_HOOK') && constant('IGK_DEBUG_HOOK')){
            igk_wln('hook: '.$name. ' '.session_id());
        }
        // + ----------------------------------------------------------------------
        // + | Default output 
        $def = null;
        if (!is_null($options) && !($options instanceof IHookOptions)) {
            $def = igk_get_robjs("default|output|type", 0, (object)$options);
        } else {
            $def = $options;
        }
        $hooks = igk_environment()->get(self::ENV_KEY);
        $tab = igk_getv($hooks, $name);
        if ($tab) {
            $list = &$tab->list;
            if ($tab->changed) {
                usort($list, function ($a, $b) {
                    if ($a->priority < $b->priority)
                        return -1;
                    if ($a->priority == $b->priority)
                        return 0;
                    return 1;
                });
                $tab->changed = 0;
            }
            $cargs = array((object)array(
                "args" => $args,
                "hook" => $name,
                "handle" => 0,
                "lastoutput" => null,
                "output" => $def ? $def->output : null
            ));
            $count = 0;
            $_invoke = function ($callback, $v, $cargs) {
                $tcargs = $cargs;
                if ($v->injectable) {
                    $fc = is_array($callback) ? Closure::fromCallable($callback) : $callback;
                    if (($fc instanceof \Closure) || is_string($fc)) { 
                        $tcargs = Dispatcher::GetInjectArgs(new \ReflectionFunction($fc), $cargs);
                    }
                }
                $cargs[0]->lastoutput = call_user_func_array($callback, $tcargs);
            };
            foreach ($list as $v) {
                if (!is_callable($v_c = $v->callback)) {
                    if (is_string($v_c)) {
                        $tab = explode('@', $v_c, 2);
                        if ($r = is_callable($tab)) {
                            $_invoke($tab, $v, $cargs);
                            continue; 
                        }
                    }
                    if (is_object($v->callback)) {
                        $cargs[0]->lastoutput = igk_invoke_callback_obj(null, $v->callback, $cargs);
                    } else {
                        igk_dev_wln_e(
                            __FILE__ . ':' . __LINE__,
                            " : not a callable ",
                            $name,
                            $v->callback
                        );
                        continue;
                    }
                } else {
                    $_invoke($v->callback, $v, $cargs); 
                }
                if ($cargs[0]->handle) {
                    break;
                }
                $count++;
            }
            return $cargs[0]->output;
        }
        return $def ? $def->output : $args;
    }
    /**
     * unregister hook
     * @param mixed $name 
     * @param mixed $callback 
     * @param bool $all 
     * @return int 
     */

    public static function unreg_hook($name, $callback, $all = true)
    {
        $hooks = &igk_environment()->createArray(self::ENV_KEY);
        if (!$hooks) {
            return 0;
        }
        if (is_null($callback)) {
            unset($hooks[$name]);            
            return true;
        }
        if (!isset($hooks[$name])) {
            return false;
        }        
        if (!isset($hooks[$name]->list)) {
            $hooks[$name]->list = [];
        }       
        $tb = &$hooks[$name]->list;
        if ($all) {
            $c = 0;
            $tb = array_filter(array_map(function ($v) use ($callback, &$c) {
                if ($v->callback === $callback) {
                    $c++;
                    return null;
                }
                return $v;
            }, $tb));
            return $c;
        }
        foreach ($tb as $k => $v) {
            if ($v->callback === $callback) {
                unset($tb[$k]);
                return 1;
            }
        }
        return 0;
    }


        /**
     * clear all hooks
     * @return void 
     */

    public static function ClearHooks(){
        $hooks = & igk_environment()->get(self::ENV_KEY);
        $hooks = [];
        unset($hooks);
        igk_environment()->set(self::ENV_KEY, null);
    }
}