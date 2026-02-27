<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssocArrayValidator.php
// @date: 20231229 17:05:43
namespace IGK\System\Html\Forms\Validations;
use function igk_resources_gets as __;

/**
* auto generate doc.
* @package IGK\System\Html\Forms\Validations
*/
class AssocArrayValidator extends FormFieldValidatorBase{

    /**
    * Validate.
    * @param mixed $value
    * @param null|mixed $default
    * @param array & $error
    * @param null|object $options
    */
    protected function _validate($value, $default=null, array &$error =[], ?object $options = null) { 
        if ($this->assertValidate($value)){
            return $value;
        }
        $error[] = __('not an associative array');
    }

    /**
    * Asserts Validate.
    * @param mixed $value
    * @return bool
    */
    public function assertValidate($value): bool { 
        return (is_array($value) || is_object($value)) && igk_array_is_assoc_only((array)$value);
    }
}