<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BoolValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;


class BoolValidator extends FormFieldValidatorBase implements IFormValidator{

    public function assertValidate($value): bool {
        return is_bool($value);
     }

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (is_bool($value))
            return $value;
        if (is_bool($default))
            return $default;
        return boolval($value);
    }

}