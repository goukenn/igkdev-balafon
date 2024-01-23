<?php
// @author: C.A.D. BONDJE DOUE
// @file: HiddenValidator.php
// @date: 20240104 16:25:04
namespace IGK\System\Html\Forms\Validations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class HiddenValidator extends FormFieldValidatorBase{

    protected function _validate($value, $default = null, array &$error = [], ?object $options = null) {
        return $value;
    }

    public function assertValidate($value): bool { 
        return !empty($value);
    }

}