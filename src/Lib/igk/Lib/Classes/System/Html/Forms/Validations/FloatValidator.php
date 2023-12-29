<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FloatValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;


class FloatValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (is_numeric($value)){
            return floatval($value);
        }
        if (is_numeric($default)){
            return floatval($default);
        }
        return 0.0;
    }
}