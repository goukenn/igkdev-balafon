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
 

    public function __construct(string $dir){
        if (!file_exists($dir)){
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