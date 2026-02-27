<?php
// @author: C.A.D. BONDJE DOUE
// @file: StrictVersionValidator.php
// @date: 20231229 18:01:42
namespace IGK\System\Html\Forms\Validations;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
*/
class StrictVersionValidator extends FormFieldValidatorBase{

    /**
    * Validate.
    * @param mixed $data
    * @param null|mixed $default
    * @param array & $error
    * @param null|object $options
    */
    protected function _validate($data, $default=null, array &$error=[], ?object $options = null) { 
        if ($this->assertValidate($data)){
            return $data;
        }
        return $default;   
    }

    /**
    * Asserts Validate.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool {
        return is_int($value) || (is_string($value) && version_compare($value, "0", '>'));
    }
}