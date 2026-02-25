<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DateValidator.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Forms\Validations;
class DateValidator extends FormFieldValidatorBase implements IFormValidator{
    /**
     * Assert that the given value is a string.
     * @param mixed $value The value to check.
     * @return bool
     */
    public function assertValidate($value): bool {
        return is_string($value);
    }
    /**
     * Validate and return a date string in Y-m-d format.
     * @param mixed $value The date value to validate.
     * @param mixed $default Default date string if value cannot be parsed.
     * @param mixed $fieldinfo Optional field metadata.
     * @param array $error Reference to an array collecting errors.
     * @return string
     */
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){
        if ($default === null){
            $default = date("Y-m-d");
        }
        if ($d = strtotime($value)){
            return date("Y-m-d", $d);
        }
        return $default;
    }
}
