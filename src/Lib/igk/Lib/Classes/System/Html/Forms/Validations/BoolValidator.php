<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BoolValidator.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Forms\Validations;

/**
* Bool validator.
* @package IGK\System\Html\Forms\Validations
*/
class BoolValidator extends FormFieldValidatorBase implements IFormValidator{
    /**
     * Assert that the given value is a boolean.
     * @param mixed $value The value to check.
     * @return bool
     */
    public function assertValidate($value): bool {
        return is_bool($value);
    }
    /**
     * Validate and return a boolean value from the input.
     * @param mixed $value The value to validate.
     * @param mixed $default The default value if validation fails.
     * @param array $error Reference to an array collecting errors.
     * @param object|null $options Optional validation options.
     * @return bool
     */
    protected function _validate($value, $default, array &$error, ?object $options = null) {
        if (is_bool($value))
            return $value;
        if (is_bool($default))
            return $default;
        return boolval($value);
    }
}