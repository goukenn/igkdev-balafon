<?php

namespace IGK\Helper;

use IGK\System\Console\Logger;

class FtpHelper
{

    public static function FileExists($res, $path){
        $dir = dirname($path);
        $g = ftp_nlist($res, $dir);
        return in_array(basename($path), $g);
    }
    /**
     * create directory
     * @param resource $ftpresourse ftp resource 
     * @param string $directory
     * @return void 
     */
    public static function CreateDir($ftpresourse, string $directory)
    {
        $dir = array_filter(explode('/', igk_html_uri($directory)));
        $bckdir = ftp_pwd($ftpresourse);
        $r = true;
        while ($r && ($_m = array_shift($dir))) {
            if (!@ftp_chdir($ftpresourse, $_m)) {
                error_clear_last();
                if (ftp_mkdir($ftpresourse, $_m) === false) {
                    $r = false;
                    continue;
                }
                @ftp_chdir($ftpresourse, $_m);
            }
        }
        ftp_chdir($ftpresourse, $bckdir);
        
        return $r;
    }

    public static function RmDir($ftpresourse, string $directory){
        $cwd = ftp_pwd($ftpresourse);
        if (!@ftp_chdir($ftpresourse, $directory))
            return true;
        $sub = ["."];
        $ddirs = [$directory];
        while($dir = array_shift($sub)){
            $files = ftp_mlsd($ftpresourse, $dir);
            $path =  ($dir=="." ?  $directory : $dir); 

            foreach($files as $k=>$v){
                if (($v['name']=="..") || ($v['name']=="."))
                {
                    continue;
                }
                $full = $path."/".$v['name'];
                igk_is_debug() && Logger::print("remove : ".$full);
                if ($v["type"] == "dir"){
                    if (!in_array($full, $ddirs)){
                        array_unshift($ddirs, $full);
                    }
                    array_push($sub, $full);
                }else {
                    ftp_delete($ftpresourse, $full);
                }
            } 

        }
 
        while($d = array_shift($ddirs)){
            if (!@ftp_rmdir($ftpresourse, $d)){
                return false;
            }
        } 
        return true;  
    } 
    /**
     * remove file from server
     * @param mixed $res 
     * @param mixed $path 
     * @return bool 
     */
    public static function RmFile($res, $path){        
        return @ftp_delete($res, $path);
    }
    public static function RenameFile($res, $source, $dest){
        return @ftp_rename($res, $source, $dest);
    }
}
