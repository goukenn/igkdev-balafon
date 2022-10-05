<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonProjectInstaller.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Installers;

use IGK\Controllers\BaseController;
use IGK\Helper\IO;

use function igk_resources_gets as __; 

require_once(__DIR__."/InstallerActionMiddleWare.pinc");

class BalafonProjectInstaller extends BalafonInstaller{
    protected $controller;
    protected $zipcore = false;
    protected function init_installer(InstallerMiddleWareActions $service){
        $c = igk_getr("controller");
        $key=self::INSTALLER_KEY;
        $this->controller = igk_getctrl($c, false) ?? die("controller not found:$c");

     

        $service->CoreZip = $this->zipfile; // igk_app()->session->getParam($key);
        $service->LibDir =  IGK_LIB_DIR;
        $service->controller = $this->controller;
        $service->project_name = igk_str_snake(basename(igk_dir(get_class($this->controller))));
        $service->intall_dir =  $this->controller->getDeclaredDir(); 
        
        //igk_ilog("init project installer: ".$this->zipfile);
        $service->add(new BalafonInstallerMiddelWare());
        $service->add(new BackupProjectMiddleWare($this->controller));
        $service->add(new RenameProjectMiddleWare());
        $service->add(new ExtractProjectLibaryMiddleWare());
        $service->add(new ClearCacheMiddleWare());
        $service->add(new SuccessProjectInstallMiddleWare());
    }
}


class BackupProjectMiddleWare extends InstallerActionMiddleWare{
    private $controller;
    public function __construct(BaseController $controller)
    { 
        $this->controller = $controller;
    }
    public function getMessage(){
        return __("Backup project ... {0}", get_class($this->controller));
    }
    public function abort(){

    }
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
class ExtractProjectLibaryMiddleWare extends InstallerActionMiddleWare{
    public function getMessage(){
        return __("Extract project library cache ...");
    }
    public function abort(){

    }
    public function invoke(){
        $ctrl = $this->getServiceInfo()->Listener->controller;
        $project_name  = $this->getServiceInfo()->Listener->project_name;
        $dir  = $this->getServiceInfo()->Listener->intall_dir;
        $core_zip = $this->getServiceInfo()->Listener->CoreZip;

        // 
        // extract zip 
        //
        //igk_ilog("extracting::::: ".$core_zip );
        if (empty($core_zip)){
            return;
        }
        if(!file_exists($zip=$core_zip)){
            return;
		}
        if(!igk_zip_unzip(igk_uri($zip), dirname($dir), "#^".$project_name."#")){
            return;
        }
        $temp_dir = $this->getServiceInfo()->Listener->TempDir;
        // igk_ilog("done.....remove temp dir".$temp_dir);
        IO::RmDir($temp_dir);
        $this->next();
    }
}
class SuccessProjectInstallMiddleWare extends InstallerActionMiddleWare{
    public function getMessage(){ 
        return __("project update well done");
    }
    public function abort(){

    }
    public function invoke(){
        // igk_ilog("installer complete");
        $srv=$this->getServiceInfo();       
        $srv->Success=1;
        $this->next();
    }
}


///<summary>Represente class: RenameLibaryMiddleWare</summary>
/**
* Represente RenameLibaryMiddleWare class
*/
class RenameProjectMiddleWare extends InstallerActionMiddleWare{
    ///<summary></summary>
    /**
    * 
    */
    public function abort(){ 
        $ctrl = $this->getServiceInfo()->Listener->controller;
        $project_name  = $this->getServiceInfo()->Listener->project_name;

        $libdir=dirname($ctrl->getDeclaredDir())."/__temp_".$project_name;
        if(is_dir($libdir)){
            rename($libdir, dirname($libdir)."/".$project_name);
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getMessage(){
        return "rename project";
    }
    ///<summary></summary>
    /**
    * 
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