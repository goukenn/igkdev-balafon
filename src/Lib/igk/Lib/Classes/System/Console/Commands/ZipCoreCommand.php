<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ZipCoreCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\Helper\IO;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;

class ZipCoreCommand extends AppExecCommand{

    var $command = "--zipcore";

    var $desc = "zip balafon core";

    var $category = "utils";


    public function exec($command, $path=null){
       
        if (!extension_loaded("zip") && !function_exists('zip_open')){
            Logger::danger("zip utility function not found");
            return -1;
        }
        $ext = "-".date("Ymd").".zip";
        $fname = "/balafon.".IGK_VERSION.$ext;
        if ($path == null){
            $path = getcwd().$fname;
        } else if (is_dir($path)){
            $path = rtrim($path, "/").$fname;
        }
        $incVersion = null;        
        if (file_exists($path)){
            $dir = dirname ($path);
            $v = explode(".", IGK_VERSION);            
            $match = "/^".implode(".", ["balafon", $v[0], $v[1]]). "/";
           
            $files = IO::GetFiles($dir, function($s)use($match){           
                return preg_match($match, basename($s));
            });
            if (count($files)>0){
                rsort($files); 
                $version = igk_preg_match("/^balafon\.(?P<version>[^-]+)/", basename($files[0]), "version");

                // igk_wln_e("file : ", $files, $version);
                $v = explode(".", $version);
            }
            $incVersion = $v[2];
            $incVersion++;
            $v[2] = $incVersion;
            $path = dirname ($path). "/balafon.".implode(".", $v).$ext;
        }
        if (igk_sys_zip_core($path, $incVersion)){
            Logger::print("out file : ".$path);
            Logger::success("zip complete");
        }
    }
}