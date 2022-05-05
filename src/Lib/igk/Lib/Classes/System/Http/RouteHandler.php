<?php

namespace IGK\System\Http;

use IGK\Actions\Dispatcher;
use IGKException;
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

    /**
     * enable strict directory
     * @var bool
     */
    protected $strict_dir;

    /**
     * authenticated user required
     * @var bool
     */
    protected $user_required;

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
    public function isUserRequired(){
        return $this->user_required;
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
        // + match verb
        if (!in_array($verb, $this->verbs)) {
            return false;
        }        
        $regex = $this->getPatternRegex();   
        if ($r = preg_match($regex, $path)) {
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
        return static::GetRouteRegex($this->path, $this->m_expressions);

        // $croute = "/" . ltrim($this->path, "/");
        // if (preg_match_all("/(?P<mark1>\/)?(\{\\s*(?P<name>" . IGK_IDENTIFIER_PATTERN . ")(?P<option>\\*)?\\s*\})(?P<mark2>\/)?/i", $croute, $tab)) {
        //     $count = 0;
        //     foreach ($tab["name"] as $i) {
        //         $c = trim($i);
        //         $s = $tab[0][$count];
        //         $opt = igk_getv($tab["option"], $count) == "*";
        //         $mark1 = igk_getv($tab["mark1"], $count);
        //         $mark2 = igk_getv($tab["mark2"], $count);

                
                
        //         if ($g = igk_getv($this->m_expressions, $c, ".*")) {
        //             if ($opt) {
        //                 $g = "({$g}(/)?)?";
        //                 //$s = "/" . rtrim($s, "/");
        //             }
        //             $croute = str_replace($s, "(?P<".$i.">" . $g . ")", $croute);
        //         }
        //         $count++;
        //     }
        // }
        // return "#^" . $croute . "$#";
    }
    public static function GetRouteRegex(string $path, ?array $expressions=null, bool $strict_dir = true){
        $croute = "/" . ltrim($path, "/");
        if (preg_match_all("/(?P<mark1>\/)?(\{\\s*(?P<name>" . IGK_IDENTIFIER_PATTERN . ")(?P<option>\\*)?\\s*\})(?P<mark2>\/)?/i", $croute, $tab)) {
            $count = 0;
            $optional = false;
            foreach ($tab["name"] as $i) {
                $c = trim($i);
                $s = $tab[0][$count];
                $opt = igk_getv($tab["option"], $count) == "*";
                $mark1 = igk_getv($tab["mark1"], $count);
                $mark2 = igk_getv($tab["mark2"], $count);
                // if ($mark1 && ($mark1 == $mark2)){
                //     // inside mark
                // }
                if ($g = igk_getv($expressions, $c, ".*")) {
                    if ($g == ".*"){
                        $g = "[^/]+";
                    }                   
                    $rp = "(?P<".$i.">" . $g . ")";
                    if ($opt) { 
                        $optional = true;
                        $rp.="?";
                    }
                    if ($mark2){
                        $rp .= "(/)";
                    }
                    if ($mark1){
                        $rp = "(".$mark1.$rp.")";
                        if ($optional)
                            {
                                $rp .="?";
                            }
                    }
                    $croute = str_replace($s, $rp, $croute);
                }
                $count++;
            }
        }
        if (!$strict_dir){
            if (strrpos($croute, "(/)",-3) !== false){
                $croute .= "?";
            }
        }
        return "#^" . $croute . "$#";
    }

    /**
     * retrive resolved uri
     * @param string $routepattern 
     * @param null|array $resolve 
     * @param null|string $baseUri 
     * @return string 
     * @throws IGKException 
     */
    public static function GetResolveURI(string $routepattern, ?array $resolve=null, ?string $baseUri=null){
        $croute = "/" . ltrim($routepattern, "/");
        if (preg_match_all("/(?P<mark1>\/)?(\{\\s*(?P<name>" . IGK_IDENTIFIER_PATTERN . ")(?P<option>\\*)?\\s*\})(?P<mark2>\/)?/i", $croute, $tab)) {
            $count = 0;
            $optional = false;
            foreach ($tab["name"] as $i) {
                $c = trim($i);
                $s = $tab[0][$count];
                $opt = igk_getv($tab["option"], $count) == "*";
                $mark1 = igk_getv($tab["mark1"], $count);
                $mark2 = igk_getv($tab["mark2"], $count);
                
                if ($g = igk_getv($resolve, $c)) {
                    $rp = $g;
                    if ($mark1){
                        $rp = "/".$rp;
                    }
                   
                    $croute = str_replace($s, $rp, $croute);
                }
                $count++;
            }
        }
        if ($baseUri != null){
            $croute = $baseUri . $croute;
        }
        
        return  $croute ;
    }
    /**
     * add expression
     * @param string $name name to identifie expression
     * @param string $expression expression to use
     * @return RouteHandler 
     */
    private function addExpression(string $name, string $expression)
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
     * @return static 
     */
    public function auth($name, bool $strict=false)
    {
        $this->auth = $name;
        $this->auth_requirement = $strict;
        return $this;
    }
    /**
     * bind condition
     * @param mixed $id identie 
     * @param mixed $pattern regular expression
     * @return RouteHandler 
     */
    public function where(string $id, string $pattern)
    {
        return $this->addExpression($id, $pattern);
    }
    public function userRequired(bool $require){
        $this->user_required = $require;
        return $this;
    }
    /**
     * activate strict dir
     * @param bool $strict_dir 
     * @return $this 
     */
    public function strict_dir(bool $strict_dir){
        $this->strict_dir = $strict_dir;
        return $this;
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
     * @return static 
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
