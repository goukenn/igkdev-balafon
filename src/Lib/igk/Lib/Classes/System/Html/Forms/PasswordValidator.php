<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PasswordValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms;
use function igk_resources_gets as __;


/**
 * validate pssword from fields
 * @package IGK\System\Html\Forms
 */
class PasswordValidator extends FormFieldValidatorBase implements IFormValidator{

    /**
     * assert validation 
     * @param mixed $value 
     * @return bool 
     */
    public function assertValidate($value): bool { 
        if (!is_string($value)) return false;

        return (strlen($value)<8) || !(preg_match("/[0-9]/", $value ) // contains number
            && preg_match("/[a-z]/", $value ) // contains lowercase letter
            && preg_match("/[A-Z]/", $value ) // contains uppercase letter
            && preg_match("/[#@_]/", $value ) // contains special symbol
        );
    }


    /**
     * validate from 
     * @param mixed $value val
     * @param mixed $default 
     * @param mixed $fieldinfo 
     * @param array $error 
     * @return mixed 
     */
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        $fieldinfo = $fieldinfo ?? new FieldInfo;
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
        if ($this->assertValidate($value)){
            return $value;
        }       
        $error[] = $fieldinfo->error ?? __("password not matching criteria");        
        return $default;
    }

}