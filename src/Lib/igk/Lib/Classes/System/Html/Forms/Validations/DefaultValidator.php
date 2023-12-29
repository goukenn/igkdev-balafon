<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DefaultValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

/**
 * represent a default form field validator
 * @package IGK\System\Html\Forms
 */
class DefaultValidator extends FormFieldValidatorBase implements IFormValidator{

    public function assertValidate($value): bool { 
        return true;
    }

 

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (empty($value)){
            return $default;
        }
        $type = $fieldinfo->type;
        if (empty($type)){
            $type = 'custom';
        }
        // if ($type!='custom')
        // igk_wln_e("type: ", $fieldinfo->type);

        if ($fieldinfo->type=="json")
        {
            igk_wln_e("json validation ");
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