<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConfigHelper.php
// @date: 20221220 11:42:40
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGKException;

///<summary></summary>
/**
* configuration helper
* @package IGK\Helper
*/
abstract class ConfigHelper{
    /**
     * resolv key config
     * @param BaseController $controller 
     * @param string $key 
     * @param mixed $value 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetConfig(BaseController $controller, string $key, $value=null){
        return $value ?? $controller->getConfig($key) ?? igk_configs()->get($key);
    }
}