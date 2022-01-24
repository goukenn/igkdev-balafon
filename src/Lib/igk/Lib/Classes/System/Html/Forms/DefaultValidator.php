<?php
namespace IGK\System\Html\Forms;


class DefaultValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (empty($value)){
            return $default;
        }
        $s = htmlentities($value);
        $maxln = $fieldinfo->maxlength;
        $pattern = $fieldinfo->pattern;
        if (($maxln>0) && (strlen($s) > $maxln)){
            $error[] = "length not match ".$fieldinfo->name;
            return null;
        }
        if ($pattern && !preg_match("/".$pattern."/",$s)){
            $error[] = "pattern not match ".$fieldinfo->name;
            return null;
        }
        return $s;
    }

}