<?php
// @author: C.A.D. BONDJE DOUE
// @file: StrictVersionValidator.php
// @date: 20231229 18:01:42
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
*/
class StrictVersionValidator extends FormFieldValidatorBase{

    protected function _validate($data, $default=null, array &$error=[], ?object $options = null) { 
        if ($this->assertValidate($data)){
            return $data;
        }
        return $default;   
    }

    public function assertValidate($value): bool {
        return is_int($value) || (is_string($value) && version_compare($value, "0", '>'));
    }

}