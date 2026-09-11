<?php
// @author: C.A.D. BONDJE DOUE
// @file: ProjectBuilderHelper.php
// @date: 20230309 21:44:58
namespace IGK\System\TamTam\Helper;
/**
* auto generate doc.
* @package IGK\System\TamTam\Helper
*/
/**
* auto generate doc.
* @package IGK\System\TamTam\Helper
*/
class ProjectBuilderHelper{
    /**
    * auto generate doc.
    * @param mixed $data
    * @param mixed|FormData $setting_class
    * @param mixed & $errors
    * @return void
    */
    public static function ValidateConfigData($data, $setting_class, & $errors=null){
        /**
        * auto generate doc.
        */
        return $setting_class::ValidateData($data, null, $errors);
    }
}