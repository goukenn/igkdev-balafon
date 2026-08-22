<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileSystem.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\IO;

use IGK\System\Exceptions\ArgumentNotValidException;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKEvents;

require_once __DIR__ . "/CoreFileSystem.php";
/**
 * file system helper 
 */
class FileSystem extends CoreFileSystem
{
    /**
     * default extension 
     * @var mixed
     */
    var $default_extension;
    /**
     * .ctr
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        if (!self::Exists($dir)) {
            throw new ArgumentNotValidException("dir");
        }
        $this->path = $dir;
    }
    /**
     * path to check if exists
     * @param string $path 
     * @return bool 
     */
    public static function Exists(string $path): bool
    {
        return igk_io_file_exists($path, true);
    }
    /**
     * return FileSystem helper
     * @param string $path path 
     * @return FileSystem|null 
     */
    public static function Create(string $path)
    {
        if (igk_io_file_exists($path, true)) {
            return new static($path);
        }
        return null;
    }
    /**
     * return the directory 
     * @return string 
     */
    private function _getDir(): string
    {
        $dir = $this->path;
        if ($this->isFile()) {
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
    public function getCacheFilePath(string $path, ?string $ext = ".php"): string
    {
        if (is_null($ext)) {
            $ext = $this->default_extension ?? '.php';
        }
        return implode(DIRECTORY_SEPARATOR, [$this->_getDir(), sha1($path) . $ext]);
    }
    /**
     * return the full path
     * @param string $path
     */
    public function getFullPath(string $path): string
    {
        return implode(DIRECTORY_SEPARATOR, array_filter([$this->_getDir(), $path]));
    }
    /**
     * check if path expired 
     * @param string $path real file to check to filesystem resources
     * @param ?string $ext extension
     * @return bool 
     */
    public function cacheExpired(string $path, ?string $ext = ".php")
    {
        // $p = filemtime($path);
        if (is_file($file=  $this->getCacheFilePath($path, $ext))){
            // save store chache file base on current file             
            $expired = $this->storeCachingFileTime($path, $file, $ext); 
           
            return $expired;
        }
        return true;
    }
    /**
     * check that file expire from cache storage
     * @param string $realpath_to_check 
     * @param string $caching_name 
     * @param string $ext 
     * @return bool 
     */
    public function checkNotExpired(string $realpath_to_check, string $caching_name, $ext = '.php')
    {
        $p = filemtime($realpath_to_check);
        $vn = $this->getCacheFilePath($caching_name, $ext);
        if (file_exists($vn)) {
            return filemtime($vn) > $p;
        }
        return false;
    }
    /**
     * 
     * @param mixed $filepath 
     * @param mixed $fs 
     * @param mixed $ext extension 
     * @return bool 
     */
    public function storeCachingFileTime(string $filepath, string $fs, string $ext): bool
    {
        self::_LoadCachingFileHistory();
        $manifest = $filepath;
        $g = filemtime($manifest);

        $r = false;
        if (isset(self::$sm_cachingHistory[$fs])) {
            $time = self::$sm_cachingHistory[$fs];
            if ($g == $time) {
                return $r;
            }
        }
        $r = true; 
       
        self::$sm_cachingHistory[$fs] = $g;

        if (!self::$sm_registerSingle) {
            igk_reg_hook(IGKEvents::HOOK_APP_SHUTDOWN, function ($e) {
                $cl = static::class;
                $cl::_onShutdown();
            });
            self::$sm_registerSingle = true;
        }
        //}
        return $r;
    }

    /**
     * flag used in single shutdow registration 
     * @var bool
     */
    private static $sm_registerSingle;
    /**
     * 
     * @var mixed
     */
    private static $sm_cachingHistory;

    private static function CachingHistoryFile()
    {
        return igk_io_cachedir() . '/.ws-caching-file_history.php';
    }

    private static function _onShutdown()
    {
        // save file : 
        self::_SaveCachingFileHistory();
    }
    private static function _SaveCachingFileHistory()
    {
        $file = self::CachingHistoryFile();
        $build = new PHPScriptBuilder;
        $def = sprintf('return [%s];' . "\n", "\n" . PHPScriptBuilder::DumpObject(self::$sm_cachingHistory));

        $build->type('function')
            ->desc('This is a caching history file list ')
            ->defs($def);
        igk_io_w2file($file, $build->render());
    }
    private static function _LoadCachingFileHistory()
    {
        if (is_null(self::$sm_cachingHistory)) {
            if (igk_io_file_exists($file = self::CachingHistoryFile(), true)) {
                self::$sm_cachingHistory = include_once $file;
            } else {
                self::$sm_cachingHistory = [];
            }
        }
    }
}
