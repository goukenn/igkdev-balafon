<?php
namespace IGK\System\Html\Forms;


class ArrayValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (empty($value)){
            if (is_array($default)){
                return $default;
            }
            return [$default];
        }
        if (!is_array($value)){
            return [$value];
        }
        return $value;
    }

}