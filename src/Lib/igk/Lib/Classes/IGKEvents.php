<?php

///<summary>represent a event method pointer</summary>
/**
 * represent a event method pointer
 */
class IGKEvents extends IGKObject
{
    const ON_BEFORE_EXIT = "sys://event/onbeforeexit";
    const HOOK_SESS_START = "sys_session_start";
    const HOOK_APP_PRESENTATION = 0xa01;
    const HOOK_INIT_APP = "init_app";
    const HOOK_CACHE_RES_CREATED = "CacheResourceCreated";
    const HOOK_CSS_REG = "css_class_reg";
    const HOOK_DB_DATA_ENTRY = "db_dataentry";
    const HOOK_DB_INIT_COMPLETE = "db_init_complete";
    const HOOK_DB_INIT_ENTRIES = "db_init_entries";
    const HOOK_HTML_BODY = "html_body";
    const HOOK_HTML_FOOTER = "html_footer";
    const HOOK_HTML_HEAD = "html_head";
    const HOOK_HTML_META = "html_meta";
    const HOOK_PAGEFOLDER_CHANGED = "sys_pagefolder";
    const HOOK_SCRIPTS = "html_load_scripts";
    const HOOK_USER_ADDED = "sys_user_added";
    const USER_PWD_CHANGED = "user pwd changed";
    const HOOK_MODEL_INIT = "db_init_model_macros";
    const HOOK_AUTLOAD_CLASS = "sys_autoload_class";
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
     * @param mixed $callback 
     * @param int $priority 
     * @return void 
     */
    public static function reg_hook($name, $callback, $priority = 10)
    {
        $hooks = igk_environment()->{"sys://hooks"};
        if (!$hooks) {
            $hooks = array();
        }
        if (!isset($hooks[$name])) {
            $hooks[$name] = (object)array("list" => array(), "changed" => 1);
        }
        $hooks[$name]->list[] = (object)array("priority" => $priority, "callback" => $callback);
        igk_environment()->{"sys://hooks"} = $hooks;
    }

    /**
     * hook event
     */
    public static function hook($name, $args = array(), $options = null)
    {
        $def = null;
        if ($options) {
            $def = igk_get_robjs("default|output", 0, (object)$options);
        }
        $hooks = igk_environment()->{"sys://hooks"};
        if (!$hooks) {
            $hooks = array();
        }

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
            $cargs = array((object)array("args" => $args, "handle" => 0, "lastoutput" => null, "output" => $def ? $def->output : null));
            $count = 0;
            foreach ($list as $v) {
                if (!is_callable($v->callback)) {
                    if (is_object($v->callback)) {
                        $cargs[0]->lastoutput = igk_invoke_callback_obj(null, $v->callback, $cargs);
                    } else {
                        igk_wln_e(
                            __FILE__ . ':' . __LINE__,
                            " : not a callable ",
                            $name,
                            $v->callback
                        );
                        continue;
                    }
                } else
                    $cargs[0]->lastoutput = call_user_func_array($v->callback, $cargs);
                if ($cargs[0]->handle) {
                    break;
                }
                $count++;
            }
            return $cargs[0]->output;
        }
        return $def ? $def->output : $args;
    }
}
