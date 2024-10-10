<?php
// @author: C.A.D. BONDJE DOUE
// @file: InspectorFormFieldValidationBase.php
// @date: 20231229 09:49:58
namespace IGK\System\Html\Forms\Validations;

use Exception;
use Error;
use IGK\Helper\Activator;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Helpers\AnnotationHelper;
use IGK\System\Html\Forms\FieldInfo;
use IGK\System\Html\Forms\FormFieldInfo;
use IGK\System\Html\IFormFieldContainer;
use IGK\System\Http\Request; 
use IGKException;
use IGKValidator;
use ReflectionException;
use ReflectionProperty;
use IGK\System\Html\Forms\Validations\Annotations\FormFieldAnnotation as FormField;

///<summary>represent class that will define property required to inspect form field request</summary>
/**
* represent class that will define property required to inspect form field request
* @package IGK\System\Html\Forms\Validations
*/
abstract class InspectorFormFieldValidationBase implements 
    IFormFieldContainer{ 
     /**
     * validate from request
     * @param Request $request 
     * @return bool|mixed 
     */
    public function validateFromRequest(Request $request, ?array &$error = [])
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
    public function validate(array $data, ?array & $error=[]){        
        $fields = $this->getFields();
        $validations = [];
        foreach ($fields as $k => $s) {
            if (is_string($s)){
                $d = new FormFieldInfo;
                $d->id = $s; 
                $k = $s;
                $s = $d;
            }else {
                // + | convert to FormFieldInfo
                $s = Activator::CreateNewInstance(FormFieldInfo::class, $s);
            }
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
                    $v_v->allowNull = $s->allowNull;
                    $v_v->allowEmpty = $s->allowEmpty;
                    $validations[$k] = $v_v; 
                }
            }
        } 
        $v_props_d = igk_reflection_get_class_properties(static::class);  
        if ($data && ($g = IGKValidator::Validate($data, $validations, $error))) {
            foreach ($v_props_d as $k) {
                $this->$k = igk_getv($g, $k);
            }
            $this->onValidationComplete($data, $validations);
            return true;
        }
        return false;
    }

    /**
     * on validateion complete 
     * @return void 
     */
    protected function onValidationComplete($data, $validations){
        // override to validate 
    }

     /**
     * 
     * @param null|string $class_name 
     * @return array<string, array|IPropertieFieldInfo> 
     * @throws Exception 
     * @throws IGKException 
     */
    static function GetFormDataFieldProperties(?string $class_name=null, ?array $def=null){
        $class_name = $class_name ?? static::class;
        $v_filter_p = [];
        $v_r = igk_sys_reflect_class($class_name);
        $v_uses = AnnotationHelper::GetUses($class_name);
        foreach($v_r->getProperties(ReflectionProperty::IS_PUBLIC) as $p){
            if ($p->isStatic()) continue;
            $v_inf=null;
            if ($def){
                $v_inf = igk_getv($def, $p->name);
            }
            $v_output = []; 
            if (is_null($v_inf) && ($annotations = AnnotationHelper::GetAnnotations($p, $v_uses, [
                FormField::class
            ], $v_output))){
                if (($n = $annotations[0]) instanceof FormField){
                    $n->setInternalId($p->name);
                    $v_inf = $n;
                }
            } else {
                $r = igk_getv($v_output, 'doc');
                if ($r && $r->var){
                    $type = explode('|', $r->var, 2)[0];  
                    $v_inf = new FieldInfo;
                    $v_inf->type = $type;
                } 
            }
            if (is_null($v_inf)){
                $v_filter_p[] = $p->name;
            } else 
                $v_filter_p[$p->name] = $v_inf;
        }
        return $v_filter_p; 
    }
    /**
     * 
     * @param mixed $context 
     * @return array 
     * @throws Exception 
     * @throws IGKException 
     */
    public function getFields($context = null): array { 
        $list = self::GetFormDataFieldProperties();
        return $list;
    }
}