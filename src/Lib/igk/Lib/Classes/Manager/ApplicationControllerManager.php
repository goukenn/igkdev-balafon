<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ApplicationControllerManager.php
// @date: 20220831 19:53:14
// @desc: 

namespace IGK\Manager;

use IGK\ApplicationLoader;
use IGK\Controllers\BaseController;
use IGK\Controllers\RootControllerBase;
use IGK\Resources\R;
use IGK\System\Configuration\Controllers\ConfigControllerRegistry;
use IGK\System\Exceptions\ControllerNotFoundException;
use IGKApp;
use IGKException;
use IGK\Helper\StringUtility as str;
use IGK\Helper\SysUtils;
use IGK\System\Exceptions\NotImplementException;

/**
 * manage controller between session
 * @package 
 */
class ApplicationControllerManager implements IApplicationControllerManager
{
    private $m_app;
    const INIT_METHOD = "initComplete";

    private $m_controllers = [];
    /**
     * the default application controller
     * @var ?BaseController
     */
    private $m_default_controller;

    public function __construct(IGKApp $application)
    {
        $this->m_app = $application;
    }

    public function getRegistratedNamedController(string $name): ?BaseController
    {
        return SysUtils::GetControllerByName($name, false); 
    }

    public function registerNamedController(string $name, BaseController $controller)
    {
        return null;
    }

    public function getDefaultController(): ?BaseController
    {
        return $this->m_default_controller;
    }

    public function setDefaultController(?BaseController $controller)
    {
        $this->m_default_controller = $controller;
    }

    /**
     * get controller instance
     * @param BaseController|string $n 
     * @param bool $throwex 
     * @return null|BaseController 
     * @throws IGKException 
     * @throws ControllerNotFoundException 
     */
    public function getController($n, bool $throwex = true): ?BaseController
    {
        if (is_string($n) && $this->m_default_controller && (get_class($this->m_default_controller) === $n)) {
            return $this->m_default_controller;
        }
        if ($n instanceof BaseController) {
            return $n;
        }
        if (is_string($n) && ($ctrl = igk_getv($this->m_controllers, igk_str_ns($n)))) {
            return $ctrl;
        }
        $resolv_controler = &self::GetResolvController();

        // igk_wln_e(__FILE__.":".__LINE__,  "the [{$n}]", $resolv_controler);
        if ($env_controller = igk_environment()->get(ConfigControllerRegistry::LOADED_CONFIG_CTRL)) {
            // merge with config controller
            $resolv_controler = array_merge($resolv_controler, $env_controller); // array_combine(array_keys($jump), array_values($jump)));
        }
        $cl = igk_getv($resolv_controler, $n);
        if ($cl) {
            $ctrl = new $cl();
            $this->register($ctrl);
            return $ctrl;
        }
        if (is_string($n) && class_exists($n) && is_subclass_of($n, BaseController::class)) {
            $ctrl = new $n();
            $this->register($ctrl);
            return $ctrl;
        }
        if ($throwex) {
            throw new ControllerNotFoundException($n);
        }
        return null;
    }
    public static function &GetResolvController()
    {
        static $resolv_ctrl;
        if ($resolv_ctrl === null) {
            $resolv_ctrl = include(IGK_LIB_DIR . "/.controller.pinc");
        }
        return $resolv_ctrl;
    }
    /**
     * resolv the first name 
     * @param mixed $class 
     * @return mixed 
     */
    public static function GetResolvName($class)
    {
        $g = self::GetResolvController();
        if ($c = array_search($class, $g)) {
            return $c;
        }
        return $class;
    }
    private function notPresent(BaseController $controller): bool
    {
        $c = get_class($controller);
        if (isset($this->m_controllers[$c])) {
            return false;
        }
        $n = $controller->getName();
        if (($n != $c) && isset($this->m_controllers[$n])) {
            return false;
        }
        return true;
    }
    /**
     * register new created controller
     * @param BaseController $controller 
     * @return bool 
     * @throws IGKException 
     */
    public function register(BaseController $controller): bool
    {
        // + | --------------------------------------------------------------------
        // + | CALL init complete took too long
        // + |
        
        $c = get_class($controller);
        if ($this->notPresent($controller)) {
            $cl = get_class($controller);
            $n = $controller->getName();
            $this->m_controllers[$n] = $controller;
            $this->m_controllers[$c] = $controller;
            ApplicationLoader::getInstance()->registerClass(
                igk_reflection_getdeclared_filename($cl),
                $cl
            ); 
            BaseController::Invoke($controller, self::INIT_METHOD, [__METHOD__]);            
            return true;
        }
        return false;
    }
 

    ///<summary>use to invoke system controller method</summary>
    ///<return>the selected uri</return>
    /**
     * use to invoke system controller method
     */
    public function InvokeUri($uri = null, $defaultBehaviour = true, $pattern = null)
    {
        // igk_sys_handle_uri($uri);
        $c = null;
        $f = null;
        $args = null;
        $igk = igk_app();
        $igk->Session->URI_AJX_CONTEXT = 0;
        if ($uri == null) {
            if (($p = igk_getr("p", null)) != null) {
                if (igk_sys_is_page($p)) {
                    $igk->CurrentPage = $p;
                }
            }
            if (igk_getr("l", null) != null) {
                R::ChangeLang(igk_getr("l"));
            }
            if (igk_getr("history", 0) == 1) {
                igk_debug_wln("notice:form history");
            }
            $c = igk_getru("c", null);
            $f = igk_getru("f", "invokeUri");
        } else {
            $args = igk_getquery_args($uri);
            // controller can be guid so contain - 
            $c = igk_getv($args, "c");
            $f = str_replace("-", "_", igk_getv($args, "f", ""));
            $p = igk_getv($args, "p");
            $l = igk_getv($args, "l");
            if ($p) {
                igk_getctrl(IGK_MENU_CTRL)->setPage($p, igk_getv($args, "pageindex", 0));
                unset($args["p"]);
            }
            if ($l) {
                R::ChangeLang(igk_getv($args, "l"));
                unset($args["l"]);
            }
        }
        $arg = igk_io_arg_from($f);
        if ($c && $f) {
            $ctrl = $this->getController($c) ?? ($pattern ? $pattern->ctrl : null) ?? igk_template_create_ctrl($c);
            if (!$ctrl) {
                return null;
            }
            if (!method_exists($ctrl, $f)) {
                igk_set_header(404);
                if (!igk_get_contents($ctrl, 404, ["method not found"])) {
                    igk_die("method not exists --- > [" . get_class($ctrl) . "::" . $f . "] " . $uri);
                }
                igk_exit();
                return false;
            }
            if ($f == IGK_EVALUATE_URI_FUNC) {
                igk_app()->setBaseCurrentCtrl($ctrl);
            }

            if (($f == IGK_EVALUATE_URI_FUNC) || $ctrl->IsFunctionExposed($f)) {
                igk_app()->session->URI_AJX_CONTEXT = igk_is_ajx_demand() || str::EndWith($f, IGK_AJX_METHOD_SUFFIX) || (igk_getr("ajx") == 1);
                $fd = null;
                // if(($fd=$ctrl->getConstantFile()) && file_exists($fd))
                //     include_once($fd);
                if (($fd = $ctrl->getDbConstantFile()) && file_exists($fd))
                    include_once($fd);
                unset($fd);
                igk_set_env(IGK_ENV_REQUEST_METHOD, strtolower(get_class($ctrl) . "::" . $f));
                igk_set_env(IGK_ENV_INVOKE_ARGS, $args);
                // igk_wln(__FILE__.":".__LINE__, "invoke : ".$f);
                if (is_array($arg))
                    call_user_func_array(array($ctrl, $f), $arg);
                else {
                    if ($arg)
                        $ctrl->$f($arg);
                    else {
                        $ctrl->$f();
                    }
                }
                igk_set_env(IGK_ENV_INVOKE_ARGS, null);
                igk_set_env(IGK_ENV_REQUEST_METHOD, null);
                if ($defaultBehaviour && $igk->Session->URI_AJX_CONTEXT) {
                    igk_exit();
                }
            }
        }
        return $c;
    }

    ///<summary>array of loaded controller</summary>
    /**
     * array of loaded controller
     * @return array 
     */
    public function getControllers(): array
    {
        $t_ctrl = array_unique(array_values($this->m_controllers));
        return $t_ctrl;
    }
    /**
     * get controller reference. used internally to update controller stored list 
     * @return array 
     */
    public function &getControllerRef(): array
    {
        return $this->m_controllers;
    }
    /**
     * list of user controllers
     * @return array 
     */
    public function getUserControllers(): array
    {
        $tab = $this->getControllers();
        $out = array();
        $callbackfilter = null;
        if (igk_count($tab) > 0) {
            foreach ($tab as $v) {
                if ((get_class($v) === __PHP_Incomplete_Class::class) ||
                    RootControllerBase::IsSystemController($v) || RootControllerBase::IsIncludedController($v) ||
                    !RootControllerBase::Invoke($v, "getCanModify") ||
                    ($callbackfilter && !$callbackfilter($v))
                ) {
                    continue;
                }
                $out[] = $v;
            }
        }
        return $out;
    }

    /**
     * invoke pattern
     * @param mixed $pattern 
     * @return mixed 
     * @throws IGKException 
     * @throws ControllerNotFoundException 
     */
    public function InvokePattern($pattern)
    {
        return $this->InvokeUri($pattern->value, 1, $pattern);
    }
}
