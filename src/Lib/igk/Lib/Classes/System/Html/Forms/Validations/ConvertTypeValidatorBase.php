<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConvertTypeValidatorBase.php
// @date: 20231230 08:47:47
namespace IGK\System\Html\Forms\Validations;

use IGK\Helper\Activator;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D BONDJE DOUE
*/
abstract class ConvertTypeValidatorBase extends FormFieldValidatorBase{
    protected $m_type;

    /**
     * the return type 
     * @return null|string 
     */
    public function getReturnType():?string{
        return $this->m_type;
    }
    /**
     * set the return type class 
     * @param null|string $type 
     * @return void 
     */
    public function setReturnType(?string $type){
        $this->m_type = $type;
    }
    /**
     * assert that the value can be convert to type
     * @param mixed $value 
     * @return bool 
     */
    public function assertValidate($value): bool {
        return is_null($value) || is_object($value) || is_array($value);
    }
    protected function _validate($value, $default = null, array &$error = [], ?object $options = null) { 
        $v_fv = new FormValidation;
        $v_fv->storage = false;
        if ($this->assertValidate($value)){ 
            $g = $v_fv->fields($this->getFields())->validate((array)$value);
            if ($g === false){
                $error = $v_fv->getErrors(); 
            } else {
                if ($this->m_type){
                    $g = Activator::CreateNewInstance($this->m_type, $g);
                }
            }
            return $g; 
        }
        $error[] = 'not a valid data';
    }
    abstract function getFields(): array;
}