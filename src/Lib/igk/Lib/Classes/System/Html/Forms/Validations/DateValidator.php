<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DateValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;


class DateValidator extends FormFieldValidatorBase implements IFormValidator{

    public function assertValidate($value): bool {
        return is_string($value);
    } 

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if ($default === null){
            $default = date("Y-m-d");
        }
        if ($d = strtotime($value)){
            return date("Y-m-d", $d);
        }
        return $default;
    }
}