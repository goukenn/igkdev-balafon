<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormEnvironmentProperties.php
// @date: 20241108 19:04:25
namespace IGK\System\Html\Forms\Validations;
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
* @property static $validation_error
*/
abstract class FormEnvironmentProperties{
    const _KEY_FORMAT = 'form_env_validation_%s';
    /**
     * store validation error
     * @param mixed $error 
     * @return void 
     */
    public static function validation_error($error){
        return self::_setEnv(__FUNCTION__, $error);
    }
    /**
     * get store validation 
     * @return mixed 
     */
    public static function get_validation_error(){
        return self::_getEnv(__FUNCTION__);
    }
    /**
     * environment getEnv
     * @param mixed $name 
     * @return mixed 
     */
    protected static function _getEnv($name){
        $c =  igk_environment()->get(sprintf(self::_KEY_FORMAT, igk_str_rm_start($name,'get_'))); 
        return $c;
    }
    /**
     * environment setup
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    protected static function _setEnv($name, $value){
        return igk_environment()->set(sprintf(self::_KEY_FORMAT, $name), $value);
    }
}