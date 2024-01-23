<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCaches.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\Controllers\BaseController;
use IGK\System\Exceptions\LoadArticleException;
use IGK\System\IO\FileSystem;
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Http\WebFileResponse;

require_once IGK_LIB_CLASSES_DIR . '/IGKAppConfig.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Configuration/ConfigUtils.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Configuration/ConfigData.php';

///<summary>cache system support</summary>
/**
 * cache system 
 * @method static FileSystem view() view file system
 */
final class IGKCaches
{
    private $m_storage = [];
    /**
     * @var IGKCaches caches
     */
    private static $sm_instance;

    public static function getInstance()
    {
        if (self::$sm_instance === null) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    private function __construct()
    {
    }

    ////<summary>resolv path from link</summary>
    /**
     * resolv path from link
     * @param mixed $file filename to resolv
     * @return mixed 
     * @throws IGKException 
     */
    public static function ResolvPath($file)
    {
        return $file;
    }
    public static function HandleCache()
    {
        if (igk_setting()->no_page_cache()) {
            return false;
        }
        list($uri, $zip) = self::CacheUri(); 
        $expires = 50000; 
        if (!IGKCaches::page_filesystem()->expired($uri, $expires)) {
            // igk_wln_e("load from cache");
            $file = IGKCaches::page_filesystem()->getCacheFilePath($uri);
            $response = new WebFileResponse($file, "text/html");
            $response->zip = $zip;
            $response->cache_output(igk_configs()->get("cache_output", $expires));
            $response->output();
        }
    }

    /**
     * get system cache uri
     * @return (string|bool)[]  uri and zip flag 
     */
    public static function CacheUri($controller = null, ?string $requestUri = null)
    {
        
        $o = "";
        if ($controller === null) {
            if ($ctrl = igk_get_defaultwebpagectrl()) {
                $ctrl = igk_uri(get_class($ctrl));
            } else {
                $controller = igk_configs()->default_controller;
            }
        }
        if (is_string($controller)) {
            $o .= $controller . "/";
        } else if ($controller instanceof BaseController) {
            $o .= $controller->getName() . "/";
        }
        if ($requestUri === null) {
            $requestUri = explode("?", igk_io_baseuri() . igk_server()->REQUEST_URI)[0];
        }
        $o .= strtolower(igk_environment()->keyname()) . ":";
        $uri = $o . $requestUri;
        $zip = igk_server()->accepts(["gzip"]);
        if ($zip) {
            $uri .= "_zip";
        }
        return [$uri, $zip, $controller];
    }
    /**
     * check if cache 
     * @param string $requestUri query string
     * @param string|IGK\Controllers\BaseController $controller 
     * @return bool 
     */
    public static function IsCachedUri(string $requestUri, $controller = null)
    {
        list($uri, $zip) = self::CacheUri($controller, $requestUri);
        $file = IGKCaches::page_filesystem()->getCacheFilePath($uri);
        return file_exists($file);
    }
    public static function __callStatic($name, $args)
    {
        $i = self::getInstance();
        if (isset($i->m_storage[$name])) {
            return $i->m_storage[$name];
        }
        //+ init article to writes
        if (method_exists($i, $fc = "_init_{$name}_caches")) {
            $o = $i->{$fc}(...$args);
            // dynamic property is deprecated 
            // $i->{$name} = $o;
            $i->m_storage[$name] = $o;
            return $o;
        }
        die("method not found. " . __CLASS__ . "::" . $name);
    }
    /**
     * init and get javascript filesystem
     * @return FileSystem|null  
     */
    public static function article_filesystem()
    {
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * init and get javascript filesystem
     * @return FileSystem  
     */
    public static function page_filesystem()
    {
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * init and get css filesystem caching
     */
    public static function css_filesystem()
    {
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * init and get javascript filesystem
     * @return FileSystem  
     */
    public static function js_filesystem()
    {
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * 
     * @param BaseControleller $ctrl 
     * @return void 
     */
    private static function ctrl_filesystem(BaseController $ctrl){
        return self::__callStatic(__FUNCTION__, [$ctrl]);
    }
    /**
     * 
     * @param string $dir 
     * @return FileSystem|null 
     * @throws IGKException 
     */
    private static function __init_cache($dir)
    {
        if (IO::CreateDir($dir)) {
            return FileSystem::Create($dir);
        }
        return null;
    }
    /**
     * 
     * @return FileSystem|null 
     * @throws IGKException 
     */
    private function _init_view_caches()
    {
        return self::__init_cache(igk_io_cachedir() . "/storage/views");
    }
    private function _init_ctrl_filesystem_caches(BaseController $ctrl)
    {
        return self::__init_cache(igk_io_cachedir() . igk_uri("/storage/ctrl/".hash(IGKConstants::FILE_PATH_HASH_ALGO,
         $ctrl->name('cache'))));
    }
    /**
     * 
     * @return FileSystem|null 
     * @throws IGKException 
     */
    private function _init_article_filesystem_caches()
    {
        return self::__init_cache(igk_io_cachedir() . "/storage/articles"); // igk_environment()->getViewCacheDir());
    }
    private function _init_page_filesystem_caches()
    {
        return self::__init_cache(igk_io_cachedir() . "/storage/pages");
    }
    private function _init_js_filesystem_caches()
    {
        return self::__init_cache(igk_io_cachedir() . "/storage/js");
    }
    private function _init_css_filesystem_caches()
    {
        return self::__init_cache(igk_io_cachedir() . "/storage/css");
    }

    /**
     * check array file 
     * @param string[]|array $files files list to check
     * @param int $mtime time to check
     * @return bool 
     */
    public static function CheckCaches($files, int $mtime, & $file = null):bool
    {
        foreach ($files as $f) {
            if (!file_exists($f))
                continue;
            if (filemtime($f) > $mtime) {
                $file = $f;
                return true;
            }
        }
        return false;
    }
}
