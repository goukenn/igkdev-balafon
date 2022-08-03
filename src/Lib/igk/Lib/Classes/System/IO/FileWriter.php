<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileWriter.php
// @date: 20220803 13:48:55
// @desc: 

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
        if(!$overwrite && is_file($filename)){
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
        if($chmod && igk_environment()->isUnix()){  
            $s_chmod = is_string($chmod) ? octdec($chmod) : $chmod; 
            if(($user = posix_getpwuid(fileowner($filename)))
              && (get_current_user() == $user["name"]) && !@chmod($filename, $s_chmod)){
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
        if(preg_match("/^phar:/i", $dirname)){
            igk_die("InvalidOperation#1200");
        }
        $pdir=array($dirname);
        $s_mode = is_string($mode) ? octdec($mode) : $mode;
        $is_unix = igk_environment()->isUnix();
     
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
                    if ($is_unix){
                        chmod($dirname, $s_mode);
                    }
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

    /**
     * if opcache enabled invalidate the file
     * @param mixed $file 
     * @param bool $force 
     * @return bool|void 
     */
    public static function Invalidate($file, $force=true){        
        if (function_exists('opcache_get_status')){
            if ($s = opcache_get_status()){
                return opcache_invalidate($file, $force); 
            } 
        }
    }
}