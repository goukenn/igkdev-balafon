<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RouteActionHandler.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Http;

use Closure;
use IGK\Models\Users;
use Exception;
use IGK\Actions\Dispatcher;
use IGK\Controllers\BaseController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;

/**
 * route action handler
 * @package IGK\System\Http
 */
class RouteActionHandler extends RouteHandler
{ 
    /**
     * store def access
     * @var mixed
     */
    protected $classBind;
    /**
     * auth requirement 
     * @var bool
     */
    protected $auth_requirement;
    /**
     * get the attached model user
     * @var mixed
     */
    protected $user;
    /**
     * routing info - updated after match
     * @var mixed
     */
    protected $info;

    /**
     * type of route
     * @var string
     */
    protected $type;
    /**
     * .ctr a route action handler
     * @param string $path path 
     * @param mixed $handleClass 
     * @param string $verb 
     * @return RouteActionHandler  
     */
    public function __construct($path, $handleClass, $type = "action", $verb = "GET, POST")
    {
        if (!is_string($path))
            throw new ArgumentTypeNotValidException("path");

        parent::__construct($path, $handleClass);
        $this->path = $path;
        $this->classBind = $handleClass;
        $this->type = $type;
        $this->verbs = is_string($verb) ? array_map("trim", explode(",", $verb)) : (is_array($verb) ? $verb : ['*']);
        $this->ajx = false;
    }
    /**
     * get request uri info
     * @param mixed $name 
     * @return string 
     */
    public function getUri($path = null)
    {
        if ($this->info){
            return igk_getv($this->info, "ruri");
        } 
        return  $path;
    }
    /**
     * 
     * @param mixed $args 
     * @param null|string $baseUri 
     * @return string 
     * @throws IGKException 
     */
    public function resolveUri($args, ?string $baseUri = null){
        return RouteHandler::GetResolveURI($this->path, $args, $baseUri );
    }
    
    /**
     * helper retrieve Route handle by name
     * @param mixed $name 
     * @return mixed 
     * @throws IGKException 
     */
    public static function uri($name)
    {
        if ($route = Route::GetRouteByName($name)) {
            return $route->getUri();
        }
        return null;
    }
    
 
    public function getPathUri(){
        $croute = "/" . ltrim($this->path, "/");
        $croute = preg_replace("/(\{\\s*(?P<name>" . IGK_IDENTIFIER_PATTERN . ")(?P<option>\\*)?\\s*\})/i", "",  $croute);
        if ($pos = strpos($croute, "//")){
            $croute = substr($croute, 0, $pos+1);
        }        
        return $croute;
    }
    /**
     * check if user is allowed agains this auth
     * @param Users $user 
     * @return bool
     */
    public function isAuth(Users $user) : bool
    { 
        // + | --------------------------------------------------------------------
        // + | check in amount if bool value is passed
        // + |
        
        if (is_bool($this->auth)){
            return $this->auth;
        }
        if ($user && !empty($this->auth)) {
            $r = $user->auth($this->auth, $this->auth_requirement);
            return $r;
        }
        return true;
    } 
    public function process(...$arguments){
        if (func_num_args()==0){
            igk_die("request action");
        }
        $controller = func_get_arg(0);
        if (!($controller instanceof BaseController)){
            igk_wln("controller is not a base controller");
        }
        return $this->_processAction($controller, ...array_slice(func_get_args(), 1));
    }
    public static function Handle($route, ...$arguments){
        if (!($route instanceof RouteActionHandler)){
            igk_die("route not a RouteAction Handler");
        }
        return $route->process(...$arguments);
    }
    public function getBindClass(){
        return $this->classBind;
    }
    /**
     * process this action
     * @param BaseController $controller 
     * @param mixed $args 
     * @return mixed 
     * @throws IGKException 
     */
    private function _processAction(BaseController $controller, ...$args)
    {
        $type = 0;
        $cl = "";
        $func_name = null;
        if (is_array($this->classBind) || is_callable($this->classBind)) {
            if (is_callable($this->classBind)) {
                //call static
                $type = 1;
                if (is_array($this->classBind)){
                    $cl = $this->classBind[0];
                    $func_name = $this->classBind[1];
                }else {
                    $cl = null;
                    $func_name = $this->classBind;
                    $type = 4;
                }
            } else {
                $cl = $this->classBind[0];
                $func_name = $this->classBind[1];
                if (method_exists($cl, $func_name)) {
                    $type = 3;
                }
            }
        } else {
            if (!is_string($this->classBind) || !class_exists($this->classBind)) {
                throw new IGKException("Process failed : not class Found :: " . $this->classBind);
            }
            $type = 2;
        } 
        switch ($type) {
            case 2:

                $cl = $this->classBind;
                $cl = new $cl($controller, $this);
                $name = array_shift($args);
                if (empty($name)) {
                    $name = "index";
                }
                if ($fc = closure::fromCallable([$cl, $name])->bindTo($cl)) {
                    return Dispatcher::Dispatch($fc, ...$args);
                }
                return $cl->$name(...$args);
            case 3:
                $g = new $cl($controller, $this);
                if ($fc = closure::fromCallable([$g, $func_name])->bindTo($g)){
                    array_shift($args);
                    return Dispatcher::Dispatch($fc, ...$args);
                }
                return call_user_func_array([$g, $func_name], $args);
            case 1:
                $g = new $cl($controller, $this);
                if ($fc = closure::fromCallable([$g, $func_name])->bindTo($g)){
                    array_shift($args);
                    return Dispatcher::Dispatch($fc, ...$args);
                }
                return call_user_func_array([$g, $func_name], $args);
                break;  
            case 4:
                return Dispatcher::Dispatch($func_name, ...$args);
                //return call_user_func_array($func_name, $args);
                break;              
        }
    }
   
    public static function GetRouteUri(RouteActionHandler $route, BaseController $controller, $routepattern=null){
        $t = $route->gettype();
        $c = "";
        if (class_exists($t)){
            $bname = basename(igk_dir($t));
            $c = strtolower(igk_preg_match("/^(?P<name>(.)+)(Action)$/", $bname, "name",0));                                
            if (!empty($c)){
                $c = $c.$route->getPathUri();
                if (!empty($routepattern)){
                    $c = rtrim($c, "/");
                }
                return $controller->getAppUri(implode("/", array_filter([$c,$routepattern])));
            }
        }
        return null;
    }
}
