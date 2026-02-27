<?php
// @file: igk_zip.php
// @author: C.A.D. BONDJE DOUE
// @description: zip utility function 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Helper\IO;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;

if (!in_array("zip", get_loaded_extensions(false))){
    return;
}  

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
    if(!$zip->open($temp_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE )){
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

/**
* Igk zip create dir.
* @param mixed $outdir
* @param mixed $name
*/
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

/**
* Igk zip create file.
* @param mixed $file
* @param mixed $dir
* @param null|mixed $folder
* @param null|mixed $regex
*/
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

/**
* Igk zip delete.
* @param mixed $file
* @param mixed $entry
* @param mixed $close
*/
function igk_zip_delete($file, $entry, $close=1){
    if(!igk_io_file_exists($file))
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
/**
 * zip folder 
 * @param string $dir input directory 
 * @param mixed $zip zip resource create with ZipArchive
 * @param string $folder destination folder
 * @param mixed $regex ignore regex
 * @param ?bool allow_hidden_dir allow hidden directory against regex
 * @return void|array entries files
 */
function igk_zip_dir(string $dir, $zip, ?string $folder=null, ?string $regex=null, $allow_hidden_dir=true){
    if(!$zip)
        return;
    $q=0;
    $tab=is_array($dir) ? $dir: array($dir);
    $files = [];
    
    while(count($tab)>0){
        $q=array_pop($tab);
        if (!$q){
            continue;
        }
        $hdir=opendir($q);
        if(is_resource($hdir)){
            $v_hasregex = ($regex !== null);
            while($d=readdir($hdir)){
                $f=$q."/".$d;
                $v_isfolder = false;
                $v_is_dir = false;
                if( (($v_isfolder = ($d == ".") || ($d == ".."))) || ($v_is_dir = (($d[0]=='.') && is_dir($f)) ) || is_link($f)){
                    // + ingore dir start start with '.'
                    if ($v_isfolder || !($v_is_dir && $allow_hidden_dir)){
                        
                        continue;
                    } 
                }
                if($v_hasregex && preg_match($regex, $f)){
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
    if(igk_io_file_exists($outf))
        unlink($outf);
    $count=0;
    if($zip->open($outf, ZIPARCHIVE::CREATE) === true){
        $ln=strlen($dir) + 1;
        $tdir=array();
        foreach($files as $v){
            if(!igk_io_file_exists($v) || preg_match($exclude_pattern, $v)){
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

/**
* Igk zip folder.
* @param mixed $outfile
* @param mixed $dir
* @param null|mixed $folder
* @param null|mixed $regex
*/
function igk_zip_folder($outfile, $dir, $folder=null, $regex=null){
    if(is_String($dir) && (is_dir($dir) == false))
        return false;
    $ar=0;
    if(is_array($dir)){
        $ar=1;
    }
    $zip=new ZipArchive();
    if($zip->open($outfile, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ===true){
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

/**
* Igk zip module.
* @param mixed $outf
*/
function igk_zip_module($outf){  
    return igk_zip_excludedir(igk_io_basedir()."/Mods", $outf, "/\.(avi|(mp|(3|4))|gkds|zip|rar)/i");
}

/**
* auto generate doc.
* @param string|callable|mixed $entry
* @return int
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

/**
* Igk zip unzip callback.
* @param mixed $zipfile
* @param mixed $callback
*/
function igk_zip_unzip_callback($zipfile, $callback){
    igk_die('not implement');
}

/**
* Igk zip unzip entry.
* @param mixed $f
* @param mixed $entry
*/
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