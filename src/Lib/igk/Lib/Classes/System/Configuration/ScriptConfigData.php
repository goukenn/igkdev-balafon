<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScriptConfigData.php
// @date: 20241123 15:58:18
namespace IGK\System\Configuration;

use IGK\Controllers\BaseController;
use IGK\Helper\Activator;
use IGK\System\ConfigurationFile;
use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\System\Configuration
* @author C.A.D. BONDJE DOUE
*/
class ScriptConfigData{

    var $main;

    public function __construct()
    {
        $this->main = ConfigurationFile::DEFAULT_MAINJS;
    }

    /**
     * 
     * @param BaseController $ctrl 
     * @return void 
     */
    public static function GetControllerMainScript(BaseController $ctrl){
        if (($g = $ctrl->envConfiguration()) instanceof ConfigurationFile){
            if ($g->scripts){
                $e = Activator::CreateNewInstance(static::class, $g->scripts);
                return $e->main ?? ConfigurationFile::DEFAULT_MAINJS;
            } 
            if ($g->mainJS){
                return $g->mainJS;
            } 
        } 
        $cnf = igk_environment()->scriptsConfig;
        if ($cnf instanceof self){
            return $cnf->main;
        }
        return ConfigurationFile::DEFAULT_MAINJS; 
    }
}