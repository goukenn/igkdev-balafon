<?php
// @file: igk_zip.php
// @author: C.A.D. BONDJE DOUE
// @description: zip utility function 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Helper\IO;
use IGK\System\Console\Logger;

if (!in_array("zip", get_loaded_extensions(false))){
    return;
}  

///<summary></summary>
///<param name="file"></param>
///<param name="name"></param>
///<param name="content"></param>
///<param name="closearchive" default="1"></param>
function igk_zip_content($file, $name, $content, $closearchive=1){
    $zip=new ZipArchive();
    if(!$zip->open($file, ZIPARCHIVE::CREATE)){
        $zip->close();
        return 0;
    }
    $zip->addFromString($name, $content);
    if($closearchive){
        $zip->close();
        return null;
    }
    return $zip;
}
///<summary></summary>
///<param name="outdir"></param>
///<param name="name"></param>
function igk_zip_create_dir($outdir, $name){
    $t=explode('/', $name);
    if(is_dir($outdir)){
        $d=$outdir;
        foreach($t as $k){
            if(empty($k))
                continue;
            $d=$d.DIRECTORY_SEPARATOR.$k;
            if(!is_dir($d))
                @mkdir($d);
        }
    }
}
///<summary></summary>
///<param name="file"></param>
///<param name="dir"></param>
///<param name="folder" default="null"></param>
///<param name="regex" default="null"></param>
function igk_zip_create_file($file, $dir, $folder=null, $regex=null){
    if(!is_dir($dir))
        return false;
    $zip=new ZipArchive();
    $zip->open($file, ZIPARCHIVE::CREATE);
    if($zip){
        igk_zip_dir($dir, $zip, $folder, $regex);
        $zip->close();
    }
    return true;
}
///<summary></summary>
///<param name="file"></param>
///<param name="entry"></param>
///<param name="close" default="1"></param>
function igk_zip_delete($file, $entry, $close=1){
    if(!file_exists($file))
        return 0;
    $zip=new ZipArchive();
    if(!$zip->open($file, ZIPARCHIVE::CREATE)){
        $zip->close();
        return 0;
    }
    $r=$zip->deleteName($entry);
    if($close)
        $zip->close();
    return $r;
}
///<summary>use to zip a directory </summary>
/**
 * zip folder 
 * @param mixed $dir 
 * @param mixed $zip 
 * @param mixed $folder 
 * @param mixed $regex ignore regex
 * @return void|array entries files
 */
function igk_zip_dir($dir, $zip, $folder=null, $regex=null){
    if(!$zip)
        return;
    $q=0;
    $tab=is_array($dir) ? $dir: array($dir);
    $files = [];
    while($q=array_pop($tab)){
        $hdir=opendir($q);
        if(is_resource($hdir)){
            while($d=readdir($hdir)){
                if(($d == ".") || ($d == "..")){
                    continue;
                }
                $f=$q."/".$d;
                if(($regex !== null) && preg_match($regex, $f)){
                    continue;
                }
                igk_is_debug() && Logger::print('Add : '.$f); 
                $hd=substr($f, strlen($dir) + 1);
                $hd=(!empty($folder) ? $folder."/": null).$hd;
                if(is_dir($f)){
                    $zip->addEmptyDir($hd);
                    array_push($tab, $f);
                }
                else if(is_file($f)){
                    $zip->addFile($f, $hd);
                    array_push($files, $hd);
                }
            }
            closedir($hdir);
        }
    }
    return $files;
}
///<summary></summary>
///<param name="dir"></param>
///<param name="outf"></param>
///<param name="exclude_pattern"></param>
function igk_zip_excludedir($dir, $outf, $exclude_pattern){
    $files=igk_io_getfiles($dir);
    $zip=new ZipArchive();
    if(file_exists($outf))
        unlink($outf);
    $count=0;
    if($zip->open($outf, ZIPARCHIVE::CREATE) === true){
        $ln=strlen($dir) + 1;
        $tdir=array();
        foreach($files as $v){
            if(!file_exists($v) || preg_match($exclude_pattern, $v)){
                continue;
            }
            $count++;
            $bf=substr($v, $ln);
            $ddir=dirname($bf);
            if(!isset($tdir[$ddir])){
                $zip->addEmptyDir($ddir);
                $tdir[$ddir]=1;
            }
            if(!is_dir($v))
                $zip->addFile($v, $bf);
        }
        $zip->close();
    }
    return array("count"=>$count, "files"=>$files);
}
///zip extract outdir
function igk_zip_extract($outdir, $hzip, $e){
    if(!is_dir($outdir))
        return;
    $d=igk_dir($outdir.DIRECTORY_SEPARATOR.zip_entry_name($e));
    if(IO::CreateDir(dirname($d))){
        $content=zip_entry_read($e, zip_entry_filesize($e));
        igk_io_save_file_as_utf8_wbom($d, $content, true);
    }
}
///<summary>zip folder :  </summary>
///<param name="dir">mixed : string|array of folder do compress </param>
function igk_zip_folder($outfile, $dir, $folder=null, $regex=null){
    if(is_String($dir) && (is_dir($dir) == false))
        return false;
    $ar=0;
    if(is_array($dir)){
        $ar=1;
    }
    $zip=new ZipArchive();
    if($zip->open($outfile, ZIPARCHIVE::CREATE)){
        if($ar){
            foreach($dir as $m){
                $kname=basename($m);
                if($folder && ($b=strstr($m, $folder))){
                    $kname=substr(igk_uri(substr($m, strlen($folder))), 1);
                }
                igk_zip_dir($m, $zip, $kname);
            }
        }
        else{
            igk_zip_dir($dir, $zip, $folder, $regex);
        }
        $zip->close();
        return true;
    }
    return false;
}
///<summary></summary>
///<param name="e"></param>
function igk_zip_isdirentry($e){
    return ((zip_entry_filesize($e) == 0) && ((zip_entry_compressionmethod($e) == "stored") && igk_str_endwith(zip_entry_name($e), "/")));
}
///<summary></summary>
///<param name="outf"></param>
function igk_zip_module($outf){
    $dir=igk_io_basedir()."/Mods";
    return igk_zip_excludedir(igk_io_basedir()."/Mods", $outf, "/\.(avi|(mp|(3|4))|gkds|zip|rar)/i");
} 
///<summary></summary>
///<param name="file"></param>
///<param name="outdir"></param>
///<param name="entry" default="null">filter regex|callback</param>
/**
 * 
 * @param mixed $file 
 * @param mixed $outdir 
 * @param string|callable|mixed $entry 
 * @return int 
 * @throws IGKException 
 */
function igk_zip_unzip($file, $outdir, $entry=null){
    if(!is_dir($outdir))
        return 0;
    $hzip=zip_open($file);
    if(!$hzip || !is_resource($hzip))
        return 0;
    while(($e=zip_read($hzip))){
        $n=zip_entry_name($e);
        if($entry && (is_callable($entry) && !$entry($n)) && (is_string($entry) && !preg_match($entry, $n))){
            continue;
        }
        if(igk_zip_isdirentry($e)){
            igk_zip_create_dir($outdir, $n);
        }
        else{
            if(!(strpos($n, "/") === FALSE))
                igk_zip_extract($outdir, $hzip, $e);
        }
    }
    zip_close($hzip);
    return 1;
}
///<summary></summary>
///<param name="zipfile"></param>
///<param name="callback"></param>
function igk_zip_unzip_callback($zipfile, $callback){
    $hzip=zip_open($zipfile);
    if(!$hzip || !is_resource($hzip))
        return;
    while(($e=zip_read($hzip))){
        $n=zip_entry_name($e);
        if(!igk_zip_isdirentry($e)){
            $callback($hzip, $n, $e);
        }
    }
    zip_close($hzip);
}
///<summary></summary>
///<param name="f"></param>
///<param name="entry"></param>
function igk_zip_unzip_entry($f, $entry){
    $c="zip://".igk_uri($f)."#".$entry;
    $h=fopen($c, 'r');
    if(!$h){
        return null;
    }
    $c="";
    while(!feof($h)){
        $c .= fread($h, 4096);
    }
    fclose($h);
    return $c;
}
///<summary>read zip content</summary>
/**
 * unzip file archive and return name content
 * @param mixed $zipfile 
 * @param mixed $name 
 * @return false|string 
 */
function igk_zip_unzip_filecontent($zipfile, $name){
    $hzip=@zip_open($zipfile);
    if(!$hzip || !is_resource($hzip))
        return false;
    $name=strtolower($name);
    $c="";
    while(($e=@zip_read($hzip))){
        $n=@zip_entry_name($e);
        if(strtolower($n) == $name){
            if(!igk_zip_isdirentry($e)){
                $c=@zip_entry_read($e, zip_entry_filesize($e));
            }
            break;
        }
    }
    @zip_close($hzip);
    return $c;
}
///<summary></summary>
///<param name="file"></param>
///<param name="outdir"></param>
///<param name="zipentry" default="null"></param>
function igk_zip_unzip_to($file, $outdir, $zipentry=null){
    if($zipentry == null){
        igk_zip_unzip($file, $outdir);
    }
    if(!is_dir($outdir))
        return 0;
    $hzip=zip_open($file);
    if(!$hzip || !is_resource($hzip))
        return 0;
    while(($e=zip_read($hzip))){
        $n=zip_entry_name($e);
        if($n == $zipentry) if(igk_zip_isdirentry($e))
            continue;
        else{
            $content=zip_entry_read($e, zip_entry_filesize($e));
            igk_io_save_file_as_utf8_wbom($outdir."/".basename($n), $content, true);
            break;
        }
        if(preg_match("#^".$zipentry."#i", $n)){
            $d=igk_dir($outdir.DIRECTORY_SEPARATOR. substr(zip_entry_name($e), strlen($zipentry)));
            if(IO::CreateDir(dirname($d))){
                $content=zip_entry_read($e, zip_entry_filesize($e));
                igk_io_save_file_as_utf8_wbom($d, $content, true);
            }
        }
    }
    zip_close($hzip);
}