<?php 
 
namespace IGK\System\IO;

use IGK\System\Exceptions\ArgumentNotValidException; 

/**
 * file system helper 
 */
class FileSystem{
    /**
     * 
     * @var string base path of the file system
     */
    public $path; 
    public function __construct(string $dir){
        if (!file_exists($dir)){
            throw new ArgumentNotValidException("dir");
        }
        $this->path = $dir;
    }
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
     * @param string $path 
     * @param mixed $timespan 
     * @return bool 
     */
    public function expired(string $path, $timespan){
        return $this->lastModified($path) < $timespan;
    }
    /**
     * get path last modification
     * @param mixed $path 
     * @return int|false 
     */
    public function lastModified(string $path){
        $c = $this->getFullPath($path);        
        if (self::Exists($c)){       
            return filemtime($c);
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
     * @return FileSystem|null 
     */
    public static function Create(string $path){
        if (file_exists($path)){
            return new self($path);
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
}