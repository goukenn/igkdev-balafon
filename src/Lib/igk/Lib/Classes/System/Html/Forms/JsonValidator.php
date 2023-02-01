<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JsonValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;


class JsonValidator implements IFormValidator{

    /**
     * validate a json data
     * @param mixed $value 
     * @param mixed $default 
     * @param mixed $fieldinfo 
     * @param array $error 
     * @return mixed 
     */
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){         
        if (json_decode($value) === null){
            $error[] = json_last_error();
            return null;
        }
        return $value;
    }
}