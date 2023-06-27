<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ArrayValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;


class ArrayValidator   extends FormFieldValidatorBase implements IFormValidator{

    public function assertValidate($value): bool {
        return is_array($value);
    }

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (empty($value)){
            if (is_array($default)){
                return $default;
            }
            return [$default];
        }
        if (!is_array($value)){
            return [$value];
        }
        return $value;
    }

}