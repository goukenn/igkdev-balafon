<?php

use IGK\Controllers\BaseController;
use IGK\System\Exceptions\LoadArticleException;
use IGK\System\IO\FileSystem;
use IGK\Helper\IO;
use IGK\System\Http\WebFileResponse;

require_once IGK_LIB_CLASSES_DIR . '/IGKAppConfig.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Configuration/ConfigUtils.php';
require_once IGK_LIB_CLASSES_DIR . '/System/Configuration/ConfigData.php';

/**
 * cache object 
 * @method static FileSystem view() view file system
 */
final class IGKCaches{
    /**
     * @var IGKCaches caches
     */
    private static $sm_instance;

    public static function getInstance(){
        if (self::$sm_instance === null){
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    private function __construct(){
    }   

    ////<summary>resolv path from link</summary>
    /**
     * resolv path from link
     * @param mixed $file filename to resolv
     * @return mixed 
     * @throws IGKException 
     */
    public static function ResolvPath($file){         
        return $file;
    }
    public static function HandleCache(){        
        if (igk_setting()->no_page_cache()){
            return false;
        }
        list($uri, $zip) = self::CacheUri();
        /// TODO:  Configs cache page attached to controller 
        // igk_wln_e("configs ::: ".igk_sys_configs()->default_controller);
   
        $file = IGKCaches::page_filesystem()->getCacheFilePath($uri);  
        if (file_exists($file) && IGKCaches::page_filesystem()->expired($uri, 50000)){
            // header("Content-Encoding: deflate");
            // readfile($file);
            
            $response = new WebFileResponse($file);
            $response->zip = $zip;
            $response->cache_output(igk_sys_configs()->get("cache_output", 3600));
            $response->output(); 
        }
    }

      /**
     * get system cache uri
     * @return (string|bool)[]  uri and zip flag 
     */
    public static function CacheUri($controller=null, ?string $requestUri=null){
        $o = "";
        if ($controller===null){ 
            $controller = igk_sys_configs()->default_controller;
        }
        if (is_string($controller)){
            $o.= $controller."/";
        }else if ( $controller instanceof BaseController){
            $o .= $controller->getName()."/";
        }
        if ($requestUri===null){
            $requestUri = igk_io_baseuri().igk_server()->REQUEST_URI;
        }
        $o .= strtolower(igk_environment()->keyname()).":";
        $uri = $o.$requestUri;
        $zip = igk_server()->accepts(["gzip"]);      
        if ($zip){
            $uri .= "_zip";
        }
        return [$uri, $zip];
    }
    /**
     * check if cache 
     * @param string $requestUri query string
     * @param string|IGK\Controllers\BaseController $controller 
     * @return bool 
     */
    public static function IsCachedUri(string $requestUri, $controller=null){
        list($uri, $zip) = self::CacheUri($controller, $requestUri); 
        $file = IGKCaches::page_filesystem()->getCacheFilePath($uri); 
        return file_exists($file);
    }
    public static function __callStatic($name, $args){
        $i = self::getInstance();
        if (isset($i->{$name})){
            return $i->{$name};
        }
        //+ init article to writes
        if (method_exists($i, $fc = "_init_{$name}_caches")){
            $o = $i->{$fc}();
            $i->{$name} = $o;
            return $o;
        }
        die("method not found. ".__CLASS__."::".$name);
    }
     /**
     * init and get javascript filesystem
     * @return FileSystem|null  
     */
    public static function article_filesystem(){
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * init and get javascript filesystem
     * @return FileSystem  
     */
    public static function page_filesystem(){
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * init and get css filesystem caching
     */
    public static function css_filesystem(){
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * init and get javascript filesystem
     * @return FileSystem  
     */
    public static function js_filesystem(){
        return self::__callStatic(__FUNCTION__, []);
    }
    /**
     * 
     * @param string $dir 
     * @return FileSystem|null 
     * @throws IGKException 
     */
    private static function __init_cache($dir){
        if (IO::CreateDir($dir)){
            return FileSystem::Create($dir);
        }
        return null;  
    }
    /**
     * 
     * @return FileSystem|null 
     * @throws IGKException 
     */
    private function _init_view_caches(){
        return self::__init_cache(igk_io_cachedir()."/storage/views"); 
    }
    /**
     * 
     * @return FileSystem|null 
     * @throws IGKException 
     */
    private function _init_article_filesystem_caches(){
        return self::__init_cache(igk_io_cachedir()."/storage/articles");// igk_environment()->getViewCacheDir());
    }
    private function _init_page_filesystem_caches(){
        return self::__init_cache(igk_io_cachedir()."/storage/pages");
    }
    private function _init_js_filesystem_caches(){
        return self::__init_cache(igk_io_cachedir()."/storage/js");
    }
    private function _init_css_filesystem_caches(){
        return self::__init_cache(igk_io_cachedir()."/storage/css");
    }
    public static function Compile($controller, $fs, $file, $raw, $key="FileBuilder", $render=1){
     
        igk_environment()->push($key, $file);
        
        $cache = $fs->getCacheFilePath($file);
        $n=igk_create_notagnode();
        if (1 || $fs->cacheExpired($file)){ 
           igk_wln_e("cahing .... ");
            // + |-----------------------------------------------
            // + | Compile the target 
            // + |-----------------------------------------------
            // $n->loadFile($controller, $file, $raw);
      
            $n->loadFile($file,null, ["ctrl"=>$controller, "raw"=>$raw]);
            // igk_html_article($controller, $file, $n, $raw, null, true, true);
        
            $option = igk_create_view_builder_option();
            $n->renderAJX($option);
            $s = ob_get_clean();
            igk_io_w2file($cache, trim($s).igk_view_builder_extra($file, $option));

            if($render){
                echo $s; 
            }
        }
        else { 
            $n->obdata(function()use($cache, $raw){
                try{
                    include($cache); 
                }
                catch(Exception $ex){
                    throw new LoadArticleException($ex, $ex);
                    igk_wln_e("exception :");
                }
                catch(Error $ex){
                    igk_wln_e("error :");
                } 
            });  
            if($render){
                $n->renderAJX();
            }
        }
        igk_environment()->pop($key);  
        return $n;
    }

    public static function Compile2($controller, $fs , $file, $raw, $key="FileLoader", $render=1){
        
       
        igk_environment()->push($key, $file);

        $cache = $fs->getCacheFilePath($file);
        
        if (1 || $fs->cacheExpired($file)){
            igk_wln_e("cache 2");
            // + |-----------------------------------------------
            // + | Compile the target 
            // + |-----------------------------------------------
            $n=igk_create_notagnode();
            $n->article($controller, $file, $raw);
            ob_start();
            $option = igk_create_view_builder_option();
            $n->renderAJX($option);
            $s = ob_get_clean();
            igk_io_w2file($cache, trim($s).igk_view_builder_extra($file, $option));
        }
         
        $n=igk_create_notagnode();
        $n->obdata(function()use($cache, $raw){
            try{
                include($cache); 
            }
            catch(Exception $ex){
                throw new LoadArticleException($ex);
                igk_wln_e("exception :");
            }
            catch(Error $ex){
                igk_wln_e("error :");

            } 
        });  
        if($render){
            $n->renderAJX();
        }
      
        igk_environment()->pop($key);

          // igk_environment()->push("FileLoader", $f);
        // $cache = $this->_cache_fs->getCacheFilePath($f);
        // $n=igk_create_notagnode();
        // if ($this->_cache_fs->cacheExpired($f)){
        //     // + |-----------------------------------------------
        //     // + | Compile the target 
        //     // + |-----------------------------------------------
        //     $n->addArticle($this->_controller, $f, $raw);
        //     ob_start();
        //     $n->renderAJX();
        //     $s = ob_get_clean();
        //     igk_io_w2file($cache, $s);
        //     if($render){
        //         echo $s; 
        //     }
        // }
        // else {
        //     $n->obdata(function()use($cache, $raw){
        //         try{
        //             include($cache); 
        //         }
        //         catch(Exception $ex){
        //             throw new LoadArticleException($ex, $ex);
        //             igk_wln_e("exception :");
        //         }
        //         catch(Error $ex){
        //             igk_wln_e("error :");

        //         } 
        //     });  
        //     if($render){
        //         $n->renderAJX();
        //     }
        // }
        // igk_environment()->pop("FileLoader");
    }
}