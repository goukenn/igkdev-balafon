<?php

namespace IGK\System\Http;

use Exception;
use IGK\Resources\R;
use IGKApp;
use IGKApplicationBase;
use IGKException;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Html\Dom\HtmlDefaultMainPage;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Http\Routes;
use IGKApplication;
use IGKEvents;

use function igk_resources_gets as __;


/**
 * base request handle
 * @package IGK\System\Http
 */
class RequestHandler
{
    private static $sm_instance;
    private $m_ctrl_request;
    /**
     * handle current context
     * @var mixed
     */
    var $context;

    /**
     * 
     * @return RequestHandler instance 
     */
    public static function getInstance()
    {
        if (self::$sm_instance === null)
            self::$sm_instance = new self();
        return self::$sm_instance;
    }

    private function __construct()
    {
    }

    /**
     * get if request handler handle the controller request
     * @param string $uri 
     * @return false 
     * @throws IGKException 
     */
    public static function IsHandling(string $uri){        
        return $uri == self::getInstance()->m_ctrl_request;
    }
    /**
     * 
     * @param mixed $path 
     * @param IGK\System\Http\Routes|null #Parameter#ebc966f5 
     * @return void 
     */
    public function handle_route($path, ?RouteCollection $routes = null)
    {
        $this->context = ['type'=>'handle_route', 'uri' => $path];
        $route_file = \IGK\System\IO\Path::getInstance()->getDataDir() . "/routes.php";
        if (!file_exists($route_file))
            return;
      
        if ($routes == null) {
            $routes = new RouteCollection();
            // + | --------------------------------------------
            // + | include route files and build route mecanism
            //
            include($route_file);
            $routes = Route::GetRoutes();
        }
     
        $user = null;
        if (empty($routes))
            return;
  
   
        $arguments = [];
        foreach ($routes as $v) {

            if ($v->match($path, igk_server()->REQUEST_METHOD)) {
                if ($user && !$v->isAuth($user)) {
                    throw new IGKException("Route access not allowed");
                }
                $v->setUser($user);

                // $v->setRoutingInfo((object)[
                //     "ruri" => $path,
                //     "args"=>[]
                // ]);
                $arguments = array_filter(explode("/", $path));
                
                $api = IGKApplication::Boot('api');
                // start engine require for 
                IGKApp::StartEngine($api, false);
                return RouteHandler::Handle($v, $arguments); 
            }
        } 
    }
    /**
     * system handle request uri
     * @param mixed|null $u 
     * @return void 
     * @throws IGKException 
     */
    public function handle_uri($u = null)
    {
        if (igk_environment()->get("sys://notsystemurihandle")) {
            return;
        }
        $u = urldecode($u ?? \igk_io_base_request_uri());
        if (is_object($u)) {
            if (!igk_sys_env_production())
                igk_die("u is object ");
            return;
        }
        $uri_key = "sys://reg/systemuri";
        $b = igk_environment()->get($uri_key);
        if ($b && $u && isset($b[$u])) {
            if (is_callable($fc = $b[$u])) {
                ob_clean();
                $fc();
            }
        } else {
            if ($b) {
                foreach ($b as $k => $v) {
                    $p = null;
                    if (preg_match("/\[%q%]/i", $k)) {
                        $sk = str_replace("[%q%]", "(\?(:query))?", $k);
                    } else
                        $sk = $k;
                    $p = igk_sys_ac_create_pattern(null, null, $sk);
                    if ($p->matche($u)) {
                        $request = explode("?", $u)[0];
                        if (!isset($b[$request])) {
                            $b[$request] = $v;
                            igk_environment()->set($uri_key, $b);
                        }
                        igk_ob_clean();

                        if (PHP_VERSION > "8.0.0") {
                            call_user_func_array($v, array_values($p->getQueryParams()));
                        } else
                            call_user_func_array($v, $p->getQueryParams());
                        break;
                    }
                }
            }
        }
        $this->handle_ctrl_request_uri($u);
    }
    /**
     * handle application request uri
     * @param mixed $u 
     * @param int $defaultBehaviour 
     * @return int|void|null|false 
     * @throws IGKException 
     */
    public function handle_ctrl_request_uri($u = null, $defaultBehaviour = 1)
    {
        
        if (igk_environment()->handle_ctrl_request){
            return 1;
        }
        if (igk_environment()->get("sys://notsystemurihandle"))
            return;
        $c = igk_getr("c");
        $f = igk_getr("f");
 
        $app = igk_app();
        
        if ($c && $f) {
            $f = str_replace("-", "_", $f);
            $arg = array();
            $args = igk_getquery_args($u);
            $arg = igk_io_arg_from($f);
            $ctrl = igk_getctrl($c, false); // + | ?? igk_template_create_ctrl($c); 
            if (!$ctrl) {
                return null;
            }
            if (!method_exists(get_class($ctrl), $f)) {
                igk_set_header(404);
                igk_show_error_doc(null, 4046, null, "method not exists [" . get_class($ctrl) . "::" . $f . "]");
                igk_exit();
                return false;
            }
            if ($f == IGK_EVALUATE_URI_FUNC) {
                $app->setBaseCurrentCtrl($ctrl);
            }
            if (($f == IGK_EVALUATE_URI_FUNC) || $ctrl->IsFunctionExposed($f)) {
                $v_isajx = igk_is_ajx_demand() || IGKString::EndWith($f, IGK_AJX_METHOD_SUFFIX) || (igk_getr("ajx") == 1);
                igk_environment()->isAJXDemand = $v_isajx;
                $app->Session->URI_AJX_CONTEXT = $v_isajx;
                $fd = null;
                if (($fd = $ctrl->getConstantFile()) && file_exists($fd))
                    include_once($fd);
                if (($fd = $ctrl->getDbConstantFile()) && file_exists($fd))
                    include_once($fd);
                unset($fd);
                igk_environment()->set(IGK_ENV_REQUEST_METHOD, strtolower(get_class($ctrl) . "::" . $f));
                igk_environment()->set(IGK_ENV_INVOKE_ARGS, $args);
                self::getInstance()->m_ctrl_request = $ctrl->getUri($f);
                $response = null;
                if (is_array($arg))
                    $response = call_user_func_array(array($ctrl, $f), $arg);
                else {
                    if ($arg)
                        $response = $ctrl->$f($arg);
                    else {
                        $response = $ctrl->$f();
                    }
                }
                self::getInstance()->m_ctrl_request = null; 
                igk_environment()->set(IGK_ENV_INVOKE_ARGS, null);
                igk_environment()->set(IGK_ENV_REQUEST_METHOD, null);
                if ($defaultBehaviour && $v_isajx) {
                    igk_hook(IGKEvents::HOOK_AJX_END_RESPONSE, []);
                    igk_environment()->isAJXDemand = null;
                    igk_do_response($response);
                    igk_exit();
                }
                $app->Session->URI_AJX_CONTEXT = 0;
                unset($_REQUEST["c"]);
                unset($_REQUEST["f"]);
            } else {
                $msg = __("No method access: {0}", "[" . $c . "::" . $f . "]");
                if (!igk_sys_env_production()) {
                    igk_notifyctrl()->addWarning($msg);
                }
                igk_ilog($msg);
            }
        }
        igk_environment()->handle_ctrl_request = 1;  
    }
    /**
     * handle redirect
     * @return void 
     */
    public function redirect(IGKApplicationBase $application, $args = [])
    {
        igk_environment()->write_debug("Redirect start : ".igk_sys_request_time());

        if (defined('IGK_REDIRECTION')) {
            die("already call redirection");
        }
        define("IGK_REDIRECTION", 1);

        IGKApp::StartEngine($application, 0);
        igk_environment()->write_debug("Redirect engine start : ".igk_sys_request_time());
        $defctrl = igk_get_defaultwebpagectrl();

        $server_info = (object)array();
        foreach (array(
            "REQUEST_URI" => '',
            "SERVER_PROTOCOL" => '',
            "REDIRECT_STATUS" => '',
            "REDIRECT_URL" => '',
            "REDIRECT_REQUEST_METHOD" => 'GET',
            "REDIRECT_QUERY_STRING" => ''
        ) as $k => $v) {
            $server_info->$k = igk_getv($_SERVER, $k, $v);
        }
        extract($args);
        // igk_wln_e("base jumpe", $defctrl);
        $app = igk_app();
        // $code = igk_getv($_REQUEST, "__c", 902);
        $code = igk_getv($_REQUEST, "__c", 901);
        $query = $server_info->{'REQUEST_URI'};
        $redirect = $server_info->{'REDIRECT_URL'};
        $redirect_status = $server_info->{'REDIRECT_STATUS'};
        $r = $server_info->{'REDIRECT_REQUEST_METHOD'};
        igk_sys_handle_res($query);
       // igk_wln_e("code", $code, $redirect, $redirect_status, $_SERVER);
        switch ($code) {
            case 901:
                // default redirect request handle
                // binding site pam 
                if ($redirect == "/sitemap.xml") {
                    igk_bind_sitemap(["ctrl" => $defctrl, "c" => "sitemap"]);
                    igk_exit();
                }
                /// TASK: handle query option on system command
                if ($this->handle_cmd_action($redirect)) {
                    igk_exit();
                } 
                break;
            case 904:
                header("Status: 404");
                header("HTTP/1.0 404 Not Found");
                igk_exit();
                break;
            case 403:
              
                igk_set_header($code);
                igk_sys_show_error_doc($code);
                igk_exit();
                break;
            case 404:
                if (igk_getr("m") == "config") {
                    igk_navto("/Configs");
                    igk_exit();
                }
                break;
        }
        $args = igk_getquery_args($server_info->{'REDIRECT_QUERY_STRING'});
        $_REQUEST = array_merge($_REQUEST, $args);
        if (($r == "POST") && ($code < 900)) {
            //DEBUG: Posted data are lost
            igk_is_debug() && igk_wln_e($_POST);
        }
        $v_ruri = igk_io_base_request_uri();
        $tab = explode('?', $v_ruri);
        $uri = igk_getv($tab, 0);
        $params = igk_getv($tab, 1);
        $page = $uri;
        $lang = null;

        if (($actionctrl = igk_getctrl(IGK_SYSACTION_CTRL)) && $actionctrl->handle_redirection_uri($page, $params, 1))
            return;   
        try {
            if (igk_sys_ispagesupported($page)) {
                $tab = $_REQUEST;
                igk_resetr();
                $_REQUEST["p"] = $page;
                $_REQUEST["l"] = $lang;
                $_REQUEST["from_error"] = true;
                $app->getControllerManager()->InvokeUri();
                HtmlRenderer::RenderDocument();
                igk_exit();
            }
        } catch (\Exception $ex) {
        }
        if (!empty($page) && ($page != "/")) {
            $dir = getcwd() . "/Sites/" . $page;
            if (is_dir($dir)) {
                chdir($dir);
                R::ChangeLang($lang);
                !defined('IGK_APP_DIR') && define('IGK_APP_DIR', $dir);
                igk_wln_e("dir : ::: ", $dir, "page " . $page);
                include("index.php");
                igk_exit();
            }
        }
        $page = $uri;
        if ($defctrl !== null) {
            if ($defctrl->handle_redirection_uri($page)) {
                igk_exit();
            }
        }
        ///TASK: HANDLE RESOURCES

        $suri = $server_info->{'REQUEST_URI'};
        if (preg_match("/\.(jpeg|jpg|bmp|png|gkds)$/i", $suri)) {
            header("Status: 301");
            header($server_info->{'SERVER_PROTOCOL'}
                . " 301 permanent");
            igk_exit();
        }
        try {
            if (!($p = igk_get_defaultwebpagectrl())) {
                igk_set_header(404);
                if (file_exists($file = igk_env_file(igk_io_applicationdir() . "/" . IGK_INC_FOLDER . "/error.404"))) {
                    include($file);
                } else {
                    HtmlDefaultMainPage::getInstance()->setIsVisible(false);
                    $doc = igk_get_document("RedirectError"); 
                    $h = igk_create_node("div");
                    $h->div()->container()->panel()->h1()->Content = __("Page not found");
                    $doc->body->clearChilds()->div()->add($h);
                    $doc->title = __("ERROR");
                    $doc->renderAJX();
                    $doc->dispose();
                    igk_exit();
                }
            } else {
            }
        } catch (Exception $ex) {
            igk_show_error_doc();
        }
    }

    ///<summary>handle command application command action</summary>
    /**
     * handle command application command action
     */
    public function handle_cmd_action(string $redirect)
    {
        $rx = "#^(" . igk_io_baseUri() . ")?\/!@(?P<type>" . IGK_IDENTIFIER_RX . ")\/(\/)?(?P<ctrl>" . IGK_FQN_NS_RX . ")\/(?P<function>" . IGK_IDENTIFIER_RX . ")(\/(?P<args>(.)*))?(;(?P<query>[^;]+))?$#i";
     
        $c = preg_match_all($rx, $redirect, $ctab);
        if ($c > 0) {
            igk_getctrl(IGK_SYSACTION_CTRL)->invokePageAction($ctab["type"][0], $ctab["ctrl"][0], $ctab["function"][0], $ctab["args"][0]);
            return true;
        }
    }

    /**
     * handle guid action 
     * @param string $guid 
     * @param array|string $query 
     * @param null|string $version 
     * @return void 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function handle_guid_action(string $guid, $query = null, ?string $version = "")
    {
        igk_header_no_cache();
        $uri = igk_io_request_entry();
        $key = igk_get_component_uri_key($guid);
        $tab = igk_app()->session->regUris;
        $handle = false;
        $routes = igk_app()->session->Routes;
        $index = array_search($key, $routes);
        $obj = null;
        $args = $query;
        if (is_string($query))
            $args = explode("/", $query);
        if (!empty($index)) {
            $obj["class"] = $index;
            if ($loader = igk_getv($tab[$key], "loader")){
                if ($loader = igk_getctrl($loader, false)){
                    $obj["loader"] = $loader;
                    $loader::register_autoload();
                }
            }
        } else if ($tab && isset($tab[$key])) {
            $obj = $tab[$key];
        }
        if (is_array($obj)) {
            $tclass =  explode("/::", $obj["class"]);
            $class = array_shift($tclass);
            $tclass = implode("", $tclass);

            if (strpos($class, "m:") === 0) {
                $mod = str_replace(".", "\\", substr($class, 2));
                $mod_instance = igk_require_module($mod);
                $method = igk_getv($args, 0, "handle");
                $args = array_slice($args, 1);
                if ($ob = call_user_func_array([$mod_instance, $method], $args)) {
                    igk_do_response($ob);
                }
                igk_set_header(500);
                igk_wln_e(__("failed to handle module action"));
            }
            // if ($ctrl=igk_getctrl(IGK_CONF_CTRL, false)){
            //     $ctrl::register_autoload();
            // }
            if (!class_exists($class)) {
                igk_set_header(500, "temp class not found");
                igk_wln_e("RequestHandler::Class not exists {$class} ", 
                igk_ob_get_func('igk_html_pre', [compact("tclass", "index", "obj", "routes", "tab")]));
            }
            if (
                is_subclass_of($class, BaseController::class)
                && ($ctrl = igk_getctrl($class, false))
            ) {
                $ctrl::register_autoload();
                R::RegLangCtrl($ctrl);
            } else {
                $tclass = null;
                $ctrl = new $class();
            }
            $method = "index";

            if (
                !empty($tclass) &&
                class_exists($tclass)
            ) {
                $cl = new $tclass($ctrl);
            } else {
                $cl = $ctrl; //new $class();
            }
            if (count($args) > 0) {
                if (method_exists($cl, $args[0])) {
                    $method = $args[0];
                    $args = array_slice($args, 1);
                }
            }
            if (method_exists($cl, $method)) {
                ob_start();
                if (!igk_do_response($ob = call_user_func_array(array($cl, $method), $args))) {
                    igk_wl(ob_get_clean());
                } else {
                    ob_end_clean();
                }
            } else {
                igk_wln("method not found");
                igk_set_header(500, "function not found");
            }
            igk_exit();
        }
        $cl = null;
        $b = json_decode($tab[$key]);
        if ($b)
            $cl = $b->classpath;

        if (!empty($cl) && class_exists($cl, false) && !empty($query)) {
            $g = new $cl($b);
            $args = explode("/", $query);
            ob_start();
            $ob = call_user_func_array(array($g, $args[0]), array_slice($args, 1));
            ob_end_clean();
            igk_wl($ob);
        }

        if (igk_getr("__clear")) {
            igk_app()->session->regUris = null;
        }
        igk_set_header(500);
        if (igk_environment()->is("DEV")) {
            igk_die(__("failed to handle component action"), 1 , 500);
        }
        igk_exit();
    }
}
