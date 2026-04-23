<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IntValidator.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Forms\Validations;

/**
* Int validator.
* @package IGK\System\Html\Forms\Validations
*/
class IntValidator extends FormFieldValidatorBase  implements IFormValidator{
    /**
    * Asserts Validate.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool {
        if (is_numeric($value)){
            return true;
        }
        return false;
     }
    /**
    * Validate.
    * @param mixed $value
    * @param null|mixed $default
    * @param mixed & $error
    * @param null|object $options
    */
    protected function _validate($value, $default=null,  & $error=[], ?object $options=null){   
        $v = $value ?? $default;
        list($allowNull, $required) = igk_extract($options,'allowNull|required');
        if (is_null($v) && !$allowNull){
            $error[] = "value can't not be null";
            return;
        }
        if (is_numeric($v)){
            return intval($v);
        } 
        if ($allowNull && is_null($v)){
            return $v;
        }
        if ($required){
            $error[] = "missing provided value";
            return;
        }
        if ($value){
            $error[] = "provided value is invalid";
            return;
        }
        return 0;
    }
}