<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKWebApplication.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\Helper\IO;
use IGK\System\Diagnostics\Benchmark;
use IGK\System\Http\RequestHandler;
use IGK\System\Html\HtmlRenderer;


require_once IGK_LIB_CLASSES_DIR . "/IGKCaches.php"; 

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
        $config = igk_configs();
        // + | --------------------------------------------------------        
        // + | handle secure port 
        // + |
        if($config->force_secure_redirection && ($sport=$config->secure_port ?? 443) && (igk_server()->SERVER_PORT != $sport)){            
            igk_navto(igk_secure_uri(igk_io_fullrequesturi(), true, false));
            igk_exit();
        }
        // + | --------------------------------------------------------        
        // + | handle cache
        // + |
        IGKEnvironment::getInstance()->is("OPS") && $render && IGKCaches::HandleCache();
        
        // igk_ilog("handle cache.".session_id());
        
        igk_environment()->write_debug("include_web_request : ".igk_sys_request_time());
        $this->initlibrary();
       
        try {
            igk_environment()->write_debug("before request handle : ".igk_sys_request_time());            
            require_once IGK_LIB_DIR . "/igk_request_handle.php";
            // | ----------------------------------------------------
            // | backup index file 
            // | ----------------------------------------------------
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
                        IGKServer::getInstance()->prepareServerInfo();
                        RequestHandler::getInstance()->redirect($this, []);
                    } else {
                        igk_set_header($srv->REDIRECT_STATUS);
                    }
                    igk_exit();
                }                  
                RequestHandler::getInstance()->handle_route($path_info);
                // 
                // configuration handle
                //
                if (!igk_configs()->get("noWebConfiguration")){
                    (new IGK\System\Http\ConfigurationPageHandler(function(bool $display){
                        $this->runEngine($display);
                    }, $file))->handle_route($path_info);
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
                $_SERVER["ENV_FULL_REQUEST_URI"] = $q;
                IGKServer::getInstance()->prepareServerInfo();
                RequestHandler::getInstance()->redirect($this, $_redirectArgs);
                igk_exit();
            }

            $this->runEngine($render);
        } 
        catch (IGKException $ex){
      
            if (!igk_environment()->no_handle_error){
                IGK\Helper\ExceptionUtils::ShowException($ex);
            }else {
                throw $ex;
            }
        }
        catch (Exception $ex) {
            igk_environment()->set("LastException",  "Error: " . $ex->getMessage());
            IGK\Helper\ExceptionUtils::ShowException($ex);
        }
  
    }
    protected function initLibrary(){
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/IHtmlGetValue.php');
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
        require_once(IGK_LIB_CLASSES_DIR . '/Database/DbQueryDriver.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ControllerTypeBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/NonAtomicTypeBase.php');
        // dom libray loader
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlAttributeValue.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/IHeaderResponse.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlItemBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlScriptNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlRenderCallbackNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlNotifyResponse.php');
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
        require_once(IGK_LIB_CLASSES_DIR . '/Css/ICssStyleContainer.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Css/ICssSupport.php');

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
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlStyleValueAttribute.php');

        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/BaseController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/RootControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/IConfigController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/PageControllerBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ILibaryController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDocumentNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/IGKObjectGetProperties.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Http/Response.php');

        // extra load  
        require_once(IGK_LIB_CLASSES_DIR . '/Helper/ViewHelper.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Helper/UriHelper.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Helper/Activator.php');

        require_once(IGK_LIB_CLASSES_DIR . '/System/Collections/ArrayList.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Database/MySQL/Controllers/DbConfigController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlDialogFrameNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlChildArray.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlImgNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/HtmlAttributeArray.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlFormNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlFormInnerNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlFormTitleNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/FrameDialogController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/SubDomainController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Database/MySQL/DataAdapterBase.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/ConfigControllerRegistry.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Helper/SysUtils.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/SysDbController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/ConfigureController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/SessionController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/PicResConfigurationController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/ComponentManagerController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/DebugController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/UsersConfigurationController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/PaletteController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Configuration/Controllers/ControllerAndArticlesController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/UserGroupController.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/IO/File/PHPScriptBuilderUtility.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlBodyMainScript.php');
        require_once(IGK_LIB_CLASSES_DIR . '/Controllers/OwnViewCtrl.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlBodyBoxNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlCtrlNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlCoreJSScriptsNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/IO/StringBuilder.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlBodyInitDocumentNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlScriptNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlNoTagNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/SVG/SvgListIconNode.php');
        require_once(IGK_LIB_CLASSES_DIR . '/System/Html/SVG/SvgRenderer.php');
        require_once IGK_LIB_CLASSES_DIR . "/System/Http/RouteCollection.php";
        require_once IGK_LIB_CLASSES_DIR . "/System/Http/Route.php";
        require_once IGK_LIB_CLASSES_DIR . "/System/Configuration/Controllers/ConfigControllerBase.php"; 
        require_once IGK_LIB_CLASSES_DIR . "/Controllers/ApplicationModuleController.php";         
        require_once IGK_LIB_CLASSES_DIR ."/System/IO/File/PHPScriptBuilder.php";
        require_once IGK_LIB_CLASSES_DIR ."/IGKAppInfoStorage.php";
        require_once IGK_LIB_CLASSES_DIR ."/Controllers/ControllerPaths.php";
        require_once IGK_LIB_CLASSES_DIR ."/System/Html/Css/CssUtils.php";
        require_once IGK_LIB_CLASSES_DIR ."/Controllers/ViewLayoutLoader.php";
        require_once IGK_LIB_CLASSES_DIR ."/System/Http/IResponse.php";
        require_once IGK_LIB_CLASSES_DIR ."/Controllers/Loader.php";
        // require_once IGK_LIB_CLASSES_DIR ."/System/Http/IResponser.php";
        // ---------------------------------------------------------------------------
        // exposed
        //
        require_once(IGK_LIB_CLASSES_DIR . '/IGKHtmlRelativeUriValueAttribute.php');
    }
    private function runEngine($render = true)
    {
        // + | ------------------------------------------------------------
        // + | start engine index
        // + | ------------------------------------------------------------
        // 0.031s 
        // igk_wln(__FILE__.":".__LINE__, "measure first cache loading");
        // Benchmark::$Enabled = true;
        // Benchmark::getInstance()->dieOnError(true);
        // Benchmark::mark(__METHOD__);
        IGKApp::StartEngine($this);
        // Benchmark::expect(__METHOD__, 0.5);
        
        // + | Dfault Handle 
        RequestHandler::getInstance()->handle_ctrl_request_uri();
        if ($render) {
            // render document
            HtmlRenderer::RenderDocument(igk_app()->getDoc());
        }
    }
    public function bootstrap()
    {

        Benchmark::$Enabled = igk_environment()->isDev();
  
        // bootstrap web application
        // + initialize library
        $this->library("subdomain");
        $this->library("session");
        $this->library("mysql");
        $this->library("zip");
        $this->library("gd");
        $this->library("curl");


igk_reg_hook(IGKEvents::HOOK_CACHE_RES_CREATED, function($e){
    $fdir= igk_io_cacheddist_jsdir();
    $access=$fdir."/.htaccess";
    if(!file_exists($access)){
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
    if ($scripts = igk_environment()->get("ScriptFolder"))
    {
        $lib_res = IGK_LIB_DIR."/Scripts/";
        foreach($scripts as $d){
            foreach(igk_io_getfiles($d, $core_res_regex) as $res){
                if (strpos($res, $lib_res)===0){
                    $bres = $sdir."/".substr($res, strlen($lib_res));
                    if (IO::CreateDir(dirname($bres))){
                        igk_io_symlink(realpath($res),$bres);
                    }
                }
            }
        }
    } 
    igk_internal_reslinkaccess();
});
igk_reg_hook("generateLink", function(){
    igk_internal_reslinkaccess();
});
    }
}
