<?php

use IGK\System\Exceptions\LoadArticleException;
use IGK\System\IO\FileSystem;
use IGK\Helper\IO;

/**
 * chachage object 
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

    public static function Compile($controller, $fs, $file, $raw, $key="FileBuilder", $render=1){
     
        igk_environment()->push($key, $file);
        
        $cache = $fs->getCacheFilePath($file);
        $n=igk_createnotagnode();
        if ($fs->cacheExpired($file)){ 
           
            // + |-----------------------------------------------
            // + | Compile the target 
            // + |-----------------------------------------------
            // $n->loadFile($controller, $file, $raw);
      
            $n->loadFile($file,null, ["ctrl"=>$controller, "raw"=>$raw]);
            // igk_html_article($controller, $file, $n, $raw, null, true, true);
        
            $option = igk_create_view_builder_option();
            $n->renderAJX($option);
            $s = ob_get_clean();
            igk_io_w2file($cache, trim($s.igk_view_builder_extra($file, $option)));

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
            // + |-----------------------------------------------
            // + | Compile the target 
            // + |-----------------------------------------------
            $n=igk_createnotagnode();
            $n->article($controller, $file, $raw);
            ob_start();
            $option = igk_create_view_builder_option();
            $n->renderAJX($option);
            $s = ob_get_clean();
            igk_io_w2file($cache, trim($s.igk_view_builder_extra($file, $option)));
        }
         
        $n=igk_createnotagnode();
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
        // $n=igk_createnotagnode();
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