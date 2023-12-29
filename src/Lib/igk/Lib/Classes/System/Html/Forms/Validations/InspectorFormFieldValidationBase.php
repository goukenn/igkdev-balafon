<?php
// @author: C.A.D. BONDJE DOUE
// @file: InspectorFormFieldValidationBase.php
// @date: 20231229 09:49:58
namespace IGK\System\Html\Forms\Validations;

use FormFieldValidation;
use IGK\Helper\Activator;
use IGK\System\Html\Forms\FormFieldInfo;
use IGK\System\Http\Request;
use IGKValidator;
use ReflectionProperty;

///<summary></summary>
/**
* represent class that will define property required to inspect form field request
* @package IGK\System\Html\Forms\Validations
*/
abstract class InspectorFormFieldValidationBase{
    abstract function getFormFields(): array;
     /**
     * validate from request
     * @param Request $request 
     * @return bool|mixed 
     */
    public function validateFromRequest(Request $request, array &$error = [])
    {
        $fields = $this->getFormFields();
        $validations = [];
        foreach ($fields as $k => $s) {

            $s = Activator::CreateNewInstance(FormFieldInfo::class, $s);
            if ($s instanceof FormFieldInfo) {
                if ($s->validator) {
                    // convert to formFieldValidationInfo
                    $validations[$k] = Activator::CreateNewInstance(FormFieldValidationInfo::class, $s);
                } else {
                    // create a validation depending on type
                    $cl = __NAMESPACE__."\\".ucfirst($s->type."Validator");
                    if (class_exists($cl) && is_subclass_of($cl, FormFieldValidatorBase::class)){
                        $v_validator = new $cl;
                        $v_validator->required = $s->required;
                        $validations[$k] = $v_validator;
                    } else {
                        $v_v = new FormFieldValidationInfo;
                        $v_v->validator = new DefaultValidator;
                        $v_v->default = $s->default;
                        $v_v->required = $s->required;
                        $v_v->error = $s->error;
                        $validations[$k] = $v_v; 
                    } 
                }
            }
        }
        $data = $request->getFormData(); 
        $v_props_d = igk_reflection_get_class_properties(static::class); 
        if ($data && ($g = IGKValidator::Validate($data, $validations, $error))) {
            foreach ($v_props_d as $k) {
                $this->$k = igk_getv($g, $k);
            }
            return true;
        }
        return false;
    }
}

