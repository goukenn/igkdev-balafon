<?php
namespace IGK\System\Html\Forms;


class DateValidator implements IFormValidator{

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