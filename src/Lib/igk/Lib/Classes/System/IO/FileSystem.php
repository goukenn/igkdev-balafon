<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileSystem.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\IO;
use IGK\System\Exceptions\ArgumentNotValidException; 
require_once __DIR__."/CoreFileSystem.php"; 
/**
 * file system helper 
 */
class FileSystem extends CoreFileSystem{  
    /**
     * default extension 
     * @var mixed
     */
    var $default_extention;
    public function __construct(string $dir){
        if (!self::Exists($dir)){
            throw new ArgumentNotValidException("dir");
        }
        $this->path = $dir;
    }
    /**
     * path to check if exists
     * @param string $path 
     * @return bool 
     */
    public static function Exists(string $path):bool{ 
        return igk_io_file_exists($path, true);
    }
    /**
     * return FileSystem helper
     * @param string $path path 
     * @return FileSystem|null 
     */
    public static function Create(string $path){
        if (igk_io_file_exists($path, true)){
            return new static($path);
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
    public function getCacheFilePath(string $path, ?string $ext=".php"): string{
        if (is_null($ext)){
            $ext = $this->default_extention ?? '.php';
        }
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
     * @param string $path real file to check to filesystem resources
     * @param ?string $ext extension
     * @return bool 
     */
    public function cacheExpired(string $path, ?string $ext=".php"){
        $p = filemtime($path);
        if (is_file($file = $this->getCacheFilePath($path, $ext))){
            return filemtime($file) < $p;
        }
        return true;
    }
    /**
     * check that file expiere from cache storage
     * @param string $realpath_to_check 
     * @param string $caching_name 
     * @param string $ext 
     * @return bool 
     */
    public function checkNotExpired(string $realpath_to_check, string $caching_name, $ext='.php'){
        $p = filemtime($realpath_to_check);  
        $vn = $this->getCacheFilePath($caching_name, $ext);
        if (igk_io_file_exists($vn)){
            return filemtime($vn) > $p;
        }
        return false;
    }
}