<?php

namespace IGK\System\Http;

use Error;
use Exception;
use IGK\ApplicationLoader;
use IGK\Controllers\ApplicationController;
use IGK\System\Console\Commands\SitemapGeneratorCommand;
use IGK\System\Html\XML\XmlProcessor;
use IGK\XML\XSDValidator;
use IGK\System\Caches\EnvControllerCacheList;
use IGK\System\Caches\InitEnvControllerChain;
use IGK\System\IO\Path;
use IGK\Controllers\ApiController;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKApp;
use IGKAppSystem;
use IGKEnvironment;
use IGKEvents;
use IGKException;
use IGKSubDomainManager;
use IGKValidator;
use ReflectionException;

require_once __DIR__ . "/BaseUriHandler.php";
require_once IGK_LIB_CLASSES_DIR . '/ApplicationLoader.php';
/**
 * uri handler 
 * @package IGK\System\Http
 */
class UriHandler extends BaseUriHandler
{
    var $cacheoutput = 5000;
    protected function bootApp()
    {
        ApplicationLoader::getInstance()->bootApp($this->m_application);
    }

    /**
     * 
     */
    protected function __construct()
    {
        $this->m_routes = $this->initRoutes();
    }
    /**
     * initialize system route 
     */
    protected function initRoutes()
    {
        return [
            "/favicon.ico" => [$this, '_favicon'],
            "/sitemap.xml" => [$this, "_sitemap"],
            "/sitemap" => [$this, "_sitemap"],
            "/assets/Styles/balafon.css" => [$this, '_caching_style'],
        ];
    }
    /**
     * match an keys 
     * @param string $uri 
     * @param mixed &$key 
     * @return bool 
     */
    function match(string $uri, &$key = null): bool
    {
        $t = array_values($this->m_routes);
        while (count($t) > 0) {
            $s = array_shift($t);
            if (preg_match('#' . $s . '#i', $uri)) {
                $key = $s;
                return true;
            }
        }
        return false;
    }
    /**
     * facade the favicon 
     * @return never 
     * @throws IGKException 
     * @throws Exception 
     */
    protected function _favicon()
    {

        igk_set_header(
            200,
            'ok',
            [
                "Content-Type: image/png",
                "Cache-Control: max-age=31536000"
            ]
        );
        igk_header_cache_output($this->cacheoutput);
        include(IGK_LIB_DIR . "/Default/R/Img/balafon.ico");
        igk_exit();
    }
    public function _sitemap()
    {
        // if not loader boot application then get controller list  
        IGKSubDomainManager::Init();
        $_is_sub = IGKSubDomainManager::IsSubDomain();
        if (!$_is_sub) {
            $file = igk_io_cachedir() . "/.sitemap.cache";
            if (igk_io_file_exists($file)) {
                header("Content-Type: application/xml");
                echo file_get_contents($file);
                igk_exit();
            } else {
                $this->bootApp();
                $buri = igk_io_baseuri();
                $indexes = SitemapGeneratorCommand::GetProjectIndexes();
                $s = SitemapGeneratorCommand::GenerateSiteMapIndex($indexes, $buri);
                if ($s == -1) {
                    header("Content-Type: application/xml");
                    echo '<?xml version="1.0"?><sitemapindex></sitemapindex>';
                } else {
                    igk_io_w2file($file, $s);
                    igk_xml($s);
                }
            }
            igk_exit();
        }
        // + | --------------------------------------------------------------------
        // + | leave site map for handling by Project
        // + | 
    }

    /**
     * init caching style
     * @return void 
     * @throws IGKException 
     */
    public function _caching_style()
    {
        include IGK_LIB_DIR . '/igk_serve_static.php';
    }
    public static function RetrieveServerHost(&$r = null)
    {
        $g = parse_url(igk_server()->HTTP_HOST);
        list($host, $port, $path) = igk_extract($g, 'host|port|path');
        $r = compact('host', 'port', 'path');
        return $host ? $host : explode('/', $path, 2)[0];
    }
    /**
     * check subdmain. or OP Address 
     */
    protected final static function _CheckSubDomain(string $path, ?string $host=null)
    {
        \IGK\ApplicationLoader::InitConstants();
        $v_host = $host ?? self::RetrieveServerHost();
        if ($v_host && preg_match('/([a-z0-9\-_]+)(\.[a-z0-9\-_]+){2,}/', $v_host)) {

            if (IGKValidator::IsIpAddress($v_host)){                 
                $domain = $v_host;
            }else {
                $domain = implode('.', array_slice(explode('.', $v_host), 0, -2));
            }

            if (defined('IGK_SUBDOMAIN_URI_LIST')) {
                $g = constant('IGK_SUBDOMAIN_URI_LIST');
                if (is_string($g)) {
                    $t = explode(';', $g);
                    while (count($t) > 0) {
                        if ($q = array_shift($t)) {
                            if ($v_host == $q) {
                                return true;
                            }
                        }
                    }
                }
            } else {
                $v_domains = include(IGKSubDomainManager::GetConfigFile());
                return self::_GetDomainManagerEntry($v_domains, $domain, $path);
            }
            return true;
        }
        return false;
    }
    /**
     * check domains agains will cards. * replace by ".+" and .(dot) is escaped
     * @param array $domains 
     * @param string $domain 
     * @return mixed 
     */
    protected static function _GetAgaintsWillCard(array $domains, string $domain){
        $tc = array_keys($domains);
        $willcard = [];
        foreach($tc as $l){
            if (strpos($l, '*')){
                $willcard[] = $l;
            }
        }
        if ($willcard){
            rsort($willcard);
            foreach($willcard as $l){
                $x =  str_replace('*','.+', str_replace('.', '\.',$l)); 
                if (preg_match("/".$x."/", $domain)){
                    return $domains[$l];
                }
            }
        }
        return null;
    }
    /**
     * retrieve controller depending of controller domain manager - other controller will be after 
     * @param array $v_domains 
     * @param string $domain 
     * @return false|object|void 
     * @throws Exception 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected static function _GetDomainManagerEntry(array $v_domains, string $domain, string $path)
    {
        $v_pdir = igk_io_projectdir();
        if ($l = igk_getv($v_domains, $domain) ?? self::_GetAgaintsWillCard($v_domains, $domain)) {
            $rf = false;
            if (is_string($l)) {
                $rf = true;
                $ld = $l;
                $l = [$l];
                if (is_dir($ld = Path::Combine($v_pdir , $ld))) {
                    $l[] = $ld;
                }
            }
            list($classname, $location) = igk_extract($l, '0|1');
            if (!$location) {
                $location = Path::Combine($v_pdir , $classname);
            } else {
                $rp = igk_io_expand_path($location);
                if (!$rf && ($rp == $location)) {
                    $rp = Path::Combine($v_pdir , $rp);
                }
                $location = $rp;
            }
            if (!class_exists($classname, false)) {
                // load controller from environment  
                $location && igk_loadlib($location);
                $tab = EnvControllerCacheList::GetControllersClasses();
                if (in_array($l, $tab)) {
                    $p = igk_app();
                    $manager = $p->getControllerManager();
                    $c = new InitEnvControllerChain;
                    $c->load([$l], $manager, null);
                }
                $l = $classname;
            }
            if ($l && !is_subclass_of($l, ApiController::class)){                
                igk_reg_hook(IGKEvents::HOOK_APP_BOOT, function()use($l, $path){
                    $ctrl =  $l::ctrl(true);
                    $path = explode(';', ltrim($path, '/'), 2)[0];
                    list($view, $args) = ViewHelper::PrepareViewArgFromPath($path);  
                    $ctrl->setCurrentView($view, true, null, $args);
                 });
                return false;
            }
            $ctrl =  $l::ctrl(true);
            $dir = $ctrl->getDeclaredDir();
            return (object)compact('ctrl', 'domain', 'dir');
        }
    }
    /**
     * serve to handle Base URI request
     * @param string $uri
     * @param mixed $app
     * @param string $callaback
     * @param string|true $subdomain 
     */
    public static function Handle(string $uri, $app = null, ?callable $bootload = null, ?string  $subdomain = null)
    {
        $v_tab = parse_url($uri);
        $l = igk_getv($v_tab, 'path');
        if ('/assets/Styles/balafon.css' == $l) {
            igk_environment()->NoLoadAction = true;
            $uri = $l;
        } else {
            // : check handle subdomain or handle 
            // : CONCEPT - load direct controller as validator  
            $v_subdomain = $subdomain ?? self::_CheckSubDomain($l);
            // if (!$v_subdomain || !is_object($v_subdomain)) {
            //     $v_host = self::RetrieveServerHost(); 
            //     if (IGKValidator::IsIpAddress($v_host)) {
            //             $v_domains = include(IGKSubDomainManager::GetConfigFile());
            //             $v_subdomain = self::GetDomainManagerEntry($v_domains, $v_host);
            //     }
            //     igk_wln_e(__FILE__ . ":" . __LINE__, $v_tab, $v_subdomain);
            // }

            if (($v_subdomain) && is_object($v_subdomain)) {
                if ($bootload) {
                    $bootload();
                }
                $ctrl = $v_subdomain->ctrl;
                if ($ctrl instanceof \IGK\Controllers\ApiController) {
                    igk_reg_hook(IGKEvents::HOOK_ACTION_WILL_DO_ACTION, function ($e) use ($app) {
                        list($ctrl) = igk_extract($e->args, 'controller');
                        igk_environment()->set(IGKEnvironment::CURRENT_CTRL, $ctrl);
                        $ctrl->bootstrap($app);
                    });
                    $ctrl->requestHandleAction($l);
                } else {
                    $ctrl->view($l);
                }
            }
        }
        return parent::Handle($uri, $app, $bootload);
    }

    /**
     * 
     */
    public static function HandlePublicDir(string $path, ?string $cwd = null)
    {
        if (empty($tc = trim($path, '/'))) {
            return;
        }
        $tab = explode('/', $tc);
        $d = $td = $cwd ?? igk_io_basedir();
        $f = true;
        $v_start = true;
        while ((count($tab) > 0)) {
            if ($q = array_shift($tab)) {
                if (!is_dir($s  = $d . "/" . $q)) {
                    array_unshift($tab, $q);
                    $f = $v_start ? false : count($tab) > 0;
                    break;
                }
                $v_start = false;
                $d = $s;
            }
        }
        if ($f) {
            // passing to data 
            // $_SERVER['REQUEST_URI'] = "";  
            $target = '';
            $f = false;
            foreach (['index.php', 'index.phtml', 'main.php', 'main.phtml'] as $k) {
                if ($f = file_exists($target = $d . '/' . $k)) {
                    break;
                }
            }
            if ($f) {
                $_SERVER['REQUEST_URI'] = "/";
                $_SERVER['PATH_INFO'] = ($tab ? '/' . implode('/', $tab) : '');
                $_SERVER['SCRIPT_NAME'] =
                    $_SERVER['PHP_SELF'] = substr($target, strlen($td));
                igk_server()->prepareServerInfo();
                chdir(dirname($target));
                (function () {
                    require(func_get_args()[0]);
                    igk_exit();
                })($target);
            }
        }
    }
}
