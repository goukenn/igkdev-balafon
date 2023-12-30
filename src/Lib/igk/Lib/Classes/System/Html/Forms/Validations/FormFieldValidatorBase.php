<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldValidatorBase.php
// @date: 20230427 10:47:00
namespace IGK\System\Html\Forms\Validations;

use IGK\Helper\Activator;
use IGK\System\Html\Forms\Validations\FormValidationParamOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D BONDJE DOUE
*/
abstract class FormFieldValidatorBase implements IFormValidator{     
    final public function validate($value, $default=null, & $error=[]){
        $v_is_require = false;
        $v_allow_null = false; 
        $v_output = null;
        $options = null;
        //+|filter option object        
        if ($value instanceof FormValidationParam){
            // ignore the reset of the param
            $error = & $value->error;
            $v_output = & $value->output;    
            $options = Activator::CreateNewInstance(FormValidationParamOptions::class , $value);

            $v_output = $this->_validate($value->input, $value->default, $error, $options);
        } else {  
            if (func_num_args()>3){
                $targ = func_get_args();
                $v_is_require = igk_getv($targ, 3);
                $v_allow_null = igk_getv($targ, 3);
            }
            $options = new FormValidationParamOptions;
            $options->allowNull = $v_allow_null;
            $options->required = $v_is_require;
            $v_output = $this->_validate($value, $default, $error, $options);
        }
        return $v_output; 
    }
    /**
     * effective validate data
     * @param mixed $value value data to validate
     * @param mixed $default the default value in case of not valid data if required
     * @param array $error error data 
     * @param null|object $options extra options
     * @return mixed 
     */
    protected abstract function _validate($value, $default=null, array & $error=[], ?object $options=null);

    /**
     * factory form field creation validator
     * @param string $name 
     * @return ?FormFieldValidatorBase 
     */
    public static function Factory(string $name): ?FormFieldValidatorBase{
        $cl = __NAMESPACE__."\\".ucfirst($name."Validator");
        if (class_exists($cl) && is_subclass_of($cl, FormFieldValidatorBase::class)){
            $v_validator = new $cl;
            return $v_validator;
        }
        return null;
    }
}