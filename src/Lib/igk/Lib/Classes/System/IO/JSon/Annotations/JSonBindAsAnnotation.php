<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSonBindAsAnnotation.php
// @date: 20250128 15:42:49
namespace IGK\System\IO\JSon\Annotations;

use IGK\Controllers\BaseController;
use IGK\System\AnnotationBase;
use IGK\System\Annotations\PhpDocBlocReader;
use IGK\System\Helpers\AnnotationHelper;
use IGK\System\IO\JSon\JSonBindToConverterBase;
use IGK\System\IO\JSon\JSonObjClassConverter;
use IGKException;
use stdClass;

///<summary></summary>
/**
 * 
 * represent how to bind property when using JSon::Bind
 * @package IGK\System\IO\JSon\Annotations
 * @author C.A.D. BONDJE DOUE
 */
class JSonBindAsAnnotation extends AnnotationBase
{
    /**
     * type to bind as
     * @var mixed
     */
    var $type;

    /**
     * define whether property must be defined
     * @var ?boolean
     */
    var $required;
    public function setRequired($v)
    {
        $this->required =  boolval($v);
    }
    /**
     * 
     * @param ?string $type 
     * @return void 
     */
    public function __construct(?string $type = null)
    {
        $this->type = $type ?? 'string';
    }
    /**
     * get bind base converter 
     * @return array 
     */
    public static function GetBaseConverter()
    {
        return [
            'mixed' => function ($c) {
                return $c;
            },
            'string' => function ($c) {
                return $c . '';
            },
            'array' => function ($c) {
                if (is_array($c)) {
                    return $c;
                }
                return [$c];
            },
            'number' => function ($c) {
                return is_numeric($c) ? floatval($c) : 0.0;
            },
            'version' => function ($c) {
                $tb = explode(".", $c);
                list($major, $minor, $patch, $subversion) = igk_array_pad($tb, 4, 0);
                return (object)compact('major', 'minor', 'patch', 'subversion');
            },
            'projectURI' => function ($c, $l = null) {
                $ctrl = JSonBindAsAnnotation::GetResolvedController();
                if ($c && igk_str_startwith($c, "@/")) {
                    return $ctrl::uri($c);
                }
                return $c;
            }
        ];
    }

    /**
     * 
     * @param mixed $object_or_class 
     * @return array<int|string, mixed> 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function GetRequiredProperties($object_or_class)
    {
        $properties = self::GetJSonByAsProperties($object_or_class);
        return array_filter($properties, function ($p) {
            return $p->required;
        });
    }
    /**
     * 
     * @param mixed $class_name 
     * @return array 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function GetJSonByAsProperties($class_name, $uses = null)
    {
        $reflect = igk_sys_reflect_class($class_name);
        $cp = [];
        $reader = new PhpDocBlocReader;
        $uses = $uses ?? AnnotationHelper::GetUses($class_name);

        foreach ($reflect->getProperties() as $k) {
            $comment = $k->getDocComment();
            $p = $reader->readDoc($comment, $uses);
            $bindAnnotation = igk_getv(array_filter($p->getAnnotations(), function ($a) {
                return $a instanceof JSonBindAsAnnotation;
            }), 0);

            if ($bindAnnotation) {
                $cp[$k->getName()] = $bindAnnotation;
            } else {
                $cp[$k->getName()] = new JSonBindAsAnnotation('mixed');
            }
        }

        igk_sys_reflect_class_unset($reflect);
        return $cp;
    }

    private static function _ResolveTypeWithListener($v_typeresolve, $converter, $type){

        if ($v_typeresolve && !key_exists($type, $converter)){
            $type = $v_typeresolve($type);
        }
        return $type;
    }
    /**
     * 
     * @param mixed $value 
     * @return void 
     */
    public function Convert($value, $options)
    {
        $converter = self::GetBaseConverter();
        $v_typeresolve = $options->resolveTypeListener;



        if (preg_match('/arrayOf<(?P<tname>.+)>/', $this->type, $tab)) {
            $ctype = $tab['tname'];
            if (is_null($value)) {
                if ($this->required) {
                    throw new \IGKException('value must be defined');
                }
                return null;
            }
            // convert to array of 
            if (!is_array($value)) {
                $t = null;
                if ($value instanceof stdClass){
                    $t = (array)$value;
                    if (!igk_array_is_assoc($t)){
                        $t = null;
                    }
                }
                $value = $t ?? [$value];
            }
            $ctype = self::_ResolveTypeWithListener($v_typeresolve, $converter, $ctype);
            // + | handle convertion 
            if ($rc = self::ResolveConverter($converter, $ctype)) {
                return array_filter(array_map(function ($i) use ($rc, $options) {
                    return $rc($i, $options);
                }, $value) ?? []);
            }
        } else {
            $ctype = $this->type;
            $ctype = self::_ResolveTypeWithListener($v_typeresolve, $converter, $ctype);

            $rc = self::ResolveConverter($converter, $ctype);
            if ($rc) {
                return $rc($value, $options);
            }
        }
        if ($this->required) {
            throw new IGKException("missing value");
        }
        return $value;
    }
    /**
     * array of resolved converter
     * @param mixed $converter 
     * @param string $type 
     * @return mixed|void 
     * @throws Exception 
     */
    public static function ResolveConverter($converter, string $type)
    {
        return igk_getv($converter, $type) ?? self::GetObjConverter($type);
    }
    /**
     * 
     * @param string $type 
     * @return void 
     */
    static function GetObjConverter(string $type)
    {
        if (class_exists($type)) {
            if (is_subclass_of($type, JSonBindToConverterBase::class)) {
                return new $type;
            }
        }
        return new JSonObjClassConverter($type);
    }
    /**
     * get binding resolved controller 
     * @return mixed 
     */
    public static function GetResolvedController()
    {
        return igk_environment()->jsonBindAsAnnotationController;
    }
    /**
     * set binding resolved controller 
     * @param null|BaseController $ctrl 
     * @return void 
     */
    public static function SetResolvedController(?BaseController $ctrl)
    {
        igk_environment()->jsonBindAsAnnotationController = $ctrl;
    }
}
