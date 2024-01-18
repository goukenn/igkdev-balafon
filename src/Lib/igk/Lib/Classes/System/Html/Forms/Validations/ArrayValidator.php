<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

/**
 * use to validate an array field
 * @package IGK\System\Html\Forms\Validations
 */
class ArrayValidator   extends FormFieldValidatorBase implements IFormValidator{

    /**
     * assert value is an array
     * @param mixed $value 
     * @return bool 
     */
    public function assertValidate($value): bool {
        return is_array($value);
    }
    /**
     * validator
     * @param mixed $value 
     * @param mixed $default  
     * @param array $error 
     * @return mixed 
     */
    protected function _validate($value, $default=null, array & $error=[], ?object $options=null){ 
        
        if (empty($value)){
            if (is_array($default)){
                return $default;
            }
            return [$default];
        }
        if (!is_array($value)){
            return [$value];
        }
        return $value;
    }

}