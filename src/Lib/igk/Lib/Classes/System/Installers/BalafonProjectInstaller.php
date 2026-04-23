<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonProjectInstaller.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Installers;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
use function igk_resources_gets as __; 

require_once(__DIR__."/InstallerActionMiddleWare.pinc");
/**
* auto generate doc.
* @package IGK\System\Installers
*/
class BalafonProjectInstaller extends BalafonInstaller{
    /**
    * Property: controller.
    * @var mixed
    */
    protected $controller;
    /**
    * Property: zipcore.
    * @var mixed
    */
    protected $zipcore = false;
    /**
    * auto generate doc.
    * @param InstallerMiddleWareActions $service
    * @return void
    */
    protected function init_installer(InstallerMiddleWareActions $service){
        $c = igk_getr("controller");
        $key=self::INSTALLER_KEY;
        $this->controller = igk_getctrl($c, false) ?? die("controller not found:$c");
        /**
        * auto generate doc.
        * @var mixed
        */
        $srv = $service;
        $service->LibDir =  IGK_LIB_DIR;
        $srv->CoreZip = $this->zipfile; 
        $srv->controller = $this->controller;
        $srv->project_name = igk_str_snake(basename(igk_dir(get_class($this->controller))));
        $srv->intall_dir =  $this->controller->getDeclaredDir(); 
        $service->add(new BalafonInstallerMiddelWare());
        $service->add(new BackupProjectMiddleWare($this->controller));
        $service->add(new RenameProjectMiddleWare());
        $service->add(new ExtractProjectLibaryMiddleWare());
        $service->add(new ClearCacheMiddleWare());
        $service->add(new SuccessProjectInstallMiddleWare());
    }
}
/**
* Backup project middle ware.
* @package IGK\System\Installers
*/
class BackupProjectMiddleWare extends InstallerActionMiddleWare{
    /**
    * Property: controller.
    * @var mixed
    */
    private $controller;
    /**
    * .ctr
    * @param BaseController $controller
    */
    public function __construct(BaseController $controller)
    { 
        $this->controller = $controller;
    }
    /**
    * Returns Message.
    */
    public function getMessage(){
        return __("Backup project ... {0}", get_class($this->controller));
    }
    /**
    * Abort.
    */
    public function abort(){
    }
    /**
    * Invoke.
    */
    public function invoke(){
        $dir = $this->controller->getDeclaredDir();
        $fname = igk_str_ns(get_class($this->controller))."_".date("Ymd");
        $path = dirname($dir)."/".$fname.".zip";
        if (igk_sys_zip_project($this->controller, $path)){
            \IGK\Models\Backups::create([
                "backup_type"=>"project",
                "backup_class"=>get_class($this->controller),
                "backup_path"=>igk_io_collapse_path($path)
            ]); 
        }
        $this->next();
    }
}
/**
* Extract project libary middle ware.
* @package IGK\System\Installers
*/
class ExtractProjectLibaryMiddleWare extends InstallerActionMiddleWare{
    /**
    * Returns Message.
    */
    public function getMessage(){
        return __("Extract project library cache ...");
    }
    /**
    * Abort.
    */
    public function abort(){
    }
    /**
    * Invoke.
    */
    public function invoke(){
        $ctrl = $this->getServiceInfo()->Listener->controller;
        $project_name  = $this->getServiceInfo()->Listener->project_name;
        $dir  = $this->getServiceInfo()->Listener->intall_dir;
        $core_zip = $this->getServiceInfo()->Listener->CoreZip;
        if (empty($core_zip)){
            return;
        }
        if(!igk_io_file_exists($zip=$core_zip)){
            return;
		}
        if(!igk_zip_unzip(igk_uri($zip), dirname($dir), "#^".$project_name."#")){
            return;
        }
        $temp_dir = $this->getServiceInfo()->Listener->TempDir;
        IO::RmDir($temp_dir);
        $this->next();
    }
}
/**
* Success project install middle ware.
* @package IGK\System\Installers
*/
class SuccessProjectInstallMiddleWare extends InstallerActionMiddleWare{
    /**
    * Returns Message.
    */
    public function getMessage(){ 
        return __("project update well done");
    }
    /**
    * Abort.
    */
    public function abort(){
    }
    /**
    * Invoke.
    */
    public function invoke(){
        $srv=$this->getServiceInfo();       
        $srv->Success=1;
        $this->next();
    }
}
/**
* Represent RenameLibaryMiddleWare class
*/
class RenameProjectMiddleWare extends InstallerActionMiddleWare{
    /**
    * auto generate doc.
    */
    public function abort(){ 
        $ctrl = $this->getServiceInfo()->Listener->controller;
        $project_name  = $this->getServiceInfo()->Listener->project_name;
        $libdir=dirname($ctrl->getDeclaredDir())."/__temp_".$project_name;
        if(is_dir($libdir)){
            rename($libdir, dirname($libdir)."/".$project_name);
        }
    }
    /**
    * auto generate doc.
    */
    public function getMessage(){
        return "rename project";
    }
    /**
    * auto generate doc.
    */
    public function invoke(){
        $ctrl = $this->getServiceInfo()->Listener->controller;
        $project_name  = $this->getServiceInfo()->Listener->project_name;
        $libdir=$ctrl->getDeclaredDir(); 
        if(is_dir($libdir)){ 
            $temp_dir = $this->getServiceInfo()->Listener->TempDir = dirname($libdir)."/__temp_".$project_name;
            rename($libdir,$temp_dir);
        }
        $this->next();
    }
}