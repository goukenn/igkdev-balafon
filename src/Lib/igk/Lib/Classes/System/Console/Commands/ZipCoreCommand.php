<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ZipCoreCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\Helper\CoreUtility;
use IGK\Helper\IO;
use IGK\Helper\PhpUnitHelper;
use IGK\Helper\SysUtils;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Shell\OsShell;

class ZipCoreCommand extends AppExecCommand{

    var $command = "--zipcore";

    var $desc = "zip balafon core";

    var $category = "utils";

    var $options = [
        "--no-test"=>"flag: disable test ",
        "--core-test-suite"=>"suite test to run"
    ];

    public function exec($command, $path=null){
       
        if (!extension_loaded("zip") && !function_exists('zip_open')){
            Logger::danger("zip utility function not found");
            return -1;
        }
        $no_check = property_exists($command->options, "--no-test");
        $v_punit = property_exists($command->options, "--phpunit");

        igk_set_timeout(0);
       

        // + | --------------------------------------------------------------------
        // + | run unit test before create a zip
        if ((!$no_check || $v_punit) && $phpunit = OsShell::Where('phpunit')){
            $core_suite = igk_getv($command,'--core-test-suite','core');
            $r = PhpUnitHelper::TestCoreProject($phpunit, $core_suite);
            if ($r){
                return $r;
            } 
            echo PHP_EOL;       
        }

        if (!$no_check)
        {
            // + | --------------------------------------------------------------------
            // + | check all files with php lint
            $r = CoreUtility::LintCoreLib();
            if (!$r){
                return $r;
            }  
            echo PHP_EOL;         
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
            $version = IO::CheckFileVersion($path); 
            $path = $dir. "/balafon.".$version.$ext;
        }
        Logger::info("run zip ......");
        if (igk_sys_zip_core($path, $incVersion)){
            Logger::print("out file : ".$path);
            Logger::success("zip complete");
        }
    }
}