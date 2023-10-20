<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonConfiguration.php
// @date: 20231016 15:36:08
namespace IGK\System\Console;

use IGK\Controllers\BaseController;
use IGK\System\IO\Path;
use IGKConstants;
use IGKException;

///<summary></summary>
/**
* balafon's base project configuration 
* @package IGK\System\Console
*/
class BalafonConfiguration{
    var $name;
    var $author;
    var $version;
    var $description;
    /**
     * array of require module
     * @var ?array
     */
    var $required;
    /**
     * build setting
     * @var mixed
     */
    var $build;
    public static function GetConfigFile(BaseController $ctrl){
        return Path::Combine($ctrl->getDeclaredDir(), IGKConstants::PROJECT_CONF_FILE );
    }
    /**
     * load balafon configuration 
     * @param BaseController $ctrl 
     * @return mixed 
     * @throws IGKException 
     */
    public static function LoadConfig(BaseController $ctrl){
        return json_decode(file_get_contents(Path::Combine($ctrl->getDeclaredDir(), IGKConstants::PROJECT_CONF_FILE )));
    }
    public static function StoreConfig(BaseController $ctrl, $config){
        $file = self::GetConfigFile($ctrl);

        igk_io_w2file($file, json_encode($config, JSON_PRETTY_PRINT));
    }
}