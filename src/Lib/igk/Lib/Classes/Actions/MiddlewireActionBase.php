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
use IGK\Actions\ActionBase;
use IGK\Actions\Traits\Authenticator\BearerAuthenticatorTrait;
use IGK\Helper\ActionHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\Helper\Response;
use IGK\System\Http\RequestResponseCode;
use IGK\System\Http\StatusCode;
use IGKException;
use Reflection;
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
        return (new static())->$name(...$arguments);
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
            while ((count($proc) > 0) && (($f = array_shift($proc)) !== null)) {
                if (in_array($name . $f, $m)) {
                    $name = $name . $f;
                    $handle = true;
                    $arguments =  Dispatcher::GetInjectArgs(new ReflectionMethod($this, $name), $arguments, []);
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
            foreach ($routes as $v) {
                if ($v->match($path, $method)) {
                    $redirect =  $v->getRedirectTo();
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

        $view_exits = $this->getController()->getIsViewExists($this->fname);
        if ($view_exits) {
            // + | --------------------------------------------------------------------
            // + | let view handle the routes
            return null;
        }
        // + | --------------------------------------------------------------------
        // + | just trhow errors
        // + |
        igk_dev_wln_e(
            __FILE__ . ":" . __LINE__,
            'invoke route/view definition - rnot found',
            static::class,
            $route,
            $args
        );
        return Response::BadRequest();
    }
}
