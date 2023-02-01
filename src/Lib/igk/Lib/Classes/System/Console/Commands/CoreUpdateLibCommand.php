<?php
// @author: C.A.D. BONDJE DOUE
// @file: CoreUpdateLibCommand.php
// @date: 20221230 13:14:38
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlReader;
use IGK\System\Installers\BalafonInstaller;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class CoreUpdateLibCommand extends AppExecCommand{

    const GET_URI = "https://igkdev.com/balafon/get-download";
    var $command = '--update-corelib';
    var $category = 'utils';
    var $desc = 'update or restore core library';

    public function exec($command) { 
        if (!extension_loaded("zip") && !function_exists('zip_open')){
            Logger::danger("zip utility function not found");
            return -1;
        }
        if (!extension_loaded("curl") && !function_exists('igk_curl_post_uri')){
            Logger::danger("curl utility function not found");
            return -2;
        }
        if ($content = igk_curl_post_uri(self::GET_URI)){
            if (igk_curl_status()==200){
                $tempfile = igk_io_sys_tempnam('bfl');
                rename($tempfile, $tempfile.= ".zip");
                igk_io_w2file($tempfile, $content);                
                if (self::CheckZipFile($tempfile, $errors)){
                    Logger::info("extract core library");  
                    $old_version = IGK_VERSION;              
                    igk_zip_unzip($tempfile, IGK_LIB_DIR."/../../");

                    if (function_exists('opcache_reset') && opcache_reset()){
                        Logger::info("old version : ".$old_version);
                        Logger::info("new version : ".IGK_VERSION);
                    }
                }else{
                    Logger::danger("not a valid corelib file");
                }
                unlink($tempfile);
            }
        } else {
            Logger::danger("can't retrieve BALAFON core library");
        }

    }
    /**
     * check core library
     */
    static function CheckZipFile(string $file, & $errors = null) :bool{
        $s = igk_zip_unzip_filecontent($file, "manifest.xml");
        if (empty($s)) {
            $$errors[] = "manifest.xml is empty";
            return false;
        }
        $xml = HtmlReader::Load($s, "xml");
        $obj = igk_createobj();
        igk_conf_load($obj, $xml);
        $manifest = igk_conf_get($obj, "manifest");
        if (igk_conf_get($manifest, "appName") != IGK_PLATEFORM_NAME) {
            $$errors[] = ("no appName or missing " . IGK_PLATEFORM_NAME);
            return false;
        }
        $version = igk_conf_get($manifest, "version");
        if (!$version) {
            $$errors[] = ("missing version in manifest");
            return false;
        }
        return true;
    }

}