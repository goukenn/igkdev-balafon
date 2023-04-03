<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PasswordValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;
use function igk_resources_gets as __;


/**
 * 
 * @package IGK\System\Html\Forms
 */
class PasswordValidator implements IFormValidator{

    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        if (empty($value)){
            if ($fieldinfo->required){
                $error[] = __("password is empty");
            }
            return null;
        }
        $ln = strlen($value);
        
        if (($ml = $fieldinfo->maxlength) && ($ln>$ml)){
            $error[] = $fieldinfo->error ?? __("password is too long");
            return null;
        }
        if ((strlen($value)<8) || !(
                preg_match("/[0-9]/", $value ) // contains number
                && preg_match("/[a-z]/", $value ) // contains lowercase letter
                && preg_match("/[A-Z]/", $value ) // contains uppercase letter
                && preg_match("/[#@_]/", $value ) // contains special symbol
            )
            ){
            $error[] = $fieldinfo->error ?? __("password not matching criteria");
            return null;
        }
        return $value;
    }

}