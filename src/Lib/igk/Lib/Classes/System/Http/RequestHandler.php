<?php

namespace IGK\System\Http;

use Exception;
use IGK\Resources\R;
use IGKApp;
use IGKApplicationBase;
use IGKException;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Html\HtmlRenderer;

use function igk_resources_gets as __;


/**
 * base request handle
 * @package IGK\System\Http
 */
class RequestHandler
{
    private static $sm_instance;
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

    public function handle_ctrl_request_uri($u = null, $defaultBehaviour = 1)
    {
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
            $ctrl = igk_getctrl($c, false) ?? igk_template_create_ctrl($c);
            // igk_wln_e(__FILE__.":".__LINE__, "controller: ".$c, $ctrl, session_id());
            if (!$ctrl) {
                return null;
            }
            if (!method_exists(get_class($ctrl), $f)) {
                igk_html_output(404);
                igk_show_error_doc(null, 4046, null, "method not exists [" . get_class($ctrl) . "::" . $f . "]");
                igk_exit();
                return false;
            }
            if ($f == IGK_EVALUATE_URI_FUNC) {
                $app->setBaseCurrentCtrl($ctrl);
            }
            if (($f == IGK_EVALUATE_URI_FUNC) || $ctrl->IsFunctionExposed($f)) {
                $v_isajx = igk_is_ajx_demand() || IGKString::EndWith($f, IGK_AJX_METHOD_SUFFIX) || (igk_getr("ajx") == 1);
                $app->Session->URI_AJX_CONTEXT = $v_isajx;
                $fd = null;
                if (($fd = $ctrl->getConstantFile()) && file_exists($fd))
                    include_once($fd);
                if (($fd = $ctrl->getDbConstantFile()) && file_exists($fd))
                    include_once($fd);
                unset($fd);
                igk_environment()->set(IGK_ENV_REQUEST_METHOD, strtolower(get_class($ctrl) . "::" . $f));
                igk_environment()->set(IGK_ENV_INVOKE_ARGS, $args);
                if (is_array($arg))
                    call_user_func_array(array($ctrl, $f), $arg);
                else {
                    if ($arg)
                        $ctrl->$f($arg);
                    else {
                        $ctrl->$f();
                    }
                }
                igk_environment()->set(IGK_ENV_INVOKE_ARGS, null);
                igk_environment()->set(IGK_ENV_REQUEST_METHOD, null);
                if ($defaultBehaviour && $v_isajx) {
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
    }
    /**
     * handle redirect
     * @return void 
     */
    public function redirect(IGKApplicationBase $application, $args = [])
    {
        if (defined('IGK_REDIRECTION')){
            die("already call redirection");
        }
        define("IGK_REDIRECTION", 1);
       
        IGKApp::StartEngine($application, 0);

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

        // igk_html_pre($_SERVER);
        // exit;
        switch ($code) {
            case 901:
                // default redirect request handle
                // binding site pam 
                if ($redirect == "/sitemap.xml") {
                    igk_bind_sitemap(["ctrl"=>$defctrl, "c"=>"sitemap"]);
                    igk_exit();
                }
                /// TASK: handle query option on system command
                if ($this->handle_cmd_action($redirect)){
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
        if ($r == "POST" && ($code < 900)) {
            //DEBUG: Posted data are lost
            igk_is_debug() && igk_wln_e($_POST);
        }
        $v_ruri = igk_io_base_request_uri();
        $tab = explode('?', $v_ruri);
        $uri = igk_getv($tab, 0);
        $params = igk_getv($tab, 1);
        $page = $uri;
        $lang = null;


        if (($actionctrl = igk_getctrl(IGK_SYSACTION_CTRL)) && igk_io_handle_redirection_uri($actionctrl, $page, $params, 1))
            return;
        try {
            if (igk_sys_ispagesupported($page)) {
                $tab = $_REQUEST;
                igk_resetr();
                $_REQUEST["p"] = $page;
                $_REQUEST["l"] = $lang;
                $_REQUEST["from_error"] = true;
                $app->ControllerManager->InvokeUri();
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
                    $doc = igk_get_document("RedirectError");
                    $doc->body->setNoDefaultMainPage(true);
                    $h = igk_createnode("div");
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
    public function handle_cmd_action(string $redirect){
        $rx = "#^(" . igk_io_baseUri() . ")?\/!@(?P<type>" . IGK_IDENTIFIER_RX . ")\/(\/)?(?P<ctrl>" . IGK_FQN_NS_RX . ")\/(?P<function>" . IGK_IDENTIFIER_RX . ")(\/(?P<args>(.)*))?(;(?P<query>[^;]+))?$#i";
        $c = preg_match_all($rx, $redirect, $ctab);
        if ($c > 0) {
            igk_getctrl(IGK_SYSACTION_CTRL)->invokePageAction($ctab["type"][0], $ctab["ctrl"][0], $ctab["function"][0], $ctab["args"][0]);
            return true;
        }
    }
}
