<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKWebApplication.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\System\Applications;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\Helper\ExceptionUtils;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\System\Diagnostics\Benchmark;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Http\RequestHandler;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Http\ConfigurationPageHandler;
use IGK\System\Http\IRequestFileHandler;
use IGK\System\Http\RequestException;
use IGKApp; 
use IGKApplicationBase;
use IGKApplicationBootOptions; 
use IGKEvents;
use IGKException; 
use ReflectionException;
use TypeError;

require_once IGK_LIB_CLASSES_DIR . "/IGKCaches.php";

/**
 * application web controller 
 * @package 
 */
class WebApplication extends IGKApplicationBase implements IRequestFileHandler
{
    protected $file;

    public function getEntryfile(){
        return $this->file;
    }
    private function runEngine($render = true)
    {
        throw new NotImplementException(__METHOD__);        
    }
    /**
     * bootstrap application
     * @param mixed $bootoptions 
     * @param callable $loader bootstrap load
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function bootstrap($bootoptions = null, ?callable $loader=null)
    {
        // - |
        // - | clean previously set header - 
        // - | 
        if (!igk_environment()->isDev()) {
           //
            header_remove(null); 
        }   
        // + | before init application dispatch to uri handler         
        IGKApp::Init();
        // + | must setup application before call the facade 
        $uri_handler = \IGK\System\Facades\Facade::GetFacade(\IGK\System\Http\UriHandler::class);
        isset($_SERVER["REQUEST_URI"]) && $uri_handler && $uri_handler::Handle($_SERVER["REQUEST_URI"], $this);
        
        // enable benchmark        
        Benchmark::Activate(
            igk_environment()->isDev() && igk_getr(Benchmark::REQUEST_PARAM),
            ["dieOnError" => ini_get("display_errors")]
        );
        // resource management
        require_once IGK_LIB_CLASSES_DIR.'/Resources/R.php';
        require_once IGK_LIB_DIR.'/Lib/functions-helpers/translation.php';
        require_once IGK_LIB_DIR.'/Lib/functions-helpers/db.php';

   
   
        // bootstrap web application
        // + initialize library
        $this->library("subdomain");
        $this->library("session");
        $this->library("mysql");
        $this->library("zip");
        $this->library("gd");
        $this->library("curl");
        if ($loader){
            $loader(); 
            if (!file_exists(igk_io_applicationdir()."/Data/configure")){          
                igk_initenv(igk_io_applicationdir(), igk_app());
            }
        }
        igk_reg_hook(IGKEvents::HOOK_CACHE_RES_CREATED, function ($e) {
            $fdir = igk_io_cacheddist_jsdir();
            $access = $fdir . "/.htaccess";
            if (!file_exists($access)) {
                IO::CreateDir(dirname($access));
                igk_io_w2file($access, implode("\n", array(
                    "allow from all",
                    "AddType text/javascript js",
                    "AddEncoding deflate js",
                    "<IfModule mod_headers.c>",
                    "Header set Cache-Control \"max-age=31536000\"",
                    "</IfModule>"
                )));
            }
            $sdir = dirname($e->args["dir"]);
            $core_res_regex = "/\.(json|xml|jpeg|png|svg)$/i";
            if ($scripts = igk_environment()->get("ScriptFolder")) {
                $lib_res = IGK_LIB_DIR . "/Scripts/";
                foreach ($scripts as $d) {
                    foreach (igk_io_getfiles($d, $core_res_regex) as $res) {
                        if (strpos($res, $lib_res) === 0) {
                            $bres = $sdir . "/" . substr($res, strlen($lib_res));
                            if (IO::CreateDir(dirname($bres))) {
                                igk_io_symlink(realpath($res), $bres);
                            }
                        }
                    }
                }
            }
            igk_internal_reslinkaccess();
        });
        igk_reg_hook(IGKEvents::HOOK_MK_LINK, function () {
            igk_internal_reslinkaccess();
        }); 
        // + | --------------------------------------------------------------------
        // + | boot modules
        // + |
        igk_reg_hook(IGKEvents::HOOK_APP_BOOT, function(){
            \IGK\System\Modules\ModuleManager::Bootstrap();
        });

        if ($bootoptions) {
            $options = Activator::CreateNewInstance(IGKApplicationBootOptions::class, $bootoptions);
            if ($c = $options->controller) {
                $this->setDefaultController($c);
            }
        }
   
    }
    /**
     * shortcut to set system default controller
     * @param null|BaseController $controller 
     * @return void 
     * @throws IGKException 
     */
    public function setDefaultController(?BaseController $controller)
    {
        igk_app()->getControllerManager()->setDefaultController($controller);
    }

    /**
     * 
     * @param string $file entry file
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws TypeError 
     * @throws RequestException 
     * @throws Exception 
     */
    public function handleRequest(string $file, bool $render=true)
    {  
        $srv = igk_server();
        $requestHandler = RequestHandler::getInstance();
        // 1. handle controller first
        // $requestHandler->handle_ctrl_request_uri();

        $_redirectArgs = ["igk_index_file" => $file];
        $access_file = ["/Lib/igk/igk_init.php"];
        $ch = "";
        $_start_index = key_exists("PHP_SELF", $_SERVER) && ($ch = $_SERVER["PHP_SELF"]) && in_array($ch, ["/", "/" . basename($file)]);
        if (!$_start_index && file_exists($f =  dirname($file) . $ch) && !is_dir($f)) {
            $ext = igk_io_path_ext(basename($f));
            if ($ext == "php") {
                include($f);
                igk_exit();
            }
            if (strstr(realpath($f), igk_io_cachedir() . "/dist/js/")) {
                //deflate header                
                if ($srv->accepts(["gzip", "deflate"])) {
                    header("Content-Encoding: deflate");
                }
            }
            igk_header_set_contenttype($ext);
            readfile($f);
            igk_exit();
        }

        //--------------------------------------------------------------
        // | handle php-fpm
        //--------------------------------------------------------------
        if (!$_start_index && igk_server()->FCGI_ROLE == "RESPONDER") {
            $_SERVER["REDIRECT_URL"] = $_SERVER["SCRIPT_URL"];
            $_SERVER["REDIRECT_QUERY_STRING"] = $_SERVER["SCRIPT_URL"];
            $_SERVER["REDIRECT_STATUS"] = "200";
            $srv->prepareServerInfo();
        }

        //--------------------------------------------------------------
        // | handle redirection
        //--------------------------------------------------------------        
        if (!defined("IGK_REDIRECTION") && (($path_info = $srv->PATH_INFO) || !empty($path_info = urldecode($srv->REDIRECT_URL ?? "")))) {
            if ($srv->REDIRECT_URL && ($srv->REDIRECT_STATUS != '200')) {
                // ----------------------------------------------
                // on igkdev.com redirect Error document handling
                // ----------------------------------------------
                if ($path_info == "/Lib/igk/igk_redirection.php") {

                    $q = $srv->SCRIPT_URL;
                    $_SERVER["REDIRECT_URL"] = $q;
                    $_SERVER["REDIRECT_REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
                    $_SERVER["REDIRECT_QUERY_STRING"] = igk_getv($_SERVER, "QUERY_STRING");
                    $_SERVER["REDIRECT_STATUS"] = $srv->REDIRECT_STATUS;
                    if (!empty($q)) {
                        if (!empty($_SERVER["QUERY_STRING"])) {
                            $q .= "?" . $_SERVER["QUERY_STRING"];
                        }
                        $_SERVER["REDIRECT_URL"] = $q;
                        $_SERVER["ENV_FULL_REQUEST_URI"] = $q;
                    }
                    $srv->prepareServerInfo();
                    $requestHandler->redirect($this, []);
                } else {
                    igk_set_header($srv->REDIRECT_STATUS);
                } 
                igk_exit();
            }
        
            $requestHandler->handle_route($path_info);
            // + |-------------------------------------------------------
            // + | configuration handle
            // + |
            if (!igk_environment()->noWebConfiguration()) { 

                (new  ConfigurationPageHandler(function (bool $display) {
                    // $this->runEngine($display);                    
                }, $file))->handle_route($path_info, function()use($requestHandler, $path_info, $_redirectArgs){
                    $this->_redirectUri($requestHandler, $path_info, $_redirectArgs);  
                });
            }
            if (!defined("IGK_REDIRECT_ACCCESS") && in_array($path_info, $access_file)) {
                if (file_exists($cfile = igk_uri(dirname(dirname(IGK_LIB_DIR)) . $path_info))) {
                    define("IGK_REDIRECT_ACCCESS", 1);
                    $_SERVER["SCRIPT_FILENAME"] = igk_str_rm_last(igk_server()->DOCUMENT_ROOT, "/") . dirname(igk_server()->SCRIPT_NAME) . $path_info;
                    $srv->prepareServerInfo();
                    include_once($cfile);
                    igk_exit();
                }
            }
            $this->_redirectUri($requestHandler, $path_info, $_redirectArgs);     
            igk_exit();
        }
        // : hanling core request uri
        RequestHandler::getInstance()->handle_ctrl_request_uri(); 
    }
    /**
     * redirect path uri
     * @param mixed $requestHandler 
     * @param mixed $path_info 
     * @param mixed $_redirectArgs 
     * @return void 
     * @throws IGKException 
     */
    private function _redirectUri($requestHandler, $path_info, $_redirectArgs){
        $_SERVER["REDIRECT_STATUS"] = '200';
        $_SERVER["REDIRECT_URL"] = $path_info;
        $_SERVER["REDIRECT_REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
        $_SERVER["REDIRECT_QUERY_STRING"] = $_SERVER["QUERY_STRING"];
        $q = $path_info;
        if (!empty($_SERVER["QUERY_STRING"]))
            $q .= "?" . $_SERVER["QUERY_STRING"];
        $_SERVER["ENV_FULL_REQUEST_URI"] = $q;
        igk_server()->prepareServerInfo();
        $requestHandler->redirect($this, $_redirectArgs);
    }
    /**
     * run application
     * @param string $file 
     * @param int $render 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws TypeError 
     */
    public function run(string $file, $render = 1)
    { 
        $this->file = $file;
        // + | secure port -- config  
        SysUtils::SecurePort(); 
        // + | handle cache
        // igk_environment()->isOPS() && $render && IGKCaches::HandleCache();
        try {
            $uri=igk_io_fullrequesturi();
            // if(igk_io_handle_system_command($uri)){
            //     igk_exit();
            // } 
            RequestHandler::HandleRequestUri($uri, $this, true, $file, $render);
           // $this->handleRequest($file, $render);
            if ($render) {
                HtmlRenderer::RenderDocument(igk_app()->getDoc());       
                igk_exit();
            }
        } catch (IGKException $ex) {
            if (!igk_environment()->no_handle_error) {
                ExceptionUtils::ShowException($ex);
            } else {
                throw $ex;
            }
        } catch (Exception $ex) {
            igk_environment()->set("LastException",  "Error: " . $ex->getMessage());
            ExceptionUtils::ShowException($ex);
        }
    }
}
