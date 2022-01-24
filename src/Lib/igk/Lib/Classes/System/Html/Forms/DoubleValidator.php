<?php
namespace IGK\System\Html\Forms;


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