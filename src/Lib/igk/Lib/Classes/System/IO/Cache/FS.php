<?php
// @author: C.A.D. BONDJE DOUE
// @file: FS.php
// @date: 20250520 06:35:43
namespace IGK\System\IO\Cache;
use Exception;
use IGK\System\IO\StringBuilder;
use IGKException;
/**
* system file caches
* @package IGK\System\IO\Cache
* @author C.A.D. BONDJE DOUE
*/
class FS{
    private $m_caches;
    private $m_auto_cache;
    public static function CacheFile(){
        return igk_io_cachedir()."/.fs-caches.php";
    }
    /**
     * file cache exists
     * @param string $file 
     * @return bool 
     */
    public function fileExists(string $file, bool $autocheck=false){
        $l = isset($this->m_caches[$file]);
        // igk_dev_wln($file, $autocheck);
        // if (!$autocheck && strstr($file, 'configs.php')){
        //  if (!$autocheck ){
        //     igk_wln($file);
        //     igk_trace();
        //     igk_exit();
        // }
        if (!$l && ($autocheck || $this->m_auto_cache)){
            if ($l = file_exists($file)){
                $this->m_caches[$file] = $file;     
                if ($autocheck && !$this->m_auto_cache){
                    $this->m_auto_cache = true;
                     $this->_registerStoreCache(); 
                }           
            } else{
                return false;
            }
        }
        return $l;
    }
    /**
     * check for file exists
     * @param string $file 
     * @return bool 
     */
    public function checkExists(string $file):bool{
        if (!$this->fileExists($file)){
            if (file_exists($file)){
                $this->m_caches[$file] = igk_realpath($file);                
            } else{
                return false;
            }
        }
        return true;
    }
    public function loadCache(){
        $this->m_caches = ($c = @include self::CacheFile()) === false ? [] : $c;
        if ($c===false){
            $this->m_auto_cache = true;
            $this->_registerStoreCache(); 
        }
    }
    protected function _registerStoreCache(){
         register_shutdown_function(function(){
                $this->storeCache();
            });
    }
    /**
     * 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     */
    public function storeCache(){
        $sb = new StringBuilder;
        $ch ='';
        foreach($this->m_caches as $k=>$v){
            $sb->appendLine($ch.'"'.$k.'"=>"'.$v.'"');
            $ch = ',';
        }
        igk_io_w2file(self::CacheFile(), igk_cache_array_content($sb.''));
    }
}