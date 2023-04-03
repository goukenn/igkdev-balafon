<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilder.php
// @date: 20230309 20:56:44
namespace IGK\System\TamTam;

use IGK\Helper\Activator;
use IGK\Helper\IO;
use IGK\System\Console\Logger;

///<summary></summary>
/**
* 
* @package IGK\System\TamTam
*/
class ProjectBuilder{

    protected $setting;

    public function __construct(){
        igk_reg_hook(ProjectBuilderEvents::AFTER_BUILD, [$this, 'afterBuild']);
        igk_reg_hook(ProjectBuilderEvents::BUILD, [$this, 'build']);
        igk_reg_hook(ProjectBuilderEvents::BEFORE_BUILD, [$this, 'beforeBuild']);
    }
    public function build($e){
        extract($e->args);
        if ($cl = $ctrl::resolveClass(\System\Build\ProjectBuilder::class)){
            Logger::warning(sprintf('missing project build for %', $ctrl));
        }
    }
    public function beforeBuild($e){
        extract($e->args);
        $install_dir = $ctrl->getDeclaredDir();
        if (is_dir($c = $ctrl->getDeclaredDir()."/.Caches")){
            IO::CleanDir($c);
        } 
        if (file_exists($config_file = $install_dir."/balafon.config.json")){
            if ($data = json_decode(file_get_contents($config_file))){
                $cl = $this->getSettingValidationDataClass();
                $g = new $cl();
                if ($setting = $cl::ValidateJson($data)){
                    $this->setting = Activator::CreateNewInstance(ProjectSettings::class, $data);
                }
            }
        }
    }
    public function afterBuild($e){

    }
    protected function getSettingValidationDataClass(){
        return ProjectSettingValidationData::class;
    }
}