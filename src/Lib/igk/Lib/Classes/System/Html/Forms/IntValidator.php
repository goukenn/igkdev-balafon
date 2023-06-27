<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IntValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;


class IntValidator extends FormFieldValidatorBase  implements IFormValidator{

    public function assertValidate($value): bool {
        if (is_numeric($value)){
            return true;
        }
        return false;
     }

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (is_numeric($value)){
            return intval($value);
        }
        if (is_numeric($default)){
            return intval($default);
        }
        return 0;
    }

}