<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JsonValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\IFormFieldOptions;

class JsonValidator extends FormFieldValidatorBase implements IFormValidator{

    public function assertValidate($value): bool { 
        return json_decode($value)!==false;
    }

    /**
     * validate a json data
     * @param mixed $value 
     * @param mixed $default 
     * @param mixed $fieldinfo 
     * @param array $error 
     * @return mixed 
     */
    public function validate($value, $default=null, ?IFormFieldOptions $fieldinfo=null, & $error=[]){         
        if (json_decode($value) === null){
            $error[] = json_last_error();
            return null;
        }
        return $value;
    }
}