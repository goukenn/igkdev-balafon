<?php

// @author: C.A.D. BONDJE DOUE
// @filename: io.php
// @date: 20220831 14:15:59
// @desc: io function helpers

use IGK\Helper\IO;
use IGK\System\IO\Cache\FS;

function igk_io_mimetype_ext($type, $default = '.dat')
{
    return igk_getv([
        "image/jpeg" => ".jpeg",
        "image/png" => ".png",
        "image/jpg" => ".jpg",
    ], $type, '.dat');
}
/**
 * retrieve mimetype
 * @param mixed $ext 
 * @param mixed $default 
 * @return mixed 
 */
function igk_io_mimetype($ext, $default)
{
    foreach (
        [
            '/\.(jpg|jpeg|webp)/' => 'image/jpeg',
            '/\.(png)/' => 'image/png',
            '/\.(bmp)/' => 'image/bitmap'
        ] as $k => $v
    ) {
        if (preg_match($k, $ext)) {
            return $k;
        }
    }
    return $default;
}
/**
 * 
 * @param string $path 
 * @return string 
 */
function igk_io_flatten(string $path)
{
    $c = igk_uri($path);
    $j = explode("../", $c);
    $path = $j[0];
    array_shift($j);
    while (count($j) > 0) {
        $cp = array_shift($j);
        $path = dirname($path);
        if ($cp) {
            $path .= '/' . $cp;
        }
    }
    $path = str_replace('./', '', $path);
    return $path;
}


/**
 * read all file's content
 * @param mixed $f 
 */
function igk_io_read_allfile($f): ?string
{
    if (is_file($f))
        return IO::ReadAllText($f);
    return null;
}


/**
 * get folder where to cache some file
 */
function igk_io_cachedir()
{
    return \IGK\System\IO\Path::getInstance()->getCacheDir();
}


/**
 * target, cibling 
 * @param mixed $target target of the link
 * @param mixed $cibling link to create
 */
function igk_io_symlink($target, $link)
{
    $r = false;
    if (!file_exists($link) && !is_link($link) && IO::CreateDir(dirname($link))) {
        $target = IGKCaches::ResolvPath($target);
        if (($home = igk_server()->HOME) && is_link($home)) {
            $cpath = realpath($home);
            if (strstr($cp = realpath($target), $cpath)) {
                $target = $home . substr($cp, strlen($cpath));
            }
        }

        // get relative link to target from link
        $relative_target = IO::GetRelativePath($link, $target) ?? $target;

        // check that the directory exists to taget file 
        if (igk_is_debug() || igk_environment()->isDev()) {
            $bck = getcwd();
            chdir(dirname($link));
            $check = $relative_target;
            $g = igk_io_file_exists($check, true);
            chdir($bck);
            if (!$g) {
                igk_dev_wln_e(__FILE__ . ":" . __LINE__, " target not valid : create a symlink ", $link, $target, $relative_target,  "?", $g);
            }
        }

        if (!($r = IO::SymLink($relative_target, $link))) {
            igk_ilog("unix symlink failed: source: " . $target . " cibling: " . $link);
            if (igk_environment()->isDev()) {
                igk_trace();
                igk_wln_e("failed to create symlink ");
            }
        }
    }
    return $r;
}


/**
 * return where global project are stored
 */
function igk_io_projectdir()
{
    $pdir = null;
    if (defined('IGK_PROJECT_DIR')) {
        $pdir = IGK_PROJECT_DIR;
    } else {
        $pdir = igk_getv($_SERVER, 'IGK_PROJECT_DIR', igk_io_applicationdir() . "/" . IGK_PROJECTS_FOLDER);
        define('IGK_PROJECT_DIR', $pdir);
    }
    $pdir || die("project dir not setup properly");
    return igk_uri($pdir);
}

if (!function_exists('igk_io_file_exists')) {
    function igk_io_file_exists(string $file, bool $autocheck =false): bool
    {
        static $FS;
        if (IGKApp::IsInit()) {
            if (is_null($FS)) {
                $FS = new FS();
                $FS->loadCache();
                $env_outside = igk_env_count_get('file_exists_outside');
                if ($env_outside){
                    igk_environment()->getDebugger()->fs_filesystem_check_counter = 
                    $env_outside; 
                }
            } 
            return $FS->fileExists($file, $autocheck);
        }
        igk_env_count("file_exists_outside"); 
        return file_exists($file);
    }
}

function igk_io_cache_file_exists(string $file):bool{
    return igk_io_file_exists($file, true);
}