<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CoreFileSystem.php
// @date: 20220803 13:48:55
// @desc: 
 
 
namespace IGK\System\IO;

// igk_trace();
// igk_wln_e("basic");

/**
 * core file
 * @package IGK\System\IO
 */
abstract class CoreFileSystem{

     /**
     * 
     * @var string base path of the file system
     */
    public $path; 
    
    /**
     * check if path is dir
     * @return bool 
     */
    public function isDir(){
        return is_dir($this->path); 
    }
    /**
     * check if path is file
     * @return bool 
     */
    public function isFile(){
        return is_file($this->path);
    }

    /**
     * check if path is expired
     * @param string $path path to cache in file system
     * @param mixed $timespan duration of the cache
     * @return bool 
     */
    public function expired(string $path, $timespan){
        $path = $this->getCacheFilePath($path);
        if (($lm = $this->lastModified($path)) === false){
            return true;
        } 
        return  $timespan < (time()-$lm);
    }
    /**
     * get path last modification
     * @param mixed $path 
     * @return int|false 
     */
    public function lastModified(string $path){
        $c = $path;
        if (self::Exists($c)){   
            return @filemtime($c);
        }
        return false;
    }
    /**
     * path to check if exists
     * @param string $path 
     * @return bool 
     */
    public static function Exists(string $path):bool{
        return file_exists($path);
    }
    /**
     * return FileSystem helper
     * @param string $path path 
     * @return static|null 
     */
    public static function Create(string $path){
        if ((static::class != self::class) && file_exists($path)){
            $m = new static($path);
            return $m;
        }
        return null;
    }
    /**
     * return the directory 
     * @return string 
     */
    private function _getDir(): string{
        $dir = $this->path;
        if ($this->isFile()){
            $dir = dirname($dir);
        }
        return $dir;
    }
    /**
     * Cache file utility
     * @param string $path path to add
     * @param string $ext extension to add to path
     * @return string cache path
     */
    public function getCacheFilePath(string $path, string $ext=".php"): string{
        return implode(DIRECTORY_SEPARATOR, [$this->_getDir(), sha1($path).$ext]);
    }
    /**
     * return the full path
     */
    public function getFullPath(string $path): string {
        return implode(DIRECTORY_SEPARATOR, array_filter([$this->_getDir(), $path]));
    }

    /**
     * check if path expired 
     * @param string $path 
     * @return bool 
     */
    public function cacheExpired(string $path){
        $p = filemtime($path);
        if (file_exists($file = $this->getCacheFilePath($path))){
            return filemtime($file) < $p;
        }
        return true;
    }

//       /**
//      * check that file expiere from cache storage
//      * @param string $realpath_to_check 
//      * @param string $caching_name 
//      * @param string $ext 
//      * @return bool 
//      */
//     public function checkNotExpired(string $realpath_to_check, string $caching_name, $ext='.php'){
//         $p = filemtime($realpath_to_check);  
//         $vn = $this->getCacheFilePath($caching_name, $ext);
//         if (file_exists($vn)){
//             return filemtime($vn) < $p;
//         }
//         return false;
//     }
}