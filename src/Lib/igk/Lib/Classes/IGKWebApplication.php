<?php

use IGK\System\Http\RequestHandler;
use IGK\Helper\StringUtility;
use IGK\System\Html\HtmlRenderer;

class IGKWebApplication extends IGKApplicationBase
{

    /**
     * return application
     * @param string $file 
     * @param int $render 
     * @return mixed 
     * @throws IGKException 
     */
    public function run(string $file, $render = 1)
    {

        if (!file_exists($file)) {
            throw new IGKException("Operation Not Valid");
        }


        $bdir = dirname($file);
        $this->file = $file;
        // prepare server info
        $srv = IGKServer::getInstance();

        // handle cache
        IGKEnvironment::getInstance()->is("OPS") && $render &&  IGKCaches::HandleCache();


        require_once(IGK_LIB_CLASSES_DIR . '/IGKObject.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKAttribute.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKSystemUriActionPatternInfo.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKEvents.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKAppSystem.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Helper/IO.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/IO/FileWriter.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Database/DataAdapterBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Database/SQLDataAdapter.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/RootControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/BaseController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/IConfigController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/ConfigControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Database/DbQueryDriver.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ControllerTypeBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/NonAtomicTypeBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlItemBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKControllerTypeAttribute.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/NonVisibleControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ILibaryController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/PageControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ApplicationController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/DefaultPageController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/FormBuilderEngine.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ToolControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlComponentNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKCtrlZone.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKDbUtility.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Cache/SystemFileCache.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKSubDomainManager.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKValidator.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKAppContext.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKAppSetting.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKAppConfig.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/ConfigUtils.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKAppType.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/ConfigData.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKControllerManagerObject.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKSession.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/SystemUriActionController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/MenuController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Resources/R.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ControllerExtension.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlUtils.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlContext.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKObjectGetProperties.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDocumentNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKHtmlDoc.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlHeadNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlBodyNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDocTheme.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKCssDefaultStyle.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/XML/XmlNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDocThemeMediaType.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKMedia.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKHtmlScriptManager.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKFv.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/GlobalScriptManagerHostNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlItemAttribute.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlCssLinkNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlCssClassValueAttribute.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/Request.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Resources/IGKLangResDictionary.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/ControllerConfigurationData.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlReader.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlReaderDocument.php');
        require_once(IGK_LIB_CLASSES_DIR . '/XML/XMLNodeType.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlReaderBindingInfo.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlOptions.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/Response.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/RequestResponse.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/WebResponse.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlRenderer.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlHeadBaseUriNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlFaviconNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlHookNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKOb.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Database/DataAdapterBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/RootControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/BaseController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/IConfigController.php');

        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/PageControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ILibaryController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Cache/CommonCache.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDocumentNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKObjectGetProperties.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/RequestHandler.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKCaches.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKViewActionsConstants.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/NotificationController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKNotifyStorage.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlSingleNodeViewerNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlNotificationItemNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlMetaManager.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKRawDataBinding.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/SystemController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKValueListener.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlANode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlAHref.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlStyleValueAttribute.php');

        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/BaseController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/RootControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/IConfigController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/PageControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ILibaryController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDocumentNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKObjectGetProperties.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/Response.php');

        require_once IGK_LIB_DIR . "/igk_request_handle.php";



        // backup index file 
        $_redirectArgs = ["igk_index_file" => $file];
        $v_path = 0;
        $redirect = 0;
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
                if (IGKServer::getInstance()->accepts(["gzip", "deflate"])) {
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
            IGKServer::getInstance()->prepareServerInfo();
        }


        if (!defined("IGK_REDIRECTION") && (($v_path = isset($_SERVER["PATH_INFO"])) || !empty($redirect = urldecode($srv->REDIRECT_URL)))) {
            // igk_sys_handle_uri(); 
            // igk_wln_e(__FILE__.":".__LINE__. "<pre>". igk_ob_get($_SERVER)."</pre>", $srv->REDIRECT_URL, "redirecting", $redirect);

            if ($srv->REDIRECT_STATUS != '200') {
                // ----------------------------------------------
                // on igkdev.com redirect Error document handling
                // ----------------------------------------------
                if ($redirect == "/Lib/igk/igk_redirection.php") {

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
                        $_SERVER["REQUEST_URI"] = $q;
                    }
                    IGKServer::getInstance()->prepareServerInfo();
                    RequestHandler::getInstance()->redirect($this, []);
                } else {
                    igk_set_header($srv->REDIRECT_STATUS);
                }
                igk_exit();
            }


            if ($redirect) {
                $path_info = $redirect;
            } else {
                $path_info = $_SERVER["PATH_INFO"];
            }


            $g = array_slice(explode("/", ($path_info)), 1);
            if (strtolower($g[0]) == strtolower(IGK_CONFIG_PAGEFOLDER)) {
                define('IGK_REDIRECTION', 0);
                if (!defined("IGK_CONFIG_PAGE"))
                    define("IGK_CONFIG_PAGE", 1);
                define("IGK_CURRENT_PAGEFOLDER", IGK_CONFIG_PAGEFOLDER);
                $script = $_SERVER["SCRIPT_NAME"];
                $dir = igk_str_rm_last(igk_html_uri(dirname($script)), '/');
                if (empty($dir) && $v_path) {
                    $dir .= $script;
                }
                $level = count($g) - 1;
                igk_io_set_dir_level($level);
                if (!empty($query = igk_server()->QUERY_STRING)) {
                    $query = "?" . $query;
                }
                $rq_path = implode("/", array_slice($g, 1));
                if (!empty($rq_path)) {
                    $rq_path = "/" . $rq_path; // .$query;
                }

                // igk_wln_e("query : ", $query,   $_SERVER["REQUEST_URI"]);
                // $_SERVER["REQUEST_URI"]=$dir."/".IGK_CONFIG_PAGEFOLDER."{$rq_path}";
                unset($_SERVER["PHP_SELF"]); //=$dir."/".IGK_CONFIG_PAGEFOLDER."/DTA";
                IGKServer::getInstance()->prepareServerInfo();
                $this->runEngine(false);
                if (file_exists(IGK_APP_DIR . "/Data/no_config")) {
                    igk_set_header("403");
                    igk_navto(igk_io_baseuri());
                }
                igk_sys_config_view($file);
                igk_exit();
            }


            if (!defined("IGK_REDIRECT_ACCCESS") && in_array($path_info, $access_file)) {
                if (file_exists($cfile = igk_html_uri(dirname(dirname(IGK_LIB_DIR)) . $path_info))) {
                    define("IGK_REDIRECT_ACCCESS", 1);
                    $_SERVER["SCRIPT_FILENAME"] = igk_str_rm_last(igk_server()->DOCUMENT_ROOT, "/") . dirname(igk_server()->SCRIPT_NAME) . $path_info;
                    IGKServer::getInstance()->prepareServerInfo();
                    include_once($cfile);
                    igk_exit();
                }
            }

            $_SERVER["REDIRECT_STATUS"] = '200';
            $_SERVER["REDIRECT_URL"] = $path_info;
            $_SERVER["REDIRECT_REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
            $_SERVER["REDIRECT_QUERY_STRING"] = $_SERVER["QUERY_STRING"];
            $dir = igk_str_rm_last(igk_html_uri(dirname($_SERVER["SCRIPT_NAME"])), '/');
            $q = $path_info;
            if (!empty($_SERVER["QUERY_STRING"]))
                $q .= "?" . $_SERVER["QUERY_STRING"];
            $_SERVER["REQUEST_URI"] = $q;
            IGKServer::getInstance()->prepareServerInfo();
            RequestHandler::getInstance()->redirect($this, $_redirectArgs);
            igk_exit();
        }
        try {
            $this->runEngine($render);        
        } catch (Exception $ex) {
            echo "Error: " . $ex->getMessage();
        }
    }
    private function runEngine($render=true){
        // + | ------------------------------------------------------------
        // + | start engine index
        // + | ------------------------------------------------------------
        // 0.031s 
        IGKApp::StartEngine($this);
            // + | Dfault Handle 
        RequestHandler::getInstance()->handle_ctrl_request_uri();
        if ($render) {
            // render document
            HtmlRenderer::RenderDocument(igk_app()->getDoc());
        }
    }


    public function render_default_uri()
    {
        igk_wln("render default ");
    }
}
