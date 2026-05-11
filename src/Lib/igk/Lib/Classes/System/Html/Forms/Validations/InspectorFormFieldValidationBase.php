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
use IGK\System\Html\IFormFieldOptions;
use IGK\System\Html\Validations\IFormFieldValidationStoreError;

/**
* represent class that will define property required to inspect form field request
* @package IGK\System\Html\Forms\Validations
*/
abstract class InspectorFormFieldValidationBase implements 
    IFormFieldContainer{
    /**
    * validate from request
    * @param Request $request
    * @param ?array & $error
    * @return bool|mixed
    */
    public function validateFromRequest(Request $request, ?array &$error = [])
    {
        // + | --------------------------------------------------------------------
        // + | merge form data with $_REQUEST, $_POST, $_GET, $_FILES* 
        // + |
        $data = (array)$request->getFormData();  
        return $this->validate($data, $error);
    }
    /**
    * Returns Validation Fields.
    */
    protected function getValidationFields(){
        return $this->getFields(__METHOD__);
    }
    /**
    * core validation
    * @param object|array $data data to validate
    * @param ?array & $error
    * @throws IGKException
    * @throws Exception
    * @throws Error
    * @throws ArgumentTypeNotValidException
    * @throws ReflectionException
    * @return bool
    */
    public function validate($data, ?array & $error=[]){        
        $fields = $this->getValidationFields();
        $validations = [];
        $v_fileRequest = [];
        foreach ($fields as $k => $s) {
            if (is_string($s)){
                $d = new FormFieldInfo;
                $d->id = $s; 
                $d->required = true;
                $k = $s;
                $s = $d;
            }else {
                // + | --------------------------------------------------------------------                
                // + | convert to FormFieldInfo
                // + | 
                $v_validator = is_object($s) && method_exists($s, 'getValidator') ?  $s->getValidator() : null;
                $ts = (array)$s;
                if (($rs = Activator::CreateNewInstance(FormFieldInfo::class, $ts)) instanceof FormFieldInfo){                
                    $rs->validator = $v_validator;                  
                    $s = $rs;
                }
            }
            if ($s instanceof FormFieldInfo) {
                if ($s->validator) {
                    $validations[$k] = Activator::CreateNewInstance(FormFieldValidationInfo::class, $s);
                } else {
                    if ($s->type == 'file'){
                        $v_fileRequest[$k] = 1;
                    }
                    $v_validator = FormFieldValidatorBase::Factory($s->type) ;                  
                    $v_v = new FormFieldValidationInfo;
                    $v_v->validator = $v_validator? $v_validator:  new DefaultValidator;                
                    $v_v->default = $s->default;
                    $v_v->required = $s->required;
                    $v_v->error = $s->error;
                    $v_v->allowNull = $s->allowNull;
                    $v_v->allowEmpty = $s->allowEmpty;
                    $v_v->field = $s;
                    $validations[$k] = $v_v; 
                }
            }
        } 
        if ($v_fileRequest && Request::IsSupportFileRequest($data)){
            $data = new FormRequestWithFileValidationData($data);
        }
        $v_props_d = igk_reflection_get_class_properties(static::class);  
        if ($data && ($g = IGKValidator::Validate($data, $validations, $error))) {
            foreach ($v_props_d as $k) {
                $this->$k = igk_getv($g, $k);
            }
            $this->onValidationComplete($data, $validations);
            return true;
        }
        FormEnvironmentProperties::validation_error($error);
        return false;
    }
    /**
    * on validateion complete
    * @param mixed $data
    * @param mixed $validations
    * @return void
    */
    protected function onValidationComplete($data, $validations){
    }
    /**
    * auto generate doc.
    * @param null|string $class_name
    * @param ?array $def
    * @return array<string
    */
    static function GetFormDataFieldProperties(?string $class_name=null, ?array $def=null){
        $v_errors = FormEnvironmentProperties::get_validation_error(); 
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
                    $v_inf->type = trim($type, '? ');
                    $v_inf->allowEmpty= $v_inf->allowNull = !!preg_match('/^\\?/', $type);
                    $v_inf->required= !preg_match('/^\\?/', $type);
                } 
            }
            if ($v_errors){
                $r = igk_getv($v_errors, $p->name);
                if ($v_inf instanceof IFormFieldValidationStoreError)
                    $v_inf->setError($r);
            }
            if (is_null($v_inf)){
                $v_filter_p[] = $p->name;
            } else 
                $v_filter_p[$p->name] = $v_inf;
        }
        return $v_filter_p; 
    }
    /**
    * auto generate doc.
    * @param mixed $context
    * @return array
    */
    public function getFields($context = null): array { 
        $list = self::GetFormDataFieldProperties();
        return $list;
    }
}