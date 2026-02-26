<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RouteHandler.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;
use IGK\Actions\Dispatcher;
use IGK\System\Regex\MatchPattern;
use IGKException;
use ReflectionMethod;
/**
 * represent a route handler object
 * @package 
 */
class RouteHandler
{

    /**
    * Property: user.
    * @var mixed
    */
    protected $user;

    /**
    * Property: info.
    * @var mixed
    */
    protected $info;
    /**
     * name for searching
     * @var mixed
     */
    protected $name;
    /**
     * route type
     * @var string
     */
    private $route_type = 'controller';
    /**
     * get verbs
     * @var array
     */
    protected $verbs = [];
    /**
     * authorisation string
     * @var string|array
     */
    protected $auth;
    /**
     * route path pattern
     * @var string
     */
    protected $path;
    /**
     * attach secutiry on route 
     * @var null|'BasicAuth'|'BearerAuth'
     */
    protected $security;
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
    /**
     * redirect path 
     * @var ?string
     */
    protected $m_redirect_uri;

    /**
    * Property: auth requirement.
    * @var mixed
    */
    protected $auth_requirement;

    /**
    * Returns Route.
    */
    public function getRoute()
    {
        return $this->route;
    }

    /**
    * Returns Path.
    */
    public function getPath()
    {
        return $this->path;
    }

    /**
    * Returns Verbs.
    */
    public function getVerbs()
    {
        return $this->verbs;
    }

    /**
    * Sets Route.
    * @param mixed $route
    */
    protected function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }
    /**
     * get if auth is required
     * @return bool 
     */

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

    /**
    * Returns Routing Info.
    * @param null|mixed $name
    */
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

    /**
    * Sets User.
    * @param mixed $user
    */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    /**
     * get the security
     * @return null|'BasicAuth'|'BearerAuth' 
     */

    public function getSecurity(){
        return $this->security;
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
     * return route type
     */

    public function getType()
    {
        return $this->route_type;
    }
    /**
     * construct route
     * @param string $path 
     * @param mixed $controller 
     * @return void 
     */

    public function __construct(string $path, $controller)
    {
        $this->path = $path;
        $this->controller = $controller;
    }
    /**
     * get if user required;
     * @return bool 
     */

    public function isUserRequired(){     
        return $this->user_required;
    }
    /**
     * get if match with the verbs
     * @param mixed $path 
     * @param string $verb 
     * @return bool
     * @throws Exception 
     */

    public function match($path, $verb = 'GET', string $defaultEntryMethod='index'):bool
    { 
        // + match verb
        if (!in_array(strtoupper($verb), $this->verbs)) {
            return false;
        }        
        $regex = $this->getPatternRegex($defaultEntryMethod);   
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
     * check that the path is accessible 
     * @param string $path 
     * @return bool 
     */

    public function isAccessible(string $path, string $defaultEntryMethod=Route::DEFAULT_ENTRY_METHOD):bool{
        // if (!$this->m_expressions){
        //     return false;
        // }
        // each expression must be optional 
        $regex = static::GetRouteRegex($this->path, null,true, $defaultEntryMethod);
        return preg_match($regex, $path);
    }
    /**
     * retrieve pattern regex expression
     * @return string 
     * @throws Exception 
     */

    protected function getPatternRegex(string $defaultEntryMethod= Route::DEFAULT_ENTRY_METHOD): string
    {
        return static::GetRouteRegex($this->path, $this->m_expressions ?? [], true, $defaultEntryMethod);
    }
    /**
     * 
     * @param string $type 
     * @return string 
     */

    public static function GetTypePattern(string $type):string{
        return igk_getv([
            'guid'=>MatchPattern::Guid,
            'sguid'=>MatchPattern::ShortGuid,
            'int'=>MatchPattern::Int,
            'float'=>MatchPattern::Float,
            'single'=>MatchPattern::Single,
        ], strtolower($type), '[^/]+');
    }
    /**
     * 
     * @param string $path 
     * @param null|array $expressions 
     * @param bool $strict_dir 
     * @return string 
     */

    public static function GetRouteRegex(string $path, ?array $expressions=null, bool $strict_dir = true, 
        ?string $defaultEntryMethod=Route::DEFAULT_ENTRY_METHOD,
        ?string $format=null): string{
        $cbroute = $croute = "/" . ltrim($path, "/");
        $uoffset = 0;
        $cout = '';
        $all_optional = is_null($expressions);
        $format= $format ??  ($all_optional ? "#^%s#": "#^%s$#"); 

        if (preg_match_all("/(?P<mark1>\/)?(\{\\s*(?P<name>" . IGK_IDENTIFIER_PATTERN . ")(?P<option>\\*)?\\s*(?::(?P<type>[a-zA-Z][a-zA-Z0-9]*))?\})(?P<mark2>\/)?/i", $croute, $tab, PREG_OFFSET_CAPTURE)) {
            $count = 0;
            $optional = false;
            $expressions = $expressions ?? [];
    
            foreach ($tab["name"] as $i) {
                $c = trim($i[0]);
                $s = $tab[0][$count][0];
                $roffset = $tab[0][$count][1];
                $opt = $all_optional || (igk_getv(igk_getv($tab["option"], $count), 0) == "*");
                $mark1 = igk_getv(igk_getv($tab["mark1"], $count), 0);
                $mark2 = igk_getv(igk_getv($tab["mark2"], $count), 0);
                $type =  igk_getv(igk_getv($tab["type"], $count), 0);
               

                if ($g = igk_getv($expressions, $c, ".*")) {
                    if ($g == ".*"){
                        if (!$all_optional && $type){
                            $g = self::GetTypePattern($type);
                        } else {
                            $g = "[^/]+";
                        }
                    }                   
                    $rp = "(?P<".$c.">" . $g . ")";
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
                   // $croute = str_replace($s, $rp, $croute);
                }
                if (($count==0) && ($roffset==0)){
                    $cout .= '/'.$defaultEntryMethod;
                }
                $count++;
                $cout .= substr($cbroute, $uoffset, $roffset-$uoffset).$rp;
                $uoffset = $roffset + strlen($s);
            } 
            
        }
        $cout .= substr($cbroute, $uoffset);
        $croute = $cout;
        if (!$strict_dir){
            if (strrpos($croute, "(/)",-3) !== false){
                $croute .= "?";
            }
        }
        return sprintf($format, $croute );
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
     * set authorisation key name
     * @param bool|string|array $name bool|string|array of authorisation condition
     * @param bool $strict authorisation requirement
     * @return static 
     */

    public function auth($name, bool $strict=true)
    {
        $this->auth = $name;
        $this->auth_requirement = $strict;
        return $this;
    }

    /**
    * Security.
    * @param mixed $name
    */
    public function security($name){
        if (is_null($name) || in_array($name, ['BearerAuth','BasicAuth']))
            $this->security = $name;
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

    /**
    * User required.
    * @param bool $require
    */
    public function userRequired(bool $require){
        $this->user_required = $require;
        return $this;
    }
    /**
     * redirect in case of authentication failed
     * @param mixed $url 
     * @return void 
     */

    public function redirectTo(string $url){
        $this->m_redirect_uri = $url;
        return $this;
    }

    /**
    * Returns Redirect To.
    */
    public function getRedirectTo(){
        return $this->m_redirect_uri;
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
     * @param array|string $verb 
     * @return static 
     */

    public function verbs($verb)
    {
        if (is_string($verb)){
            $verb = explode("|", $verb);
        }
        return $this->setVerb($verb);
    }

    /**
    * Processes.
    * @param mixed ...$arguments
    */
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