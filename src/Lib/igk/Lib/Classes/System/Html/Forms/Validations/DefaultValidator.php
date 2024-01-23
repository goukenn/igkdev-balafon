<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DefaultValidator.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Forms\Validations;

use IGK\System\Html\Forms\IFormValidationField;
use IGKException;

/**
 * represent a default form field validator
 * @package IGK\System\Html\Forms
 */
class DefaultValidator extends FormFieldValidatorBase implements IFormValidator, IFormValidationFieldHost{
    protected $_fieldInfo;

    /**
     * set validation field
     * @param null|IFormValidationField $field 
     * @return void 
     */
    public function setFieldInfo(?IFormValidationField $field){
        $this->_fieldInfo = $field;
    }
    public function getFieldInfo():?IFormValidationField{
        return $this->_fieldInfo;
    }
    
    public function assertValidate($value): bool { 
        return true;
    } 

    /**
     * required field info as options
     * @param mixed $value 
     * @param mixed $default 
     * @param array $error 
     * @param null|object $options 
     * @return mixed 
     * @throws IGKException 
     */
    protected function _validate($value, $default=null, & $error=[], $options=null){ 
      
        if (empty($value)){
            return $default;
        }
        $fieldinfo = igk_getv($options, self::FIELD_INFO_PROPERTY, $this->_fieldInfo); 
        if (!$fieldinfo){
            return $default;
        } 
        if ($fieldinfo instanceof IFormValidationField){  
            $type = $fieldinfo->type;
            if (empty($type)){
                $type = 'custom';
            }
            if ($type=="json")
            {
                igk_wln_e("json validation ");
            }     
            $s = htmlentities($value);
            $maxln = $fieldinfo->maxLength;
            $minln = $fieldinfo->minLength ?? 0;
            $pattern = $fieldinfo->pattern;
            if (($maxln > $minln) && (strlen($s) > $maxln)){
                $error[] = "length not match ".$fieldinfo->name;
                return null;
            }
            if ($pattern && !preg_match("/".$pattern."/",$s)){
                $error[] = "pattern not match ".$fieldinfo->name;
                return null;
            }
            return $s;
        }
        $error[] = 'no form field ';
        return null;
    }

}