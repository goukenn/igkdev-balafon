<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PasswordValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;
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
    public function assertValidate($value, ?FieldInfo $fieldInfo = null): bool { 
        if (!is_string($value)) return false;
        $v_min = IGK_PWD_LENGTH;
        $v_max = IGK_PWD_MAX_LENGTH;
        if ($fieldInfo){
            $v_min = $fieldInfo->minLength ?? $v_min;
            $v_max = $fieldInfo->maxlength ?? $v_max;
        }
        $ln = strlen($value);

        return (($ln>= $v_min) && ($ln<=$v_max))
            && preg_match("/[0-9]/", $value ) // <- contains number
            && preg_match("/[a-z]/", $value ) // <- contains lowercase letter
            && preg_match("/[A-Z]/", $value ) // <- contains uppercase letter
            && preg_match("/[#@_!\?]/", $value ) // <- contains special symbol
        ;
    }

    protected function _initFieldRequirement(){
        $f = new FieldInfo();
        $f->maxlength = IGK_PWD_MAX_LENGTH;
        $f->minLength = IGK_PWD_LENGTH;
        return $f;
    }

    /**
     * validate from 
     * @param mixed $value val
     * @param mixed $default 
     * @param mixed $fieldinfo 
     * @param array $error 
     * @return null|mixed passing value or mixed 
     */
    public function validate($value, $default=null, $fieldinfo=null, & $error=[]){ 
        $fieldinfo = $fieldinfo ?? $this->_initFieldRequirement();
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
        if (($ml = $fieldinfo->minLength) && ($ln<$ml)){
            $error[] = $fieldinfo->error ?? __("password is too short");
            return null;
        }
        if ($this->assertValidate($value, $fieldinfo)){
            return $value;
        }       
        $error[] = $fieldinfo->error ?? __("password not matching criteria");        
        return $default;
    }

}