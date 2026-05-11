<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CheckboxValidator.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Forms\Validations;

/**
* Checkbox validator.
* @package IGK\System\Html\Forms\Validations
*/
class CheckboxValidator extends BoolValidator implements IFormValidator{
    /**
    * Validate and return a boolean value suitable for a checkbox field.
    * @param mixed $value The value to validate.
    * @param mixed $default The default value if validation fails.
    * @param mixed & $error
    * @param array $error Reference to an array collecting errors.
    * @return bool
    */
    protected function _validate($value, $default=null, & $error=[], $options=null){
        if (is_bool($value))
            return $value;
        if (is_bool($default))
            return $default;
        return boolval($value);
    }
}