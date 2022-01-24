<?php
namespace IGK\System\Html\Forms;


class JsonValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        
        if (json_decode($value) === null){
            $error[] = json_last_error();
            return null;
        }
        return $value;
    }
}