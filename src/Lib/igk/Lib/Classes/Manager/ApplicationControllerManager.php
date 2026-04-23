<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ApplicationControllerManager.php
// @date: 20220831 19:53:14
// @desc: 
namespace IGK\Manager;
use Doctrine\Inflector\Rules\Patterns;
use IGK\ApplicationLoader;
use IGK\Constants;
use IGK\Controllers\ApplicationModuleController;
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
use IGK\System\Http\RequestResponseCode;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;

/**
 * manage controller between session
 * @package 
 */
class ApplicationControllerManager implements IApplicationControllerManager
{
    /**
    * Property: app.
    * @var mixed
    */
    private $m_app;
    /**
    * Constant: init method.
    * @var mixed
    */
    const INIT_METHOD = "initComplete";
    /**
    * Constant: ref module token identifier.
    * @var mixed
    */
    const REF_MODULE_TOKEN_IDENTIFIER='ref-module';
    /**
    * Property: controllers.
    * @var mixed
    */
    private $m_controllers = [];
    /**
     * the default application controller
     * @var ?BaseController
     */
    private $m_default_controller;
    /**
    * .ctr
    * @param IGKApp $application
    */
    public function __construct(IGKApp $application)
    {
        $this->m_app = $application;
    }
    /**
    * Returns Registrated Named Controller.
    * @param string $name
    * @return ?BaseController
    */
    public function getRegistratedNamedController(string $name): ?BaseController
    {
        return SysUtils::GetControllerByName($name, false);
    }
    /**
    * Registers Named Controller.
    * @param string $name
    * @param BaseController $controller
    */
    public function registerNamedController(string $name, BaseController $controller)
    {
        return null;
    }
    /**
    * Returns Default Controller.
    * @return ?BaseController
    */
    public function getDefaultController(): ?BaseController
    {
        return $this->m_default_controller;
    }
    /**
    * Sets Default Controller.
    * @param null|BaseController $controller
    */
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
        static $resolved;
        if (is_null($resolved)){
            $resolved = [];
        }
        if (key_exists($n, $resolved)){
            return $resolved[$n];
        }
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
        if ($env_controller = igk_environment()->get(ConfigControllerRegistry::LOADED_CONFIG_CTRL)) {
            $resolv_controler = array_merge($resolv_controler, $env_controller); 
        }
        $cl = igk_getv($resolv_controler, $n);
        $is_s = is_string($n) ;
        if ($cl) {
            $ctrl = new $cl();
        } else if ($is_s && class_exists($n) && is_subclass_of($n, BaseController::class)) {
            if (($n == ApplicationModuleController::class) || is_subclass_of($n, ApplicationModuleController::class)) {
                throw new \IGKException('module controller can\'t be instancied');
            }
            $ctrl = new $n();
        } else if ($is_s){
            $ctrl = self::RetrieveControllerFromReference($n); 
            if ($ctrl){
                $resolved[$n] = $ctrl;
            }
            return $ctrl;
        }
        if ($ctrl) {
            $this->register($ctrl);
            return $ctrl;
        }  
        if ($throwex) {
            throw new ControllerNotFoundException($n);
        }
        return null;
    }
    /**
    * Returns Resolv Controller.
    */
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
    /**
    * auto generate doc.
    * @param BaseController $controller
    * @return bool
    */
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
    /**
     * use to invoke system controller method
     */
    public function InvokeUri($uri = null, $defaultBehaviour = true, $pattern = null)
    {
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
                igk_set_header(RequestResponseCode::NotFound);
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
                if (($fd = $ctrl->getDbConstantFile()) && igk_io_file_exists($fd, true))
                    include_once($fd);
                unset($fd);
                igk_set_env(IGK_ENV_REQUEST_METHOD, strtolower(get_class($ctrl) . "::" . $f));
                igk_set_env(IGK_ENV_INVOKE_ARGS, $args);
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
                if ((get_class($v) === \__PHP_Incomplete_Class::class) ||
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
    /**
    * auto generate doc.
    * @param string $reference
    * @return void
    */
    static function RetrieveControllerFromReference(string $reference)
    {
        $reference = trim($reference, '. ');
        $regex = new RegexMatcherContainer;
        $cp = $regex->begin('#ref-module\\s*\(', '\)', 'ref-module')->last();
        $stringLitteral = [
            'begin'=>"('|\")",
            'end'=>'\\1',
            'name'=>'refname',
            'patterns'=>[
                [
                    "match"=>"\\\\."
                ]
            ]
        ];
        $cp->patterns = [
            [
                'name' => 'refname',
                'match' => '(?i)[a-z][a-z0-9\\.\\/\\\\]+\\b',
            ],
            $stringLitteral
        ];
        $pos = 0;
        $v = '';
        while ($g = $regex->detect($reference, $pos)) {
            if ($e = $regex->end($g, $reference, $pos)) {
                if (($e->tokenID == self::REF_MODULE_TOKEN_IDENTIFIER ) && (strlen(trim($v)) > 0)){
                    $v = str_replace('.', '\\', $v);  
                    if ($module = igk_get_module($v)){
                        return $module;
                    }
                    $check = igk_get_module('igk\\authentications\\WebAuthn');
                    throw new \IGKException('reference module not found or not loaded '.$v. ' ===? check is null '.(is_null($check)));
                }
                if ($e->match->name =='refname'){
                    $v = igk_str_remove_quote($e->value); 
                }
            }
        }
        return null;
    }
}