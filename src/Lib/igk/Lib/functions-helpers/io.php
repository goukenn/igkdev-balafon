<?php

// @author: C.A.D. BONDJE DOUE
// @filename: io.php
// @date: 20220831 14:15:59
// @desc: io function helpers

use IGK\Helper\IO;


/**
 * 
 * @param string $path 
 * @return string 
 */
function igk_io_flatten(string $path){
    $c = igk_uri($path);
    $j = explode("../", $c);
    $path = $j[0];
    array_shift($j);
    while(count($j)>0){
        $cp = array_shift($j);
        $path = dirname($path);
        if ($cp){
            $path .= '/'.$cp;
        }
    }
    $path = str_replace('./', '', $path);
    return $path;
}


/**
 * read all file's content
 * @param mixed $f 
 */
function igk_io_read_allfile($f):?string
{
    if (is_file($f))
        return IO::ReadAllText($f);
    return null;
}


///<summary>get folder where to cache some file</summary>
/**
 * get folder where to cache some file
 */
function igk_io_cachedir()
{
    return igk_uri(igk_io_applicationdir() . DIRECTORY_SEPARATOR . IGK_CACHE_FOLDER);
}


///<summary>target, cibling le lien</summary>
///<param name="target">: link to create</param>
///<param name="cibling">: lien a créer</param>
/**
 * target, cibling le lien
 * @param mixed $target : link to create
 * @param mixed $cibling : lien a créer
 */
function igk_io_symlink($target, $link)
{
    $r = false;
    if (!file_exists($link) && !is_link($link) && IO::CreateDir(dirname($link))) {
        $target = IGKCaches::ResolvPath($target);
        if (!igk_server()->WINDIR) {
        }
        if (($home = igk_server()->HOME) && is_link($home)) {
            $cpath = realpath($home);
            if (strstr($cp = realpath($target), $cpath)) {
                $target = $home . substr($cp, strlen($cpath));
            }
        }
        if (!($r = IO::SymLink($target, $link))) {
            igk_ilog("unix symlink failed: source: " . $target . " cibling: " . $link);
            if (igk_environment()->isDev()) {
                igk_trace();
                igk_wln_e("failed to create symlink "); ///fileexists? " . file_exists($target), "realtarget:" . realpath($target), "failed; " . $target, " cibling " . $link);
            }
        }
    }
    return $r;
}


///<summary>return where global project are stored</summary>
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