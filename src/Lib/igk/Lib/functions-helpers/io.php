<?php

// @author: C.A.D. BONDJE DOUE
// @filename: io.php
// @date: 20220831 14:15:59
// @desc: io function helpers

use IGK\Helper\IO;

function igk_io_mimetype_ext($type, $default='.dat'){
    return igk_getv([
        "image/jpeg"=>".jpeg",
        "image/png"=>".png",
        "image/jpg"=>".jpg",
    ], $type, '.dat');
}
/**
 * retrieve mimetype
 * @param mixed $ext 
 * @param mixed $default 
 * @return mixed 
 */
function igk_io_mimetype($ext, $default){
    foreach([
        '/\.(jpg|jpeg|webp)/'=>'image/jpeg',
        '/\.(png)/'=>'image/png',
        '/\.(bmp)/'=>'image/bitmap'
    ] as $k=>$v){
        if (preg_match($k, $ext)){
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
 * target, cibling 
 * @param mixed $target target of the link
 * @param mixed $cibling link to create
 */
function igk_io_symlink($target, $link)
{
    $r = false;
    // if (file_exists($link) && is_link($link)){
    //     $lnk = readlink($link);
    // }
    if (!file_exists($link) && !is_link($link) && IO::CreateDir(dirname($link))) {
        $target = IGKCaches::ResolvPath($target);      
        if (($home = igk_server()->HOME) && is_link($home)) {
            $cpath = realpath($home);
            if (strstr($cp = realpath($target), $cpath)) {
                $target = $home . substr($cp, strlen($cpath));
            }
        }
        
        // get relative link to target from link
        $relative_target = IO::GetRelativePath($link , $target) ?? $target; 

        // check that the directory exists to taget file 
        if (igk_is_debug() || igk_environment()->isDev()){
            $bck = getcwd();
            chdir(dirname($link));
            $check = $relative_target;           
            $g = file_exists($check); 
            chdir($bck);
            if (!$g){
                igk_dev_wln_e(__FILE__.":".__LINE__ , " target not valid : create a symlink ", $link, $target, $relative_target,  "?", $g);
            }
        }

        if (!($r = IO::SymLink($relative_target, $link))) {
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