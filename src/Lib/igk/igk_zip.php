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
use IGK\System\IO\Path;

if (!in_array("zip", get_loaded_extensions(false))){
    return;
}  

///<summary></summary>
///<param name="file"></param>
///<param name="name"></param>
///<param name="content"></param>
///<param name="closearchive" default="1"></param>
/**
 * zip content
 * @param string $temp_file 
 * @param string $name 
 * @param string $content 
 * @param int $closearchive 
 * @return int|null|ZipArchive 
 */
function igk_zip_content(string $temp_file, string $name, string $content, $closearchive=1){
    $zip=new ZipArchive();
    if(!$zip->open($temp_file, ZIPARCHIVE::CREATE)){
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
    if ($zip->open($file, ZIPARCHIVE::CREATE) === true){    
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
    if($zip->open($file, ZIPARCHIVE::CREATE)!==true){
        // $zip->close();
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
 * @param string $dir input directory 
 * @param mixed $zip zip resource create with ZipArchive
 * @param string $folder destination folder
 * @param mixed $regex ignore regex
 * @return void|array entries files
 */
function igk_zip_dir(string $dir, $zip, ?string $folder=null, ?string $regex=null){
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
/**
 * exclude directory from pattern
 * @param string $dir 
 * @param string $outf 
 * @param string $exclude_pattern 
 * @return (null|array|int)[] 
 */
function igk_zip_excludedir(string $dir, string $outf,string $exclude_pattern){
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
    if($zip->open($outfile, ZIPARCHIVE::CREATE) ===true){
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
///<param name="outf"></param>
function igk_zip_module($outf){  
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
    $zip = new ZipArchive();
    if ($zip->open($file, ZipArchive::RDONLY) === true){
        $c = $zip->numFiles;
        $dirs = [];
        for($i = 0; $i < $c ; $i++){
            $n = $zip->getNameIndex($i);
            if($entry && (is_callable($entry) && !$entry($n)) && (is_string($entry) && !preg_match($entry, $n))){
                continue;
            }
            if (substr($n, -1) == '/'){
                // directory 
                if (!isset($dirs[$n])){
                    $dirs[$n] = 1;
                    IO::CreateDir(Path::Combine($outdir, $n));
                }
            } else {
                if ($stream = $zip->getStream($n)){
                    igk_io_w2file(Path::Combine($outdir, $n), stream_get_contents($stream));
                    fclose($stream);
                }else {
                    Logger::danger($zip->getStatusString());
                }
            } 
        }
        $zip->close();
        return true;
    }
    return false; 
}
///<summary></summary>
///<param name="zipfile"></param>
///<param name="callback"></param>
function igk_zip_unzip_callback($zipfile, $callback){
    igk_die('not implement');
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
function igk_zip_unzip_filecontent(string $zipfile, string $name){
    $zip = new ZipArchive; 
    $c = null;
    if ($zip->open($zipfile, ZipArchive::RDONLY)===true){
        $c = $zip->numFiles; 
        for($i = 0; $i < $c ; $i++){
            $n = $zip->getNameIndex($i);            
            if(strtolower($n) == $name){
                if ($stream = $zip->getStream($n)){
                    $c = stream_get_contents($stream);
                    fclose($stream);
                }else {
                    Logger::danger($zip->getStatusString());
                }
                break;
            } 
        }
        $zip->close();
    }
    return $c;
}
///<summary></summary>
///<param name="file"></param>
///<param name="outdir"></param>
///<param name="zipentry" default="null"></param>
/**
 * unzip file to
 * @param mixed $file 
 * @param mixed $outdir  
 * @return int 
 * @throws IGKException 
 */
function igk_zip_unzip_to(string $file, string $outdir){
    return igk_zip_unzip($file, $outdir);
  
}