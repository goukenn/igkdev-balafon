<?php
// @file : IGKActionBase.php


///<summary>Represente view's action definition</summary>

use IGK\Actions\IActionProcessor;
use IGK\Actions\MiddlewireActionBase;
use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\System\Exceptions\ActionNotFoundException;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Http\Request;

/**
 * Represente view's action definition
 */
abstract class IGKActionBase implements IActionProcessor
{
    /**
     * 
     * @var BaseController
     */
    protected $ctrl;
    protected $context;
    protected $throwActionNotFound = true;
    var $handleAllAction;
    /**
     * macros helper 
     * @var mixed
     */
    protected static $macro = [];
    const FAILED_STATUS = "@error";
    /**
     * define function handle
     * @var string[]
     */
    protected $defineHandle = [
        self::FAILED_STATUS => "handleError"
    ];
    /**
     * override this to handle request header
     * @return void 
     */
    protected function fetchRequestHeader()
    {
    }
    /**
     * extends default faction with macro function
     * @param mixed $name 
     * @param mixed $callback 
     * @return void 
     */
    public static function Register($name, $callback)
    {
        // 
        self::$macro[$name] = $callback;
    }
    ///<summary></summary>
    ///<param name="ctrl"></param>
    /**
     * 
     * @param mixed $ctrl
     */
    public function Initialize($ctrl)
    {
        $this->ctrl = $ctrl;
        return $this;
    }
    ///<summary>for action return the current user id</summary>
    /**
     * 
     * @return mixed 
     * @throws Exception 
     */
    public function getUserId()
    {
        return igk_sys_current_user_id();
    }
    /**
     * 
     * @param mixed $ctrl 
     * @param mixed|null $context 
     * @return object 
     * @throws Exception 
     */
    public static function Init($ctrl, $context = null)
    {
        $cl = static::class;
        if ($cl == __CLASS__) {
            igk_die("Operation not allowed");
        }
        $o = new $cl();
        $o->ctrl = $ctrl;
        $o->context = $context;
        return $o;
    }
    public static function __callStatic($name, $arguments)
    {
        return (new static)->$name(...$arguments);
    }
    /**
     * 
     * @param mixed $fname 
     * @param mixed $args 
     * @param int $exit 
     * @param int $flag 
     * @return mixed 
     * @throws Exception 
     */
    protected function Handle($fname, $args, $exit = 1, $flag = 0)
    {
        $ctrl = null;
        if ($fname instanceof BaseController) {
            if (func_num_args() < 3) {
                throw new \Exception("Require 3 argument in that case");
            }
            $ctrl = $fname;
            $c = func_get_args();
            array_shift($c);

            extract([
                "fname" => $c[0],
                "args" => $c[1],
                "exit" => igk_getv($c, 2, 1),
                "flag" => igk_getv($c, 3, 0)
            ], EXTR_OVERWRITE);
        }

        $this->ctrl = $ctrl ? $ctrl : igk_ctrl_current_view_ctrl();
        $b = $this->getActionProcessor();
        if (is_string($b)) {
            if (!class_exists($b)) {
                return false;
            }
            $cargs = [$this];
            $b = new $b(...$cargs);
        }
        return self::HandleActions($fname, $b, $args, $exit, $flag);
    }
    public function __call($name, $arguments)
    {
        if ($fc = igk_getv(self::$macro, $name)) {
            return $fc(...$arguments);
        }
        //+ | handle fetch request header
        $this->fetchRequestHeader(Request::getInstance());
        if ($fc = igk_getv($this->defineHandle, $name)) {
            if (method_exists($this, $fc)) {
                return $this->$fc(...$arguments);
            }
        }

        //+ | dispatch to method
        if (method_exists($this, $fc = $name . "_" . strtolower(igk_server()->REQUEST_METHOD))) {
            return $this->$fc(...$arguments);
        }
        if ($this->throwActionNotFound)
            throw new ActionNotFoundException("[".get_class($this)."]->".$name);
        return false;
    }
    /**
     * 
     * @return string|object classname or IActionProcessor Object 
     */
    protected function getActionProcessor()
    {
        return IGK\Actions\Dispatcher::class;
    }

    public function getController()
    {
        return $this->ctrl;
    }
    public function __get($n)
    {
        if (method_exists($this, $fc = "get" . $n)) {
            return $this->$fc();
        }
        return null;
    }

    /**
     * 
     * @param mixed $viewname 
     * @param array|object|self $arrayList action list, object dispatcher, IGKActionBase
     * @param mixed $params param to pass
     * @param int $exit must stop after execute
     * @param int $flag extra flag
     * @return mixed 
     * @throws IGKException 
     */
    public static function HandleActions($viewname, $arrayList, $params, $exit = 1, $flag = 0)
    {
        igk_set_env(IGKEnvironment::VIEW_HANDLE_ACTIONS, array("v" => $viewname, "list" => $arrayList, "args" => $params));
        $b = 0;
        if (is_string($arrayList)) {
            if (class_exists($arrayList)) {
                $arrayList = new $arrayList();
            } else {
                igk_die("not allowed view action handler");
            }
        }
        if (is_array($arrayList)) {
            foreach ($arrayList as $k => $v) {
                igk_view_reg_action($viewname, $k, $v);
            }
            igk_do_response($b = igk_view_handle_action($viewname, $params));
        } else if (is_object($arrayList)) {
            $b = self::HandleObjAction($viewname, $arrayList, $params, $exit, $flag);
        }
        igk_set_env(IGKEnvironment::VIEW_HANDLE_ACTIONS, null);
        if ($b && $exit) {
            $c = igk_get_current_base_ctrl();
            if ($c)
                $c->regSystemVars(null);
            igk_exit();
        }
        return $b;
    }
    /**
     * Handle action
     * @param string $fname 
     * @param mixed $object target
     * @param array $params parameters
     * @param int $exit stop after execution
     * @param int $flag flag use
     * @return mixed 
     * @throws IGKException 
     */
    public static function HandleObjAction($fname, $object, array $params = [], $exit = 1, $flag = 0)
    {
        // + | -------------------------------------------------------------
        // + | handle object action

        $action = "";
        $r = 0;
        if (igk_server()->REDIRECT_STATUS != 200) {
            $action = self::FAILED_STATUS;
            array_unshift($params, 0, igk_server()->REDIRECT_STATUS);
        } else {
            // + | -------------------------------------------------------------
            // + |  sanitize action name                 
            $action = ActionHelper::SanitizeMethodName(igk_getv($params, 0));
        }

        if (!empty($action)) {
            igk_set_env(IGKEnvironment::VIEW_CURRENT_ACTION, $action);
            igk_environment()->set(IGKEnvironment::VIEW_CURRENT_VIEW_NAME, $fname);
            $args = array_slice($params, 1);
          
            try {
                if ($object instanceof MiddlewireActionBase) {
                    $c =  $object->__call($action, $args);
                } else {
                    if (method_exists($object, $action)){
                        ActionHelper::BindRequestArgs($object, $action, $args);
                    }
                    $c = $object->$action(...$args);
                }
                if ($exit) {
                    return igk_do_response($c);
                }
            } catch (IGK\System\Http\RequestException $ex) {
                if ($ex->handle()) {
                    igk_exit();
                }
                throw new IGKException($ex->getMessage(), $ex->getCode(), $ex);
            } catch (Throwable $ex) {
                throw new IGKException($ex->getMessage(), $ex->getCode(), $ex);
            }
            return $c;
        }
        if (!empty($action) && (((($flag & 1) == 1) || method_exists($object, $action)) || igk_getv($object, "handleAllAction"))) {
            igk_set_env(IGKEnvironment::VIEW_CURRENT_ACTION, $action);
            $g = new ReflectionMethod($object, $action);
            $params = array_slice($params, 1);
            if (($g->getNumberOfRequiredParameters() == 1) && ($cl = $g->getParameters()[0]->getType()) && igk_is_request_type($cl)) {
                $req = IGK\System\Http\Request::getInstance();
                $req->setParam($params);
                $params = [$req];
            }
            $r = call_user_func_array(array($object, $action), $params);
            igk_do_response($r);
        }
        return $r;
    }
    protected function handleError($code, ...$params)
    {
        $c = $this->getController();
        if ($c && ($f = $c::getErrorViewFile($code)) && file_exists($f)) {
            return $c::viewError($code);
        }
        igk_trace();
        igk_wln_e("handle error data :::: ", compact("code", "f", "params"));
    }
}
