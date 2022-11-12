<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MiddlewireActionBase.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Actions;
 
use IGK\Helper\SysUtils;
use IGK\Models\Users;
use IGK\System\Http\RedirectRequestResponse;
use IGK\System\Http\Request;
use IGK\System\Http\Route;
use IGK\System\Http\RouteActionHandler;
use IGKActionBase;
use IGKException;
use Reflection;
use ReflectionMethod;

use function igk_resources_gets as __;
/**
 * Action Middleware
 * use to process method with specific checkMiddle - route 
 * @package IGK\Actions
 */
abstract class MiddlewireActionBase extends IGKActionBase{
    /**
     * default user
     * @var mixed
     */
    protected $user;

    /**
     * redirection uri
     * @var mixed
     */
    protected $redirect;
    /**
     * array of this middle wire auths
     * @var mixed
     */
    protected $auths;

    /**
     * middleware object
     * @var mixed
     */
    protected $middleware;

    /**
     * get action processor
     * @return string|object 
     */
    protected function getActionProcessor()
    {
        return $this;
    }
    /**
     * check middle before process action
     * @return true|RedirectRequestResponse 
     * @throws IGKException 
     */
    protected function checkMiddle(){ 
        
        $this->ctrl->checkUser(false);         
        $this->user = Users::currentUser();
        if (empty($auths = $this->auths)){
            return true;
        }
        if (!$this->user){ 
            if ($this->redirect){
                return new RedirectRequestResponse($this->ctrl::uri($this->redirect));
            }
            throw new IGKException("User Not found");
        }  
        if ( !$this->user->auth($auths)){
             throw new IGKException("Resource access not allowed");
        } 
        return true;
    }
/**
 * 
 * @param mixed $name 
 * @param mixed $arguments 
 * @return mixed 
 */
    public static function __callStatic($name, $arguments)
    {    
        return  (new static())->$name(...$arguments);        
    }
    /**
     * 
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws IGKException 
     */
    public function __call($name, $arguments)
    {
        if ($rep = $this->checkMiddle()){
            if (is_object($rep)){
                return $rep;
            } 
        } 
        Route::LoadConfig($this->ctrl);
       
        $path = "/".implode("/", array_merge([$name], $arguments)); 

    
        $ruri = Request::getInstance()->view_args("entryuri").$path; 
        $routes = Route::GetAction(static::class);
        $user = $this->user;
        $path =  "/" . trim($path, "/");  
        if (!empty($routes)){
            // must use the route technique to validate the path
             $method = igk_server()->REQUEST_METHOD;
            foreach($routes as $v){  
                // igk_dev_wln_e(__FILE__.":".__LINE__,  "name: ". $v->isAuthRequired());
                if ($v->match($path, $method)){  
                    $redirect =  $v->getRedirectTo(); 
                    if ($v->isUserRequired()){
                        if (!$user){
                            $m = "User required.";
                            $redirect && $this->_handle_redirect($redirect, 402, $m);                            
                            throw new IGKException("User required.", 402);
                        }
                    }               
                    if($v->isAuthRequired()){
                        if ($user && !$v->isAuth($user)){
                            $m = "Route access not allowed.";
                            $redirect && $this->_handle_redirect($redirect, 402, $m);
                            throw new IGKException($m, 402);
                        } else if (!$user){
                            $m = "Role require an user.";
                            $redirect && $this->_handle_redirect($redirect, 402, $m);
                            throw new IGKException($m, 402);
                        }
                    }
                    $v->setUser($user);
                    $v->setRoutingInfo((object)[
                        "ruri"=>$ruri
                    ]);                    
                    if ($v->getBindClass() === null){
                        $m = get_class_methods(static::class);
                        // detected method to invoke
                        $proc = ["_".strtolower(igk_server()->REQUEST_METHOD), ""];
            
                        while((count($proc)>0) && (($f = array_shift($proc))!==null)){
                            if (in_array($name.$f, $m)){
                                $name = $name.$f;  
                                $arguments = $arguments ? Dispatcher::GetInjectArgs(new ReflectionMethod($this, $name), $arguments) : [];
                                return $this->$name(...$arguments);
                            }
                        }  
                        // no controller task setup
                        return null;
                    }
                    // + | bind action
                    array_unshift($arguments, $name);  
                    array_unshift($arguments, $this->ctrl);  
                    //igk_wln_e($arguments);
                    return RouteActionHandler::Handle($v, ...$arguments);
                } 
            }  
            // + | route not resolved 
            throw new IGKException(__("Route {0} not resolved, in {1} ", $path, get_class($this)), 404);
        }
        $route = Route::GetMatchAll();
        return $this->invoke($route, $arguments); 
    }
    private function _handle_redirect($url, $code, $message){
        igk_navto($url, $code);
    }
    /**
     * invoke route
     * @param mixed $route 
     * @param mixed $args 
     * @return void 
     */
    protected function invoke($route, $args){
    }
   
}
