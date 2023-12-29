<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DoubleValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;


class DoubleValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (is_numeric($value)){
            return doubleval($value);
        }
        if (is_numeric($default)){
            return doubleval($default);
        }
        return 0.0;
    }
}