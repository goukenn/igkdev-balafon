<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BoolValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;


class BoolValidator extends FormFieldValidatorBase implements IFormValidator{

    
    public function assertValidate($value): bool {
        return is_bool($value);
    }
    protected function _validate($value, $default, array &$error, ?object $options = null) { 
        if (is_bool($value))
            return $value;
        if (is_bool($default))
            return $default;
        return boolval($value);
    }

}