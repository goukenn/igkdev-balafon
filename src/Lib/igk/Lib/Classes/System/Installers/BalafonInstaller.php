<?php
// @file: IGKBalafonInstaller.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Installers;
use IGK\Controllers\SystemController;
use IGK\System\Http\AcceptMimeTypes;
use IGK\IActionResult;
use Throwable;
use function igk_resources_gets as __;

require_once(IGK_LIB_CLASSES_DIR . "/Helper/Activator.php");
require_once(IGK_LIB_CLASSES_DIR . "/System/Html/Templates/BindingContextInfo.php");
require_once IGK_LIB_CLASSES_DIR . "/HookOptions.php";
/**
 * use to update core framework
 */
class BalafonInstaller implements IActionResult , IBalafonInstaller
{
    /**
    * Constant: installer key.
    * @var mixed
    */
    const INSTALLER_KEY = "installer://uploadfile";
    /**
    * Property: zipcore.
    * @var mixed
    */
    protected $zipcore = true;
    /**
    * Property: zipfile.
    * @var mixed
    */
    protected $zipfile;
    /**
     * .ctr
     * @return void 
     */
    public function __construct()
    {
    }
    /**
    * auto generate doc.
    */
    public function index()
    {
    }
    /**
    * Config dir.
    */
    public function configDir()
    {
        return SystemController::configDir();
    }
    /**
    * auto generate doc.
    * @param string $param
    * @param string $default
    * @return mixed
    */
    private function _get_dir(string $param, string $default){
        $dir = null;
        if (!empty($dir = igk_getr($param))){
            $dir = igk_io_expand_path(base64_decode($dir));
        }
        else{
            $dir = $default;
        }
        return $dir;
    }
    /**
     * do update
     */
    public function update()
    {
        $zfile = igk_app()->session->getParam(self::INSTALLER_KEY);
        igk_ilog("the update file : ".$zfile);
        if (igk_server()->eventStreamRequest() && !igk_is_ajx_demand()) {
            igk_set_header(500, "update not allowed");
            igk_exit();
        }
        $r = 0;
        $key = self::INSTALLER_KEY;
        $from_upload = 0;
        $install_dir = $this->_get_dir("dir", IGK_LIB_DIR);
        $base_dir = $this->_get_dir("basedir", igk_io_basedir());
        require_once(dirname(__FILE__) . "/InstallerActionMiddleWare.pinc");
        $mime_type = AcceptMimeTypes::EventStream;
        if (igk_server()->eventStreamRequest()) {
            header("Content-Type: ".$mime_type);
            header("Cache-Control: no-cache");
        } else {
            if (igk_server_is_local() || igk_is_conf_connected()) {
                igk_server()->HTTP_ACCEPT = $mime_type;
            } else {
                igk_set_header(500);
                igk_wln_e("misconfiguration - accept only request from local server");
            }
        }
        $this->zipfile = $zfile = igk_app()->session->getParam($key);
        igk_app()->session->setParam($key, null);
        igk_sess_write_close();
        igk_flush_start();
        igk_set_timeout(0);
        $service = new InstallerMiddleWareActions();
        $service->fromUpload = $from_upload;
        if ($this->zipcore) {
            if (igk_server()->IGK_LOCAL_TEST) {
                $service->BaseDir = igk_server()->TEST_BASE_DIR;
                $service->LibDir = igk_server()->LIB_DIR;
                $service->CoreZip = igk_server()->CORE_ZIP;
                $service->installDir = $install_dir;
                if (!igk_io_file_exists($service->CoreZip)) {
                    igk_flush_write($r ? "ok" : "failed", "finish");
                    igk_flush_data();
                    igk_exit();
                }
            } else {
                if (!empty($zfile) && igk_io_file_exists($zfile)) {
                    $service->CoreZip = $zfile;
                    $service->BaseDir = $base_dir;
                    $service->LibDir = IGK_LIB_DIR;
                    $service->installDir = $install_dir;
                    $from_upload = 1;
                    $service->fromUpload = 1;
                } else {
                    igk_ilog("the zip file {$zfile} not present");
                    igk_ilog("install dir ".$install_dir);
                    igk_flush_write("zip file not exits or is empty", "finish");
                    igk_flush_data();
                    igk_exit();
                }
            }
        }
        $this->init_installer($service);
        $r = false;
        try {            
            $r = $service->process();            
            if (!$r){
                $service->abort();
            }
        } catch (Throwable $ex) {
            igk_ilog("something bad happend: " . $ex->getMessage());
            $service->abort();
        }
        igk_flush_write($r ? "ok" : "failed", "finish");
        igk_flush_data();
        igk_ilog("finish install : ".$r);
        igk_exit();
    }
    /**
     * receive zip file
     */
    public function upload()
    { 
        if (!igk_is_ajx_demand() || !igk_server()->method("POST")) {
            igk_wln_e(
                "ajx-demand " . igk_is_ajx_demand(),
                igk_environment()->get("source_request"),
                $_SERVER
            );
            die("operation not allowed. " . __FUNCTION__);
        }
        igk_ilog("installer recieve file ...");
        $file = igk_io_sys_tempnam("igk");
        rename($file, $file = $file . ".zip");
        igk_app()->session->setParam(self::INSTALLER_KEY, igk_uri($file));
        igk_ilog("installer session : ". igk_app()->session->getParam(self::INSTALLER_KEY));
        igk_sess_write_close(); 
        igk_io_store_ajx_uploaded_data(dirname($file), basename($file));
        $size = 0;
        if (igk_io_file_exists($file)  && (($size = @filesize($file)) == 0)) {
            igk_ilog(static::class . ":no data to store : " . $file);
            igk_set_header(500, "file not set");
        } else {
            igk_ilog("installer stored data : " . $file . ":" . $size);
        }
        igk_exit();
    }
    /**
    * Initializes installer.
    * @param InstallerMiddleWareActions $service
    */
    protected function init_installer(InstallerMiddleWareActions $service)
    { 
        require_once IGK_LIB_DIR . "/igk_html_func_items.php";
        $service->add(new BalafonInstallerMiddelWare());
        $service->add(new BackupLibConfigMiddleWare());
        $service->add(new MaintenaceLibMiddleWare());
        $service->add(new ClearCacheMiddleWare());
        $service->add(new RenameLibaryMiddleWare());
        $service->add(new ExtractLibaryMiddleWare());
        $service->add(new UnlinkZipMiddleWare());
        $service->add(new SuccessMiddleWare());
    }
}