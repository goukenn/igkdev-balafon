<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CheckboxValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;
 

class CheckboxValidator extends BoolValidator implements IFormValidator{

    protected function _validate($value, $default=null, & $error=[], $options=null){ 
        if (is_bool($value))
            return $value;
        if (is_bool($default))
            return $default;
        return boolval($value);
    }

}