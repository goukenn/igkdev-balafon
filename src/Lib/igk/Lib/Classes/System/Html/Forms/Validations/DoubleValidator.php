<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DoubleValidator.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Forms\Validations;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
*/
class DoubleValidator extends FormFieldValidatorBase implements IFormValidator{
    /**
     * Assert that the given value is numeric.
     * @param mixed $value The value to check.
     * @return bool
     */
    public function assertValidate($value): bool {
        return is_numeric($value)
;    }
    /**
     * Validate and return a double value from the input.
     * @param mixed $value The value to validate.
     * @param mixed $default The default value if validation fails.
     * @param array $error Reference to an array collecting errors.
     * @param mixed $object Optional context object.
     * @return float
     */
    protected function _validate($value, $default=null, & $error=[], $object=null){
        if (is_numeric($value)){
            return doubleval($value);
        }
        if (is_numeric($default)){
            return doubleval($default);
        }
        return 0.0;
    }
}
