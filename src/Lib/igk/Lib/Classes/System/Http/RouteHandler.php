<?php

namespace IGK\System\Http;

use IGK\Actions\Dispatcher;
use ReflectionMethod;

/**
 * represent a route handler object
 * @package 
 */
class RouteHandler
{
    /**
     * name for searching
     * @var mixed
     */
    protected $name;
    /**
     * route type
     * @var string
     */
    private $type = 'controller';
    /**
     * get verbs
     * @var array
     */
    protected $verbs = [];

    /**
     * autorisation string
     * @var string|array
     */
    protected $auth;

    /**
     * route path pattern
     * @var string
     */
    protected $path;

    /**
     * controller or route class handler
     */
    protected $controller;

    /**
     * set the route
     */
    protected $route;

    /**
     * support ajx
     * @var bool
     */
    protected $ajx;

    /**
     * stored expression
     * @var mixed
     */
    protected $m_expressions;

    public function getRoute()
    {
        return $this->route;
    }
    public function getPath()
    {
        return $this->path;
    }
    public function getVerbs()
    {
        return $this->verbs;
    }

    protected function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function isAuthRequired(){
        return !empty($this->auth);
    }

 /**
     * set roting property object
     * @param object $info 
     * @return void 
     */
    public function setRoutingInfo(object $info)
    {
        if ($info == null) {
            $this->info = $info;
            return $this;
        }
        $this->info = igk_get_robjs("ruri|args", 0, $info);
        return $this;
    }
    public function getRoutingInfo($name=null)
    {
        if ($name!==null && $this->info){
            return igk_getv($this->info, $name);
        }
        return $this->info;
    }
    /**
     * return the selected user auth
     * @return mixed 
     */
    public function getUserAuth()
    {
        if ($u = $this->user) {
            return $u->{"::auth"};
        }
        return;
    }
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    /**
     * return the selected use
     * @return mixed 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * return the name
     */
    public function getName()
    {
        return $this->name;
    }
    /** 
     * return this type
     */
    public function getType()
    {
        return $this->type;
    }

    public function __construct($path, $controller)
    {
        $this->path = $path;
        $this->controller = $controller;
    }
    /**
     * get if match with the verbs
     * @param mixed $path 
     * @param string $verb 
     * @return int|false 
     * @throws Exception 
     */
    public function match($path, $verb = 'GET')
    { 
        if (!in_array($verb, $this->verbs)) {
            return false;
        }
        $regex = $this->getPatternRegex();
        if ($r = preg_match($regex, "/" . ltrim($path, "/"))) {
            if ($this->ajx && !igk_is_ajx_demand()) {
                throw new RequestException(400);
            }
            $this->setRoute($path);
            $this->info = (object)[
                "regex"=>$regex,
                "response"=>$r,
                "ruri" =>$path
            ];
            
        }
        return $r;
    }
    /**
     * retrieve pattern regex expression
     * @return string 
     * @throws Exception 
     */
    protected function getPatternRegex()
    {
        $croute = "/" . ltrim($this->path, "/");
        if (preg_match_all("/(\{\\s*(?P<name>" . IGK_IDENTIFIER_PATTERN . ")(?P<option>\\*)?\\s*\})/i", $croute, $tab)) {
            $count = 0;
            foreach ($tab["name"] as $i) {
                $c = trim($i);
                $s = $tab[0][$count];
                $opt = igk_getv($tab["option"], $count) == "*";
                // igk_wln($i);
                if ($g = igk_getv($this->m_expressions, $c, ".*")) {
                    if ($opt) {
                        $g = "(/{$g}(/)?)?";
                        $s = "/" . rtrim($s, "/");
                    }
                    $croute = str_replace($s, "(?P<".$i.">" . $g . ")", $croute);
                }
                $count++;
            }
        }
        return "#^" . $croute . "$#";
    }
    /**
     * add expression
     * @param mixed $name 
     * @param mixed $expression 
     * @return RouteHandler 
     */
    private function addExpression($name, $expression)
    {
        $this->m_expressions[$name] = $expression;
        return $this;
    }
    /**
     * set the shorcut key name
     * @return RouteHandler 
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * set autorisation key name
     * @param mixed $name string|array of autorisation
     * @param bool $strict autorisation requirement
     * @return RouteActionHandler 
     */
    public function auth($name, bool $strict=false)
    {
        $this->auth = $name;
        $this->auth_requirement = $strict;
        return $this;
    }
    /**
     * add where condition expression
     * @return RouteHandler 
     */
    public function where($id, $pattern)
    {
        return $this->addExpression($id, $pattern);
    }
 /**
     * set allowed verb
     * @param array $verb 
     * @return RouteHandler
     */
    public function setVerb(array $verb)
    {
        $this->verbs = $verb;
        return $this;
    }
    /**
     * shortcut function
     * @param array $verb 
     * @return RouteHandler 
     */
    public function verbs(array $verb)
    {
        return $this->setVerb($verb);
    }
    protected function process(...$arguments)
    {
        $ctrl = igk_getctrl($this->controller); 
        $args = $arguments;
        $functions = get_class_methods($this->controller);
        $method = igk_server()->REQUEST_METHOD;
        $extens = ["_".$method, ""]; 
        while($func = array_shift($args)){
            // get public function 
            foreach($extens as $f){
                if (in_array($func.$f, $functions) && $ctrl->IsFunctionExposed($func.$f)){
                    // dispath to method 
                    $func.=$f;
                    // Dispatch to methods
                    $ref = new ReflectionMethod($ctrl, $func);
                    Dispatcher::ResolvDispatchMethod($ref, $args); 
                    return \IGK\System\Http\Response::HandleResponse($ctrl->$func(...$args));
                }
            }
        }
        throw new RequestException(404, "api route not found");
        // \IGK\System\Http\Response::HandleResponse( new ErrorRequestResponse(404) ) ;
    }

    /**
     * 
     * @param mixed|RouteHandler $route 
     * @param array $arguments argument 
     * @return mixed 
     */
    public static function Handle($route, ...$arguments){
        return $route->process(...$arguments);
    }   

     /**
     * set ajx route pattern requirement
     * @param bool $value 
     * @return $this 
     */
    public function ajx(bool $value =  true){
        $this->ajx = $value;
        return $this;
    }
}
