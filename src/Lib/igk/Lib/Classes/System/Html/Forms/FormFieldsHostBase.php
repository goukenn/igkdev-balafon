<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldsHostBase.php
// @date: 20240103 19:04:35
namespace IGK\System\Html\Forms;

use IGK\System\Annotations\PhpDocBlocReader;
use IGK\System\Helpers\AnnotationHelper;
use IGK\System\Html\Forms\Validations\Annotations\ValidateWithAnnotation;
use IGK\System\Html\Forms\Validations\InspectorFormFieldValidationBase;
use IGK\System\Reflection\Helper\ReflectionHelper;
use IGKType;
use ReflectionProperty;

///<summary></summary>
/**
 * use to initialize form's field
 * @package IGK\System\Html\Forms
 * @author C.A.D. BONDJE DOUE
 */
abstract class FormFieldsHostBase extends InspectorFormFieldValidationBase
{
    /**
     * 
     * @return array 
     * @throws IGKException 
     */
    public function getFields(): array
    {
        //++ auto fields validation loading ... 
        $reflect = igk_sys_reflect_class(static::class);
        $v_uses = AnnotationHelper::GetUses(static::class);
        $reader = new PhpDocBlocReader;
        $data = [];
        $v_val = null;
        foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $props) {
            if ($props->isStatic()) {
                continue;
            }
            $comment = $props->getDocComment();
            $v_val = igk_createobj();
            $v_annotate = false;
            $v_p = null;
            $r = $n = null;
            if ($comment) {
                $v_p = $reader->readDoc($comment, $v_uses);
                foreach ($v_p->getAnnotations() as $v_a) {
                    if ($v_a instanceof ValidateWithAnnotation) {
                        $validator = $v_a->getValidator();
                        $b = array_filter((array)get_object_vars($v_a));
                        if ($validator)
                            $v_val->validator = $validator;
                        $v_val->type = self::GetFormFieldPropertyType($props, $v_a, $r);
                        foreach ($b as $k => $v) {
                            if (in_array($k, ['type'])) continue;
                            $v_val->$k = $v;
                        }
                        $v_annotate = true;
                        if (property_exists($v_a, 'default')){
                            $v_val->default = igk_getv($this, $props->name, igk_getv($v_a,'default', null));
                        }
                        break;
                    }
                }
            }
            if (!$v_annotate) {
                if (ReflectionHelper::PropertyHasType($props)) {
                    $v_gt = $props->getType();
                    $v_t = $v_gt->getName();
                    if (IGKType::IsPrimaryType($v_t)) {
                        $n = self::GetFieldTypeFromPrimitive(strtolower($v_t));
                        if (!$v_gt->allowsNull()) {
                            $r = 1;
                        }
                    }
                } else {
                    if ($v_p && $v_p->var) {
                        $v_ct = $v_p->var;
                        if (strpos($v_ct, '?') !== 0) {
                            $r = 1; // required
                        } else {
                            $v_ct = igk_str_rm_start($v_ct, '?');
                        }
                        if (IGKType::IsPrimaryType($v_ct)) {
                            $n = self::GetFieldTypeFromPrimitive(strtolower($v_ct));
                        }
                    } else {
                        $r = 1; // required
                    }
                }
                $v_val->type = $n ?? 'text';
                if ($r)
                    $v_val->required = $r;
            }
          
            $data[$props->name] = $v_val;
        }
        return $data;
    }
    public static function GetFormFieldPropertyType(ReflectionProperty $props, $v_a, & $r){
        if ($v_a){
            if (igk_getv($v_a, 'required')){
                $r = 1;
            }
            if (property_exists($v_a, 'type')){
                return self::GetFieldTypeFromPrimitive(igk_getv($v_a, 'type', 'text'));
            }
        }
        return self::GetPropsType($props, $r) ?? 'text';
    }
    public static function GetPropsType(ReflectionProperty $props, & $r){
        if (ReflectionHelper::PropertyHasType($props)) {
            $v_gt = $props->getType();
            $v_t = $v_gt->getName();
            if (IGKType::IsPrimaryType($v_t)) {
                $n = self::GetFieldTypeFromPrimitive(strtolower($v_t));

                if (!$v_gt->allowsNull()) {
                    $r = 1;
                }

                return $n;
            }
        }
        return null;
    }
    public static function GetFieldTypeFromPrimitive(string $type)
    {
        return igk_getv([
            'int' => 'number',
            'float' => 'number',
            'bool'=>'checkbox',
            'textarea'=>'textarea',
            'richtext'=>'richtext',
            'hidden'=>'hidden',
        ], $type, 'text');
    }
}
