<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKActionBase.php
// @date: 20220803 13:48:54
// @desc: 

// @file : IGKActionBase.php


///<summary>Represente view's action definition</summary>

use IGK\Actions\Dispatcher;
use IGK\Actions\IActionProcessor;
use IGK\Actions\MiddlewireActionBase;
use IGK\Controllers\BaseController;
use IGK\Controllers\ControllerEnvParams;
use IGK\Helper\ActionHelper;
use IGK\Helper\ExceptionUtils;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ActionNotFoundException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Http\NotAllowedRequestException;
use IGK\System\Http\Request;
use IGK\System\Http\Route;
use IGK\System\Http\WebResponse;
use IGK\System\Traits\InjectableTrait;

/**
 * Represente view's action definition
 */
abstract class IGKActionBase implements IActionProcessor
{
    use InjectableTrait;
    
    const INIT_TRAIT_PREFIX =   '_init_trait_' ;
    /**
     * 
     * @var BaseController
     */
    protected $ctrl;
    
    protected $context;

    protected $defaultEntryMethod = 'index';

    protected $fname = '';
    /**
     * store error message
     * @var array
     */
    protected $errors = [];
    /**
     * handle exit and force do_response
     * @var bool 
     */
    protected $handleExit = true;
    /**
     * throw no action found
     * @var true
     */
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
    protected $notify_name;

   

    /**
     * change the controller
     * @param null|BaseController $controller 
     * @return void 
     */
    public function setController(?BaseController $controller){        
        if ($controller)
            $this->initialize($controller);
        $this->ctrl = $controller;

    }
    /**
     * get default entry method
     * @return string 
     */
    public function getDefaultEntryMethod(){
        return $this->defaultEntryMethod;
    }
    public function __construct()
    {
        if (empty($this->notify_name)) {
            $this->notify_name = static::class;
        }
    }
    /**
     * action processor host
     * @return $this 
     */
    public function getHost()
    {
        return $this;
    }

    public static function CurrentAction()
    {
        return igk_environment()->get(IGKEnvironment::VIEW_CURRENT_ACTION);
    }
    public static function CurrentViewName()
    {
        return igk_environment()->get(IGKEnvironment::VIEW_CURRENT_VIEW_NAME);
    }
    public static function ActionParams()
    {
        return igk_environment()->get(IGKEnvironment::VIEW_ACTION_PARAMS);
    }
    /**
     * handle notify bool
     * @param null|bool $result 
     * @param string $successMsg 
     * @param string $dangerMsg 
     * @return null|bool 
     * @throws IGKException 
     */
    protected function handleBool(?bool $result, string $successMsg, string $dangerMsg)
    {
        if ($result) {
            $this->notify_success($successMsg);
        } else {
            $this->notify_danger($dangerMsg);
        }
        return $result;
    }
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
    protected function initialize(BaseController $ctrl)
    {   
        $this->ctrl = $ctrl;
        $this->fname = ViewHelper::GetViewArgs('fname');
        $traits = class_uses(static::class);
        
        foreach($traits as $f){
            $n = basename(igk_uri($f)); 
            if (method_exists($this, $fc = self::INIT_TRAIT_PREFIX.$n)){
                $this->$fc($ctrl);  
            }
        } 
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
     * @return static 
     * @throws Exception 
     */
    public static function CreateInstance($ctrl, $context = null)
    {
        $cl = static::class;
        if ($cl == __CLASS__) {
            igk_die("Operation not allowed");
        }
        $o = new $cl();
        $o->ctrl = $ctrl;
        $o->context = $context;
        $o->initialize($ctrl);
        return $o;
    }
   
    public static function __callStatic($name, $arguments)
    {
        $c =  (new static);
        igk_environment()->action_handler_instance = $c ; 
        return $c->$name(...$arguments);
    }
    /**
     * parameter mixing handle fonction
     * @param mixed $fname 
     * @param mixed $args 
     * @param int $exit 
     * @param int $flag 
     * @return mixed 
     * @throws Exception 
     * - override 1 :  (BaseController , $fname, $args, $exit=1, $flag=0)
     * - override 2 :  ($fname, $args,  $exit=1, $flag=0)
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
                "exit" => igk_getv($c, 2, $this->handleExit ? 1 : 0),
                "flag" => igk_getv($c, 3, 0)
            ], EXTR_OVERWRITE);
        }

        $ctrl = $ctrl ? $ctrl : igk_ctrl_current_view_ctrl();
        $this->initialize($ctrl);
        $b = $this->getActionProcessor();
        if (is_string($b)) {
            if (!class_exists($b)) {
                return false;
            }
            $cargs = [$this];
            $b = new $b(...$cargs);
            // $this->throwActionNotFound = $flag;
        }
        return self::HandleActions($fname, $b, $args, $exit, $flag);
    }
    public function __call($name, $arguments)
    {
        // + : -----------------------------------------
        // + : fallback to index if numeric name is call 
        // + : -----------------------------------------
    
        if (is_numeric($name)) {
            array_unshift($arguments, $name);
            $name = $this->defaultEntryMethod;           
            if (method_exists($this, $fc = $name)) {
                return $this->$fc(...$arguments);
            }
        }
        if ($fc = igk_getv(self::$macro, $name)) {
            return $fc(...$arguments);
        }
        // + |-------------------------------------------
        // + | handle fetch request header
        // + |
        $this->fetchRequestHeader(Request::getInstance());
        if ($fc = igk_getv($this->defineHandle, $name)) {
            if (method_exists($this, $fc)) {
                return $this->$fc(...$arguments);
            }
        }
        //+ | ------------------------------------------------------------------------------------
        //+ | dispatch to verb method    
        //+ |
        $verb = strtolower(igk_server()->REQUEST_METHOD ?? 'get');
        if (method_exists($this, $fc = $name . "_" . $verb)) {
            return $this->_dispatchAndInvoke($fc, $arguments);
       
        } else if ($verb == "options") {
            // handle default method option
            //options request ...
            $rep = new WebResponse("/Options:data,request_uri:".igk_io_request_uri(), 200, [
                "Content-Type: text/html",
                "Access-Control-Allow-Origin: ".igk_configs()->get("access-control-allow-origin", "*"),
                "Access-Control-Allow-Methods: ".igk_configs()->get("access-control-allow-methods", "*"),
                "Access-Control-Allow-Headers: ".igk_configs()->get("access-control-allow-headers", "*")
            ]);
            $rep->cache =false;
            igk_do_response($rep);  
        }
        if ($this->throwActionNotFound) {
            throw new ActionNotFoundException("[" . get_class($this) . "]->" . $name);
        }
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
    /**
     * get controller
     * @return BaseController 
     */
    public function getController()
    {
        return $this->ctrl;
    }
    /**
     * get current user profile
     * @return object 
     */
    protected function currentUser(){
        return $this->getController()->getUser();
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
     * @param array|object|self $arrayList action list, object dispatcher, ActionBase
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
     * determine if handling response 
     * @param mixed $response 
     * @return bool 
     * @throws IGKException 
     */
    protected function _handleResponse($response): bool
    {
        // + | --------------------------------------------------------------------
        // + | by default in ajx context and not null 
        // + |
        
        return (igk_is_ajx_demand() && !is_null($response));
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
        $actionMethod = "";
        $env = igk_environment();
        $redirect_status = igk_server()->REDIRECT_STATUS;
        if ($redirect_status && ($redirect_status != 200)) {
            $actionMethod = self::FAILED_STATUS;
            array_unshift($params, 0, igk_server()->REDIRECT_STATUS);
        } else {
            // + | -------------------------------------------------------------
            // + |  sanitize action name                 
            $actionMethod = ActionHelper::SanitizeMethodName(igk_getv($params, 0));
        }

        if (!empty($actionMethod)) {
            // igk_dev_ilog("CALLING ACTION ".static::class ."::".$actionMethod);
            $args = array_slice($params, 1);
            $env->set(IGKEnvironment::VIEW_CURRENT_ACTION, $actionMethod);
            $env->set(IGKEnvironment::VIEW_CURRENT_VIEW_NAME, $fname);
            $env->set(IGKEnvironment::VIEW_ACTION_PARAMS, $args);
            try {

                if ($verb = igk_server()->REQUEST_METHOD) {
                    if (preg_match("/(.)_(" . Route::SUPPORT_VERBS . ")$/i", $actionMethod)
                        // && (!preg_match("/_($verb)$/i", $actionMethod))
                    ) {
                        throw new NotAllowedRequestException(null, "explicit verbs not allowed missmatch");
                    }
                    unset($verb);
                }
                $_is_middelwire = $object instanceof MiddlewireActionBase;
                if ($_is_middelwire) {
                    $c =  $object->__call($actionMethod, $args);
                } else {
                    if (method_exists($object, $actionMethod)) {
                        ActionHelper::BindRequestArgs($object, $actionMethod, $args);
                    }
                    $c = $object->$actionMethod(...$args);
                }
                $object->getController()->{ControllerEnvParams::ActionViewResponse} = $c;

                $_host = $object->getHost();
                // + | --------------------------------------------------------------------
                // + | force redirection before render comment
                // + |
                if (!empty($_host->redirect)){
                    igk_navto($_host->redirect);
                }
                // + | --------------------------------------------------------------------
                // + | CHECK EXIT FOR DO RESPONSE   
                // + |               
                //                
                if ($exit || ($_host->_handleResponse($c))) {
                    return igk_do_response($c);
                }
            } catch (IGK\System\Http\RequestException $ex) {
                if ($ex->handle()) {
                    igk_exit();
                }
                throw new IGKException($ex->getMessage(), $ex->getCode(), $ex);
            } catch (Throwable $ex) {
                // igk_wln_e($ex->getMessage());
               // ExceptionUtils::ShowException($ex); 
                throw new IGKException($ex->getMessage(), $ex->getCode(), $ex);
            }
            return $c;
        }
    }
    protected function handleError($code, ...$params)
    {
        $c = $this->getController();
        if ($c && ($f = $c::getErrorViewFile($code)) && file_exists($f)) {
            return $c::viewError($code);
        } 
        igk_dev_wln_e(__FILE__ . ":" . __LINE__,  "No handle error: ", compact("code", "f", "params"));
    }

    protected function get_notify()
    {
        $notkey = $this->getController()->notifyKey($this->notify_name);
        return igk_notifyctrl($notkey);
    }
    /**
     * assert notify
     * @param mixed $result condition
     * @param mixed $success 
     * @param mixed $danger 
     * @return mixed 
     * @throws IGKException 
     */
    public function assert_notify($result, $success, $danger)
    {
        if ($result) {
            $this->notify_success($success);
        } else {
            $this->notify_danger($danger);
        }
        return $result;
    }
    protected function notify_danger($msg)
    {
        $this->get_notify()->danger($msg);
    }
    protected function notify_success($msg)
    {
        $this->get_notify()->success($msg);
    }

    /**
     * index action entry point
     * @return void 
     */
    public function index()
    {
    }
}
