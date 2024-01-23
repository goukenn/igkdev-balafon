<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConvertTypeValidatorBase.php
// @date: 20231230 08:47:47
namespace IGK\System\Html\Forms\Validations;

use IGK\Helper\Activator;
use function igk_resources_gets as __;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Forms\Validations
 * @author C.A.D BONDJE DOUE
 */
abstract class ConvertTypeValidatorBase extends FormFieldValidatorBase
{
    protected $m_type;
    private $m_supportArray = true;

    /**
     * 
     * @param bool $support_array 
     * @return $this 
     */
    public function supportArray(bool $support_array): static{
        $this->m_supportArray = $support_array;
        return $this;
    }
    public function getSupportArray():bool{
        return $this->m_supportArray;
    }
    /**
     * the return type 
     * @return null|string 
     */
    public function getReturnType(): ?string
    {
        return $this->m_type;
    }
    /**
     * set the return type class 
     * @param null|string $type 
     * @return static 
     */
    public function returnType(?string $type):static
    {
        $this->m_type = $type;
        return $this;
    }
    /**
     * assert that the value can be convert to type
     * @param mixed $value 
     * @return bool 
     */
    public function assertValidate($value): bool
    {
        return is_null($value) || is_object($value) || is_array($value);
    }
    protected function _validate($value, $default = null, array &$error = [], ?object $options = null)
    {
        $v_fv = new FormValidation;
        $v_fv->storage = false;
        $v_fc = function($v_fv, $value, $v_fields, & $error){
            $g = $v_fv->fields($v_fields)->validate((array)$value);
            if ($g === false) {
                $error = $v_fv->getErrors();
            } else {
                if ($this->m_type) {
                    $g = Activator::CreateNewInstance($this->m_type, $g);
                }
            }
            return $g;
        };
        if ($this->assertValidate($value)) {
            $v_fields = $this->getFields();
            $v_is_index = is_array($value) && igk_array_is_indexed($value);
            if ($v_is_index) {
                if (!$this->getSupportArray()){
                    $error = __(Errors::GetErrors(Errors::DISABLE_ARRAY));
                     //"converter disable array";
                    return false;
                }
                $q = $value;
                $r = [];
                while(count($q)>0){
                    $b = array_shift($q);
                    $e = [];
                    $g = $v_fc($v_fv, $b, $v_fields, $e);
                    if (($g===false) && ($e)){
                        $error = $e;
                        break;
                    }
                    $r[] = $g;
                }
                return $r ? $r : false;

            } else {
                $e = [];
                $g = $v_fc($v_fv, $value, $v_fields, $e);
                if (($g===false) && ($e)){
                    $error = $e; 
                }
                // $g = $v_fv->fields($v_fields)->validate((array)$value);
                // if ($g === false) {
                //     $error = $v_fv->getErrors();
                // } else {
                //     if ($this->m_type) {
                //         $g = Activator::CreateNewInstance($this->m_type, $g);
                //     }
                // }
                return $g;
            }
        }
        $error[] = 'not a valid data';
    }
    abstract function getFields(): array;
}
