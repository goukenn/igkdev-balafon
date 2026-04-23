<?php
// @author: C.A.D. BONDJE DOUE
// @file: HiddenValidator.php
// @date: 20240104 16:25:04
namespace IGK\System\Html\Forms\Validations;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class HiddenValidator extends FormFieldValidatorBase{
    /**
    * Validate.
    * @param mixed $value
    * @param null|mixed $default
    * @param array & $error
    * @param null|object $options
    */
    protected function _validate($value, $default = null, array &$error = [], ?object $options = null) {
        return $value;
    }
    /**
    * Asserts Validate.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool { 
        return !empty($value);
    }
}