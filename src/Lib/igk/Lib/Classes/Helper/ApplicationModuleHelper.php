<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationModuleHelper.php
// @date: 20230303 18:15:13
namespace IGK\Helper;

use IGK\Controllers\ApplicationModuleConfigurationInfo;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\System\Controllers\ApplicationModules;
use IGK\System\Modules\ModuleManager;
use IGKEvents;
use stdClass;

///<summary></summary>
/**
* 
* @package IGK\Helpers
*/
class ApplicationModuleHelper{
    const SYS_ENV_KEY = 'sys://init_controller/modules';
    /**
     * get module name form class 
     * @var  $class_name get module name form class 
     */
    public static function GetModuleNameFromTestClass(string $class_name):string{
        $dir = igk_io_collapse_path(dirname(igk_sys_reflect_class($class_name)->getFileName()));
        $dir = igk_str_rm_start($dir, "%modules%/");
        if (false !== ($pos = strpos($dir, "/Lib/Tests"))){
            $dir = substr($dir, 0, $pos);
        }
        $dir = igk_str_rm_last($dir, "/Lib/Tests");
        if (empty($dir)){
            return null;
        }
        return $dir;
    }

    /**
     * import required module 
     * @param array $required_conf 
     * @param BaseController $ctrl 
     * @return void 
     */
    public static function ImportRequiredModule(array $required_conf, BaseController $ctrl){ 
        // + | load build requirement
        array_map(function($n)use ($ctrl){
            if (empty($n))return;
            $module = igk_require_module($n);
            if ($module && $module->supportMethod(\IGK\Controllers\ApplicationModuleController::INIT_METHOD )){
                igk_reg_hook(IGKEvents::HOOK_INIT_INC_VIEW, function()use($module){
                    $doc = igk_ctrl_current_doc() ?? igk_die('require document');  
                    $module->initDoc($doc); 
                });
            }
        },array_keys($required_conf));  
    }
    /**
     * get module required info
     * @return mixed|ApplicationModuleConfigurationInfo
     */
    public static function GetModuleRequireInfo(\IGK\Controllers\ApplicationModuleController $module, ?BaseController $ctrl){
        $g = igk_environment()->get(self::SYS_ENV_KEY);
        $v_cif = igk_getv($g, get_class($ctrl)); 
        $v_n = self::GetConfigKey($module); // str_replace('.', '/', ltrim($module->getName(), '.'));
        if ($info = igk_getv($v_cif, $v_n)){
            if ($info instanceof stdClass){
                $info = Activator::CreateNewInstance(ApplicationModuleConfigurationInfo::class, $info );

            }
            return $info;
        } 
    }
    /**
     * return get configuration key
     */
    public static function GetConfigKey(\IGK\Controllers\ApplicationModuleController $module):string{
        return str_replace('.', '/', ltrim($module->getName(), '.'));
    }
}