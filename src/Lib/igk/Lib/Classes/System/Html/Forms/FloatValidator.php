<?php
namespace IGK\System\Html\Forms;


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