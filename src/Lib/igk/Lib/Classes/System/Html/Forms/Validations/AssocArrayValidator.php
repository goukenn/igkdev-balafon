<?php
// @author: C.A.D. BONDJE DOUE
// @file: AssocArrayValidator.php
// @date: 20231229 17:05:43
namespace IGK\System\Html\Forms\Validations;
use function igk_resources_gets as __;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
class AssocArrayValidator extends FormFieldValidatorBase{

    protected function _validate($value, $default=null, array &$error =[], ?object $options = null) { 
        if ($this->assertValidate($value)){
            return $value;
        }
        $error[] = __('not an associative array');
    }

    public function assertValidate($value): bool { 
        return (is_array($value) || is_object($value)) && igk_array_is_assoc_only((array)$value);
    }

}