<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonConfiguration.php
// @date: 20231016 15:36:08
namespace IGK\System\Console;
use IGK\Controllers\BaseController;
use IGK\System\IO\Path;
use IGK\Constants;
use IGKException;
/**
* balafon's base project configuration 
* @package IGK\System\Console
*/
class BalafonConfiguration{
    /**
    * auto generate doc.
    * @var string
    */
    var $name;
    /**
     * project author 
     * @var ?string|string[]
     */
    var $author;
    /**
     * string presentations 
     * @var ?string
     */
    var $version;
    /**
     * description of the project 
     * @var mixed
     */
    var $description;
    /**
     * keys word to used 
     * @var ?string[]
     */
    var $keywords;
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
    /**
    * auto generate doc.
    * @param BaseController $ctrl
    * @return string
    */
    public static function GetConfigFile(BaseController $ctrl): string{
        return Path::Combine($ctrl->getDeclaredDir(), Constants::PROJECT_CONF_FILE);
    }
    /**
     * load balafon configuration 
     * @param BaseController $ctrl 
     * @return mixed 
     * @throws IGKException 
     */
    public static function LoadConfig(BaseController $ctrl){
        return json_decode(file_get_contents(Path::Combine($ctrl->getDeclaredDir(), Constants::PROJECT_CONF_FILE )));
    }
    /**
    * Store config.
    * @param BaseController $ctrl
    * @param mixed $config
    */
    public static function StoreConfig(BaseController $ctrl, $config){
        $file = self::GetConfigFile($ctrl);
        igk_io_w2file($file, json_encode($config, JSON_PRETTY_PRINT));
    }
}