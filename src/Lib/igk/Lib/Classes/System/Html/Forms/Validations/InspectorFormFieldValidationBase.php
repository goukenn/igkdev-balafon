<?php
// @author: C.A.D. BONDJE DOUE
// @file: InspectorFormFieldValidationBase.php
// @date: 20231229 09:49:58
namespace IGK\System\Html\Forms\Validations;

use Exception;
use Error;
use IGK\Helper\Activator;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Forms\FormFieldInfo;
use IGK\System\Html\IFormFieldContainer;
use IGK\System\Http\Request;
use IGKException;
use IGKValidator;
use ReflectionException; 

///<summary></summary>
/**
* represent class that will define property required to inspect form field request
* @package IGK\System\Html\Forms\Validations
*/
abstract class InspectorFormFieldValidationBase implements IFormFieldContainer{
    abstract function getFields(): array;
     /**
     * validate from request
     * @param Request $request 
     * @return bool|mixed 
     */
    public function validateFromRequest(Request $request, array &$error = [])
    {
        $data = (array)$request->getFormData();  
        return $this->validate($data, $error);
    }
    /**
     * core validation
     * @param array $data 
     * @param mixed $error 
     * @return bool 
     * @throws IGKException 
     * @throws Exception 
     * @throws Error 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function validate(array $data, & $error){        
        $fields = $this->getFields();
        $validations = [];
        foreach ($fields as $k => $s) {

            $s = Activator::CreateNewInstance(FormFieldInfo::class, $s);
            if ($s instanceof FormFieldInfo) {
                if ($s->validator) {
                    // convert to formFieldValidationInfo
                    $validations[$k] = Activator::CreateNewInstance(FormFieldValidationInfo::class, $s);
                } else {
                    // create a validation depending on type
                    $v_validator = FormFieldValidatorBase::Factory($s->type) ;                  
                    $v_v = new FormFieldValidationInfo;
                                    
                    $v_v->validator = $v_validator? $v_validator:  new DefaultValidator;                
                    $v_v->default = $s->default;
                    $v_v->required = $s->required;
                    $v_v->error = $s->error;
                    $validations[$k] = $v_v; 
                }
            }
        } 
        $v_props_d = igk_reflection_get_class_properties(static::class);  
        //igk_wln("validateion === ", $validations);
        if ($data && ($g = IGKValidator::Validate($data, $validations, $error))) {
            foreach ($v_props_d as $k) {
                $this->$k = igk_getv($g, $k);
            }
            igk_wln_e("the g ", $this, $data, $v_props_d, $g, $validations['calendar_id']->validator);
            return true;
        }
        return false;
    }
}

