<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilder.php
// @date: 20230309 20:56:44
namespace IGK\System\TamTam;
use IGK\Helper\Activator;
use IGK\Helper\ApplicationModuleHelper;
use IGK\Helper\IO;
use IGK\System\Configuration\ProjectSettings;
use IGK\System\Console\Logger;
use IGK\System\IO\Path;
use IGK\Constants;
/**
* Helper to project build
* @package IGK\System\TamTam
*/
class ProjectBuilder{
    /**
    * Property: setting.
    * @var mixed
    */
    protected $setting;
    /**
     * configuration file
     * @var ?string
     */
    var $configFile;
    /**
    * Constant: builder entry class.
    * @var mixed
    */
    const BUILDER_ENTRY_CLASS='System\\Build\\ProjectBuilder';
    /**
    * .ctr
    */
    public function __construct(){
        igk_reg_hook(ProjectBuilderEvents::AFTER_BUILD, [$this, 'afterBuild']);
        igk_reg_hook(ProjectBuilderEvents::BUILD, [$this, 'build']);
        igk_reg_hook(ProjectBuilderEvents::BEFORE_BUILD, [$this, 'beforeBuild']);
    }
    /**
    * Builds.
    * @param mixed $e
    */
    public function build($e){
        extract($e->args);
        if ($cl = $ctrl->resolveClass(self::BUILDER_ENTRY_CLASS)){
            Logger::warning(sprintf('missing project build for %', $ctrl));
        }
        $v_required = (array)igk_conf_get($builder->setting, 'required');
        $v_required && ApplicationModuleHelper::ImportRequiredModule($v_required, $ctrl);
        // + | do build
        $v_plugins = (array)igk_conf_get($builder->setting, 'build/plugins');
        if (!empty($v_plugins)){ 
            $chain_setting = (object)[
                'cancel'=>false
            ];
            array_map(function($n, $class_name)use($ctrl, & $chain_setting){
                if ($chain_setting->cancel){
                    return;
                } 
                $cl = igk_str_ns($class_name);
                if (!class_exists($cl) && empty($cl = $ctrl->resolveClass($class_name))){
                    Logger::danger('missing plugin class. '.$class_name);
                    $chain_setting->cancel = true;
                    return;
                }
                $args = $n;
                $plugin = null;
                if (is_object($args)){
                    $plugin = Activator::CreateNewInstance($cl, $args);
                } else if ($args){
                    $plugin = new $cl(...$args);
                }else 
                    $plugin = new $cl();
                $plugin->build($ctrl);
            },$v_plugins, array_keys($v_plugins)); 
        }
    }
    /**
    * Before build.
    * @param mixed $e
    */
    public function beforeBuild($e){
        extract($e->args);
        $install_dir = $ctrl->getDeclaredDir();
        if (is_dir($c = $ctrl->getDeclaredDir()."/.Caches")){
            IO::CleanDir($c);
        } 
        $cnf = $this->configFile ?? Constants::PROJECT_CONF_FILE;
        if (igk_io_file_exists($config_file = Path::Combine($install_dir, $cnf))){
            if ($data = json_decode(file_get_contents($config_file))){
                $cl = $this->getSettingValidationDataClass();                
                if ($cl && ($setting = $cl::ValidateData($data))){
                    $this->setting = Activator::CreateNewInstance(ProjectSettings::class, $setting->getData());
                }
            }
        } else {
            Logger::danger('missing configuration file: '. $cnf);
        }
    }
    /**
    * After build.
    * @param mixed $e
    */
    public function afterBuild($e){
    }
    /**
    * Returns Setting Validation Data Class.
    */
    protected function getSettingValidationDataClass(){
        return ProjectSettingValidationData::class;
    }
}