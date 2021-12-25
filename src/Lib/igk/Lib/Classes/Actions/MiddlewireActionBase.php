<?php

namespace IGK\Actions;

use IGK\Helper\MacrosHelper;
use IGK\Models\Users;
use IGK\System\Http\RedirectRequestResponse;
use IGK\System\Http\Request;
use IGK\System\Http\Route;
use IGK\System\Http\RouteActionHandler;
use IGKActionBase;
use IGKException;
/**
 * static function Handle($obj, $fname, $args);
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

   
    protected function getActionProcessor()
    {
        return $this;
    }
    protected function checkMiddle(){
        $this->ctrl->checkUser(false); 
        if (!$this->ctrl->User){
             
            if ($this->redirect){
                return new RedirectRequestResponse($this->ctrl::uri($this->redirect));
            }
            throw new IGKException("User Not found");
        }
        $user = $this->ctrl->User;
        if (!$user){
            throw new IGKException("no users");
        } 

        $user = Users::currentUser();
        if ( $this->auths && !$user->auth($this->auths)){
             throw new IGKException("Resource access not allowed");
        }
        $this->user = $user;
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
        $path = trim($path, '/');    

        if (!empty($routes)){
            // must use the route technique to validate the path
            // igk_wln(__FILE__.":".__LINE__, $path, $routes);
            
            foreach($routes as $v){ 
                if ($v->match($path, igk_server()->REQUEST_METHOD)){ 
                    if (!$v->isAuth($user)){
                        throw new IGKException("Route access not allowed");
                    } 
                    $v->setUser($user);
                    $v->setRoutingInfo((object)[
                        "ruri"=>$ruri
                    ]);                    
                    array_unshift($arguments, $this->ctrl); 
                    return RouteActionHandler::Handle($v, ...$arguments);
                }
                 
            }
            throw new IGKException("Route not resolved", 404);
        }
        $route = Route::GetMatchAll();
        return $this->invoke($route, $arguments); 
    }
    abstract protected function invoke($route, $args);
}
