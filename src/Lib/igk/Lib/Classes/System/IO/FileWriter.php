<?php
namespace IGK\System\IO;

use IGK\Helper\IO;
use IGKApp;
use IGKAppContext;
use IGKException;

/**
 * file writer helper. to store file
 * @package IGK\System\IO
 */
class FileWriter{

    /**
     * save to file 
     * @param mixed $filename 
     * @param mixed $content 
     * @param bool $overwrite 
     * @return true 
     */
    public static function Save($filename, $content, $overwrite=true, $chmod=IGK_DEFAULT_FILE_MASK, $type="w+"){
         
        if(empty($filename)){
            igk_die(__FUNCTION__." Filename is empty or null");
        }
        $filename=igk_io_dir($filename);
        if(!is_dir(dirname($filename))){
            if(!IO::CreateDir(dirname($filename)))
                return false;
        }
        if(is_file($filename) && !$overwrite){
            return false;
        } 
        // if ($filename === "/var/www/html/sites/HomeNotify/src/application/Caches/.controller.cache"){
        //     igk_ilog("file: ".$filename);
        //     igk_trace();
        //     igk_exit();
        // }
        $hf=@fopen($filename, $type);
        if(!$hf){  
            igk_ilog("Failed to write ".$filename, __FUNCTION__);    
            return false;
        }
        // $v_iempty=empty($content);
        fwrite($hf, $content);
        fflush($hf);
        fclose($hf);
        if($chmod){ 
            // $user = posix_getpwuid(fileowner($filename));
            // igk_wln_e($filename, "user info : ", get_current_user(), PHP_OS, $user, $_SERVER);
    
            if(in_array(PHP_OS, ["Linux"])
              && ($user = posix_getpwuid(fileowner($filename)))
              && (get_current_user() == $user["name"]) && !@chmod($filename, $chmod)){
                if (igk_current_context() == IGKAppContext::running){
                    if(IGKApp::IsInit()){
                        igk_notify_error("/!\\ chmod failed ". $filename. " : ".$chmod);
                    }
                } 
                igk_ilog(__METHOD__."  -> chmodfailed :::".$filename.":".$chmod); 
            }        
        }
        return true; 
    }
    /**
     * create directory
     * @return bool success
     */
    public static function CreateDir($dirname, $mode=IGK_DEFAULT_FOLDER_MASK){
        $dirname = igk_io_dir($dirname); 
        // if ((basename($dirname) =="views") && 
        // ($dirname!='/var/www/html/sites/balafon/src/application/Caches/storage/views')){
        //     igk_wln_e("dir : ", $dirname);
        //     igk_trace();
        //     igk_exit();
        // }

        if(preg_match("/^phar:/i", $dirname)){
            igk_die("InvalidOperation#1200");
        }
        $pdir=array($dirname);
     
        while($dirname=array_pop($pdir)){
            if(empty($dirname))
                return false;
            if(is_dir($dirname))
                continue;
            if(empty($dirname))
                return false;
            if(is_dir($dirname))
                continue;
            $p=dirname($dirname);
            if(empty($p))
                continue;
            if(is_dir($p) && !is_file($dirname) && !is_dir($dirname) ){ 
                if (@mkdir($dirname)){
                    chmod($dirname, $mode);
                }else{
                    igk_dev_wln_e("failed to create : ".$dirname);
                    throw new IGKException("failed to create ".$dirname); 
                }
            }
            else{
                array_push($pdir, $dirname);
                array_push($pdir, dirname($dirname));
            }
        }        
        return igk_count($pdir) == 0;
    }
}