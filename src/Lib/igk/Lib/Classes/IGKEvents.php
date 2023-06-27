<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKEvents.php
// @date: 20220803 13:48:54
// @desc: 


///<summary>represent a event method pointer</summary>

use IGK\Actions\Dispatcher;
use IGK\HookOptions;
use IGK\IHookOptions;
use IGK\System\Exceptions\ArgumentTypeNotValidException;

/**
 * represent app - system - controller - public hook
 */
class IGKEvents extends IGKObject
{ 

    const ON_BEFORE_EXIT = "sys://event/onbeforeexit";
    const HOOK_SESS_START = "sys_session_start";
    
    // + | --------------------------------------------------------------------
    // + | Application constant 
    // + |  
    const HOOK_APP_SHUTDOWN = 'app_shutdown'; 
    const HOOK_APP_PRESENTATION = 0xa01;
    const HOOK_APP_BOOT = "sys://app_boot";
    const HOOK_APP_SETTING_RESET = "app_setting_reset";

    // + | --------------------------------------------------------------------
    // + | command event constant : 
    // + |
    const HOOK_COMMAND = 'sys_commnand';
    


    const HOOK_INIT_APP = "init_app";
    const HOOK_BEFORE_INIT_APP = "before_init_app";
    const HOOK_AFTER_INIT_APP = "after_init_app";
    const HOOK_CACHE_RES_CREATED = "CacheResourceCreated";
    const HOOK_CSS_REG = "css_class_reg";
    // + | --------------------------------------------------------------------
    // + | DB QUERY
    // + |
    
    const HOOK_DB_DATA_ENTRY = "db_dataentry";
    const HOOK_DB_INIT_START = "db_init_start";
    const HOOK_DB_INIT_COMPLETE = "db_init_complete";
    const HOOK_DB_INIT_ENTRIES = "db_init_entries";
    const HOOK_DB_TABLECREATED = "db_table_created";
    const HOOK_DB_POST_GROUP = "db_post_db_create_database_list";
    const HOOK_DB_CACHES_INITIALIZED = "db_cache_initialized";
    const HOOK_DB_INSERT = 'db_date_inserted';

    const HOOK_HTML_BEFORE_RENDER_DOC="html_before_render_doc";
    const HOOK_HTML_AFTER_RENDER_BODY="html_after_render_body";
    const HOOK_HTML_BODY = "html_body";
    const HOOK_HTML_FOOTER = "html_footer";
    const HOOK_HTML_HEAD = "html_head";
    const HOOK_HTML_META = "html_meta";
    const HOOK_HTML_PRE_FILTER_ATTRIBUTE = "html_prefilter_attribute";
    
    const HOOK_PAGEFOLDER_CHANGED = "sys_pagefolder";
    const HOOK_SCRIPTS = "html_load_scripts";
    // + | --------------------------------------------------------------------
    // + | USER MANAGEMENT HOOK
    // + |
    
    const HOOK_USER_ADDED = "sys_user_added";
    const HOOK_USER_EXISTS = "sys_user_exists";
    const HOOK_USER_LOGIN = "sys_user_login";
    const HOOK_USER_LOGOUT = "sys_user_logout";
    const HOOK_USER_ACTIVATED = "sys_user_status_changed";
    const HOOK_USER_DELETE = "sys_user_delete";    
    // + | --------------------------------------------------------------------
    // + | DB HOOK
    // + |
    const HOOK_DB_START_DROP_TABLE = 'sys://db/startdroptable';
    const HOOK_DB_RENAME_COLUMN = 'sys://db/rename_column';

    const HOOK_MK_LINK = "generateLink";
    const USER_PWD_CHANGED = "user pwd changed";
    const HOOK_MODEL_INIT = "db_init_model_macros";
    const HOOK_AUTLOAD_CLASS = "sys_autoload_class";
    const HOOK_VIEW_MODE_CHANGED = "config_view_mode_changed";
    const HOOK_CONFIG_CTRL = "config_get_configs";
    const HOOK_DEBUGGER_VIEW = "debugger_view";
    // + | --------------------------------------------------------------------
    // + | CONTROLLER HOOK
    // + |    
    const HOOK_CONTROLLER_INIT_COMPLETE = "on_controller_init_complete";
    const HOOK_CONTROLER_LOADED = 'on_controller_loaded';

    const HOOK_FORCE_VIEW = "doc_force_view";
    const HOOK_AJX_END_RESPONSE = "ajx_end_reponse";
    const HOOK_LOG_APPEND = "sys_log_append_msg";
    const HOOK_INSTALL_SITE = "sys_install_site";
    const HOOK_INIT_GLOBAL_MATERIAL_FILTER = "sys_init_gobal_material_filter";
    const HOOK_MAIL_REGISTER = "sys_hook_mail_register";

    const P_SUBDOMAIN_PRIORITY = 0;
    const P_SESSION_PRIORITY = 100;
    
    public static function CreateHookOptions():IHookOptions{
        return new HookOptions();
    }
    /**
     * css context bind controller styles sheet on init
     */
    const HOOK_BIND_CTRL_CSS = "css_bind_ctrl_style";
    const ENV_KEY = "sys://hooks";

    const HOOK_DOM_PROPERTY_CHANGED = "dom_property_changed";
    /**
     * filter node . update property or replace with output response.
     */
    const FILTER_CREATED_NODE = "post_filter_node";

    const FILTER_DB_SCHEMA_INFO = "filter_db_schema_info";
    /**
     * filter node creation
     */
    const FILTER_PRE_CREATE_ELEMENT = "pre_filter_node";
    const FILTER_POST_CREATE_ELEMENT = "post_filter_node";
    const FILTER_CONFIG_MENU = 'filter_config_menu';

    
    const VIEWCOMPLETE = 0x1;
    private $m_methods;
    private $m_name;
    private $m_owner;
    private $m_singlemethod;
    ///<summary></summary>
    ///<param name="owner"></param>
    ///<param name="name"></param>
    ///<param name="single" default="false"></param>
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
    ///<summary>display value</summary>
    /**
     * display value
     */
    public function __toString()
    {
        return __CLASS__ . "[" . $this->m_name . ";for[" . get_class($this->m_owner) . "]]";
    }
    ///<summary>register a class method to this</summary>
    ///<param class="class">mixed class or callable</param>
    ///<param class="method">if class method is a name</param>
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
    ///<summary>invoke resgistrated method</summary>
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
    ///<summary></summary>
    /**
     * 
     */
    public function Clear()
    {
        $this->m_methods = array();
    }
    ///<summary>enumerate registrated methods</summary>
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
    ///<summary></summary>
    /**
     * 
     */
    public function getInfo()
    {
        return $this->__toString() . " count # " . igk_count($this->m_methods);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getIsDebugging()
    {
        return igk_get_env("sys://event/isdebugging/" . $this->m_name, 0);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getMethodCount()
    {
        return igk_count($this->m_methods);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getName()
    {
        return $this->m_name;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getOwner()
    {
        return $this->m_owner;
    }
    ///<summary></summary>
    ///<param name="class"></param>
    ///<param name="method"></param>
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
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="name" default="IGK_FUNC_KEY"></param>
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
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    public function setIsDebugging($v)
    {
    }

    /**
     * register hooks
     * @param mixed $name 
     * @param callable|string|array $callback 
     * @param int $priority 
     * @return void 
     */
    public static function reg_hook(string $name, $callback, $priority = 10, $injectable=true)
    {
        $hooks = & igk_environment()->createArray(self::ENV_KEY); 
        if (!isset($hooks[$name])) {
            $hooks[$name] = (object)array("list" => array(), "changed" => 1);
        }
        $hooks[$name]->list[] = (object)array(
            "priority" => $priority, 
            "callback" => $callback, 
            "injectable"=> $injectable,
        );
        $hooks[$name]->changed = 1;  
    }

    /**
    * 
    * @param mixed $name 
    * @param array $args 
    * @param ?\IGK\IHookOptions|array|object $options require IHoopOptions to bybass option behaviour
    * @return mixed 
    * @throws IGKException 
    * @throws ArgumentTypeNotValidException 
    * @throws ReflectionException 
    */
    public static function hook($name, $args = array(), $options = null)
    {
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
            $cargs = array((object)array("args" => $args, 
            "hook"=>$name,
            "handle" => 0, 
            "lastoutput" => null, 
            "output" => $def ? $def->output : null));
            $count = 0;
            foreach ($list as $v) {
                if (!is_callable($v->callback)) {
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
                } else{ 
                    $tcargs = $cargs;
                    if ($v->injectable ){
                        $fc = is_array($v->callback) ? Closure::fromCallable($v->callback) : $v->callback;
                        if (($fc instanceof \Closure ) || is_string($fc)){
                            // if ($name=="LoginService"){
                            //     igk_dev_wln("for login service");
                            // }
                            $tcargs = Dispatcher::GetInjectArgs( new \ReflectionFunction($fc), $cargs);
                        }
                    } 
                    $cargs[0]->lastoutput = call_user_func_array($v->callback, $tcargs);
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
    public static function unreg_hook($name, $callback, $all=true){
        $hooks = igk_environment()->createArray(self::ENV_KEY);
        if (!$hooks) {
            return 0;
        }
        if (is_null($callback)){
            unset($hooks[$name]);
            return true;
        }
        if (!isset($hooks[$name]->list)){
            $hooks[$name]->list = [];
        }
        $tb = & $hooks[$name]->list;
    
        if ($all){
            $c = 0;            
            $tb = array_filter(array_map(function($v)use($callback, & $c){
                if ($v->callback === $callback){
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
}
