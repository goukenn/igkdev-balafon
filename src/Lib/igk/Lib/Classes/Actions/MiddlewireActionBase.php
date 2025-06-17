<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MiddlewireActionBase.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Actions;

use Exception;
use IGK\Helper\SysUtils;
use IGK\Models\Users;
use IGK\System\Http\RedirectRequestResponse;
use IGK\System\Http\Request;
use IGK\System\Http\Route;
use IGK\System\Http\RouteActionHandler;
use IGK\Actions\ActionBase;
use IGK\Actions\Traits\Authenticator\BearerAuthenticatorTrait;
use IGK\Helper\ActionHelper;
use IGK\Helper\StringUtility;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Helpers\AnnotationHelper;
use IGK\System\Http\Helper\Response;
use IGK\System\Http\RequestResponseCode;
use IGK\System\Http\Security;
use IGK\System\Http\StatusCode;
use IGK\System\IO\File\PHPDocCommentParser;
use IGK\System\IO\StringBlockReader;
use IGK\System\Security\Helpers\PhpDocCommentSecurityAndAuthUtility;
use IGKEvents;
use IGKException;
use Reflection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use function igk_resources_gets as __;

/**
 * Action Middleware
 * use to process method with specific checkMiddle - route 
 * @package IGK\Actions
 */
abstract class MiddlewireActionBase extends ActionBase implements IActionMiddleWare
{
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
    protected function checkMiddle()
    {

        $this->ctrl->checkUser(false);
        $u = Users::currentUser();
        if (!$u) {
            $token = null;

            if (in_array(BearerAuthenticatorTrait::class,  class_uses($this)) || method_exists($this, 'getUserFromToken')) {
                // retrieve user from token 
                if ($app_user = $this->getUserFromToken(true, $token)) {
                    if ($u = $this->userProfileFromApplicationUser($app_user)) {
                        $u = $u->model();
                    }
                }
            }
        }
        $this->user = $u;
        $token = null;

        if (empty($auths = $this->auths)) {
            return true;
        }
        if (!$this->user) {
            if ($this->redirect) {
                return new RedirectRequestResponse($this->ctrl::uri($this->redirect));
            }
            throw new IGKException("User Not found");
        }
        if (!$this->user->auth($auths)) {
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
        return (function($a){return $a;})(new static())->$name(...$arguments);
    }
    /**
     * magic core system to handle route definitions
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws IGKException 
     */
    public function __call($name, $arguments)
    {
        if ($rep = $this->checkMiddle()) {
            if (is_object($rep)) {
                return $rep;
            }
        }
        // + | load route configuration config
        Route::LoadConfig($this->ctrl);

        $path = "/" . implode("/", array_merge([$name], $arguments));
        $ruri = Request::getInstance()->view_args("entryuri") . $path;
        $routes = Route::GetAction(static::class);
        $method = strtolower(igk_server()->REQUEST_METHOD);

        $path =  "/" . trim($path, "/");
        // detected method to invoke
        $_invoke = function ($name, $arguments, $m, &$handle) use ($method) {
            if (($method == 'options') && !method_exists($this, $name . '_' . $method)) {
                return Response::OptionResponse();
            }
            $proc = ["_" . $method, ""];
            $handle = false;
            $v_user = $this->currentUser();
            $v_global_security = $v_global_auth = null;
            $v_global_strict = false;
            $td = new ReflectionClass($this);
            if ($comment = $td->getDocComment()){
                if ($v_global_security = PhpDocCommentSecurityAndAuthUtility::ParseComment($comment, $p)){
                    $v_global_auth = $p->auth;
                    $v_global_strict = $p->strict_auth;
                }
            }
            

            while ((count($proc) > 0) && (($f = array_shift($proc)) !== null)) {
                if (in_array($name . $f, $m)) {
                    $name = $name . $f;
                    $v_refmethod = new ReflectionMethod($this, $name);
                    // + | check for route not security 
                    if (!$v_user) {
                        self::_CheckMethodAccess($this, $v_refmethod, $v_global_security, $v_global_auth, $v_global_strict);
                    } else {
                        // + | 
                        self::_VerifMethodAccess($this, $v_refmethod, $v_user);
                    }
                    $handle = true;
                    $arguments =  Dispatcher::GetInjectArgs($v_refmethod, $arguments, []);
                    return $this->$name(...$arguments);
                }
            }
        };

        $_handling = function ($name, $arguments, $_invoke) {
            $handle = false;
            $r = $_invoke($name, $arguments, ActionHelper::GetExposedMethods(static::class), $handle);
            if ($handle) {
                return ['result' => $r];
            }
        };
        if (!empty($routes)) {
            $user = $this->user;

            if ($method == 'options') {
                if ($r = $_handling($name, $arguments, $_invoke)) {
                    return $r['result'];
                }
            }
            // + | --------------------------------------------------------------------
            // + | must use the route technique to validate the path
            // + | 
            $ctrl = $this->getController();
            foreach ($routes as $v) {
                if ($v->match($path, $method)) {
                    $redirect =  $v->getRedirectTo();
                    $security = $v->getSecurity();
                    if (!$user && $security) {
                        $ack = (object)[
                            'security' => $security,
                            'access' => false,
                            'controller' => $ctrl
                        ];
                        igk_hook(IGKEvents::HOOK_MIDDLEWARE_ACTION, $ack);
                        if (!$ack->access) {
                            throw new IGKException("User required security missing.", RequestResponseCode::Forbiden);
                        }
                        $ctrl->checkUser(false, null);

                        $this->user = $user = Users::currentUser();
                    }
                    if ($v->isUserRequired()) {
                        if (!$user) {
                            $m = "User required.";
                            $redirect && $this->_handle_redirect($redirect, 302, $m);
                            throw new IGKException("User required.", RequestResponseCode::Forbiden);
                        }
                    }
                    if ($v->isAuthRequired()) {
                        if ($user && !$v->isAuth($user)) {
                            $m = "Route access not allowed.";
                            $redirect && $this->_handle_redirect($redirect, 301, $m);
                            throw new ActionRequestException($m, RequestResponseCode::Forbiden);
                        } else if (!$user) {
                            $m = "Missing required user.";
                            $redirect && $this->_handle_redirect($redirect, 301, $m);
                            throw new ActionRequestException($m, RequestResponseCode::Unauthorized);
                        }
                    }
                    $v->setUser($user);
                    $v->setRoutingInfo((object)[
                        "ruri" => $ruri
                    ]);
                    if ($v->getBindClass() === null) {
                        // detected method to invoke 
                        if (is_numeric($name)) {
                            array_unshift($arguments, $name);
                            $name = 'index';
                        }
                        if ($r = $_handling($name, $arguments, $_invoke)) {
                            return $r['result'];
                        }
                        // no controller task setup
                        // return null;
                    }
                    // + | bind action
                    array_unshift($arguments, $name);
                    array_unshift($arguments, $this->ctrl);
                    //igk_wln_e($arguments);
                    return RouteActionHandler::Handle($v, ...$arguments);
                }
            }
            // + | --------------------------------------------------------------------
            // + | missing route : check that the view is present then do some with args
            // + |

            if ($r = $_handling($name, $arguments, $_invoke)) {
                return $r['result'];
            }
            // + | route not resolved 
            igk_dev_wln_e("route not resolved " . $path);
            throw new IGKException(__("Route {0} not resolved, in {1} ", $path, get_class($this)), 404);
        } else {
            // no definition foute found for this class suppose all method is accessible 
            if ($r = $_handling($name, $arguments, $_invoke)) {
                return $r['result'];
            }
        }
        $route = Route::GetMatchAll();
        return $this->invoke($route, $arguments);
    }
    /**
     * verif method security 
     * @param mixed $host 
     * @param ReflectionMethod $v_refmethod 
     * @param mixed $user 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _VerifMethodAccess($host, ReflectionMethod $v_refmethod, $user)
    {
        if ($security = self::_ParseSecurity($v_refmethod, $p)) {
            $ctrl = $host->getController();
            $reader = StringBlockReader::Annotation();
            $src =  $reader->read($security);
            $args = StringUtility::ReadArgs($src);
            $ack = (object)[
                'security' => null,
                'access' => false,
                'controller' => $ctrl,
                'user' => $user
            ];
            while (!$ack->access  && (count($args) > 0)) {
                $t = array_shift($args);
                if (!is_array($t))
                    $t = [$t];
                foreach ($t as $sec) {
                    $ack->security = $sec;
                    igk_hook(IGKEvents::HOOK_CHECK_MIDDLEWARE_ACCESS_TOKEN, $ack);
                    if ($ack->access) {
                        break;
                    }
                }
            }
            if (!$ack->access) {
                throw new IGKException("invalid token ", 500);
            }
        }
    }
    /**
     * parsing security info 
     * @param ReflectionMethod $v_refmethod 
     * @param mixed &$p 
     * @return mixed|void 
     */
    static function _ParseSecurity(ReflectionMethod $v_refmethod, & $p)
    {
        if ($comment = $v_refmethod->getDocComment()){
            return PhpDocCommentSecurityAndAuthUtility::ParseComment($comment, $p); 
        }
    } 

    /**
     * check method access no user 
     * @param mixed $host 
     * @param ReflectionMethod $v_refmethod 
     * @param ReflectionMethod $global_security 
     * @return never 
     * @throws Exception 
     */
    private static function _CheckMethodAccess($host, ReflectionMethod $v_refmethod, $global_security =null , $global_auth=null, $global_strict_auth=false)
    {
        $c_mid_key = IGKEvents::HOOK_MIDDLEWARE_ACTION;
        $auth = '';
        $p = null;
        $security = self::_ParseSecurity($v_refmethod, $p) ?? $global_security;
        if ($security) {
            if (is_null($p)){
                $p = (object)['security'=>$security, 'auth'=>$global_auth, 'strict_auth'=>$global_strict_auth];
            }
            $fc_auth = function ($e) {
                if ($e->args->access){
                    return;
                }
                $c = igk_server()->getAccessObject();
                if (is_null($c)) {
                    igk_json(['error'=>true, 'message'=>'require auth to access'], RequestResponseCode::Forbiden);
                    return;
                }
                $arg = $e->args;
                $login = $pwd = null;
                list($security, $controller) = igk_extract($arg, 'security|controller');
                $is_bearer = in_array($security, [Security::BEARER_AUTH]);
                $token = $is_bearer ? $c->getBearerToken() : $c->getBasicToken();

                if ($token){
                    list($login, $pwd) = explode(':', base64_decode($token), 2);
                }
                else {
                    if (in_array($security, [Security::BASIC_AUTH])){
                            list($login, $pwd) = $c::HandleBasicAuth();
                    }
                }
                if ($login && $pwd){
                    $connected = $controller->login($login, $pwd, false);
                    $e->args->access = $connected;
                }
            };
            igk_reg_hook($c_mid_key, $fc_auth);
            $auth = $p->auth;
            $strict = $p->strict_auth;
            $ctrl = $host->getController();
            $reader = StringBlockReader::Annotation();
            $src =  $reader->read($security);
            $args = StringUtility::ReadArgs($src);
            $ack = (object)[
                'security' => null,
                'access' => false,
                'controller' => $ctrl
            ];
            if ($auth){
                $auth = array_map(function($a)use($ctrl){
                    return $ctrl->authName($a);
                }, $auth);
            }
            while (!$ack->access  && (count($args) > 0)) {
                $t = array_shift($args);
                if (!is_array($t))
                    $t = [$t];
                foreach ($t as $sec) {
                    $ack->security = $sec;
                    igk_hook($c_mid_key, $ack);
                    if ($ack->access) {
                        break;
                    }
                }
            }
            igk_unreg_hook($c_mid_key, $fc_auth);

            if (!$ack->access) {
                throw new IGKException("Security issue. Missing User.", RequestResponseCode::Forbiden);
            }
            $ctrl->checkUser(false, false);
            $userProfile = $ctrl->userProfile;
            if ($auth && (($userProfile && !$userProfile->auth($auth, $strict)) || !$ctrl->getUser()->auth($auth, $strict))) {
                throw new IGKException("Security issue.", RequestResponseCode::Unauthorized);
            }
            return true;
        }
        return false;
    }

    /**
     * redirect code 
     * @param mixed $url 
     * @param mixed $code redirect code 301|302
     * @param mixed $message message for status
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _handle_redirect($url, $code = 301, $message = null)
    {
        igk_navto($url, $code, $message);
    }
    /**
     * invoke route fallback in case route definition not cached up
     * @param mixed $route 
     * @param mixed $args 
     * @return void 
     */
    protected function invoke($route, $args)
    {
        // + | --------------------------------------------------------------------
        // + | fallback route 
        // + |

        $view_exits = !empty($this->fname) && $this->getController()->getIsViewExists($this->fname);
        if ($view_exits) {
            // + | --------------------------------------------------------------------
            // + | let view handle the routes
            return null;
        }
        // + | --------------------------------------------------------------------
        // + | just throw an error
        // + |
        igk_dev_wln_e(
            __FILE__ . ":" . __LINE__,
            'invoke route/view definition - not found',
            static::class,
            $route,
            $args
        );
        return Response::BadRequest();
    }
}
