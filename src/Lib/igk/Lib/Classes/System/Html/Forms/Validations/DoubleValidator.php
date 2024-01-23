<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DoubleValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;


class DoubleValidator extends FormFieldValidatorBase implements IFormValidator{

    public function assertValidate($value): bool { 
        return is_numeric($value)
;    }

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