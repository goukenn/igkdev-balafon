<?php
namespace IGK\System\Html\Forms;


class IntValidator implements IFormValidator{

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