<?php

namespace IGK\System\Http;

use ArrayAccess;
use IGK\Controllers\BaseController;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException;

/**
 * Collection of registrated routes.
 * @package IGK\System\Http
 * @method static RouteActionHandler get(string $middlewireActionBaseClass, string $pattern, ?string $page) \
 * register GET route and return RouteActionHandler
 * @method static RouteHandler get($pattern, $controllerClass) \
 * register GET route and return a RouteHandler
 */
class Route  
{
     
    /**
     * action register
     * @var array
     */
    static $sm_actions = [];
    /**
     * route register
     * @var array
     */
    static $sm_routes = [];
    /**
     * force resolv
     * @var mixed
     */
    static $sm_forceresolv;
    /**
     * name route list
     * @var mixed
     */
    static $sm_name_list;
    /**
     * route allowed verbs
     * @var string[]
     */
    protected $verb = ["GET", "POST"];
    /**
     * path of this route
     * @var string
     */
    protected $path = "";

    protected function _access_OffsetSet($n, $v){
        $this->path = $n;
        $this->controller = $v;
    }
    protected function _access_OffsetGet($n){

    }

    /**
     * load controller route route config files
     * @param mixed $controller 
     * @return void 
     */
    public static function LoadConfig(BaseController $controller)
    {
        if (file_exists($cf = $controller::configFile("routes"))) {
            $inc = function () {
                include_once(func_get_arg(0));
            };
            $inc($cf);
        }
    }
    /**
     * @param BaseController $controller 
     * @param mixed $classpath 
     * @return mixed 
     * @throws IGKException 
     */
    public static function Uri_List(BaseController $controller, $classpath)
    {
        self::LoadConfig($controller);
        $t = self::GetAction($classpath);
        return $t;
    }
    /**
     * get match all route 
     * @return Route 
     */
    public static function GetMatchAll(): Route
    {
        static $sm_route;
        if ($sm_route === null) {
            $sm_route = new Route();
            $sm_route->path = "*";
            $sm_route->verb = ["*"];
        }
        return $sm_route;
    }
    /**
     * retrieve all route collection
     * @return array  
     */
    public static function GetRoutes(){
        return array_filter(array_map(function($v){
            if ($v instanceof RouteHandler){
                if ($v->getType() == "controller"){
                    return $v;
                }

            }
        }, self::$sm_routes));
    }
  

    ///<summary>register action provider</summary>
    /**
     * register action provider
     * @param mixed $actionClass 
     * @param mixed $path 
     * @param mixed $handleClass 
     * @return RouteActionHandler|RouteHandler 
     */
    public static function RegisterAction($actionClass, $path, $handleClass=null)
    {
        /**
         * two type of returned data. depend on argument
         */
        if (is_string($actionClass) && is_string($path) && ($handleClass==null)){
            return self::RegisterRoute($actionClass, $path);
        }

        if (!isset(self::$sm_actions[$actionClass])) {
            self::$sm_actions[$actionClass] = [];
        }
        $c = new RouteActionHandler($path, $handleClass, $actionClass);
        self::$sm_actions[$actionClass][] = $c;
        self::$sm_forceresolv = 1;
        self::$sm_name_list = [];
        return $c;
    }
    
    /**
     * register route
     * @param string $path 
     * @param string $controller 
     * @return RouteHandler route handler
     */
    public static function RegisterRoute(string $path, string $controller){
        $c = new RouteHandler($path, $controller);
        self::$sm_routes[] = $c;
        return $c;
    }
    ///<summary>get action Provider</summary>
    public static function GetAction($actionClass)
    { 
        return igk_getv(self::$sm_actions, $actionClass);
    }
    public static function __callStatic($name, $arguments)
    {
        $verbs = explode('|', 'POST|GET|STORE|HEAD|PUT');

        if (in_array($v = strtoupper($name), $verbs)) {
            $fc = static::RegisterAction(...$arguments);
            $fc->setVerb([$v]); 
            return $fc;
        }
        throw new IGKException("operation not allowed");
    }
    /**
     * get route by name
     * @param mixed $name 
     * @param mixed $classPath 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetRouteByName($name, $classPath = null)
    {
        $actions = null;
        if ($classPath!==null){            
            if ($ac = igk_getv(self::$sm_actions, $classPath)){
                $actions = [$ac];
            }else{
                $actions = [];
            }
        }else{
            //search in all actions
            $actions = self::$sm_actions;
        }

        foreach ($actions as $actions) {
                          foreach ($actions as $a) {                 
                if ($name == $a->getName()) { 
                        return $a;
                    
                }
            }
        }       
        return null;
    }
}
