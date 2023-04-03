<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderHelper.php
// @date: 20230309 21:44:58
namespace IGK\System\TamTam\Helper;


///<summary></summary>
/**
* 
* @package IGK\System\TamTam\Helper
*/
class ProjectBuilderHelper{
    /**
     * 
     * @param mixed $data 
     * @param mixed|FormData $setting_class 
     * @return void 
     */
    public static function ValidateConfigData($data, $setting_class, & $errors=null){
        /**
         * 
         */
        return $setting_class::ValidateData($data, null, $errors);
    }
}