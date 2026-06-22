<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSon.php
// @date: 20230103 23:37:50
namespace IGK\Helper;
use Exception;
use IGK\System\Helpers\AnnotationHelper;
use IGK\System\IO\JSon\Annotations\JSonBindAsAnnotation;
use IGK\System\IO\JSon\JSonBindAsException;
use IGK\System\IO\JSon\JSonBindingValueOption;
use IGK\System\IO\Path;
use IGK\System\IToArrayResolver;
use IGK\System\IToJSon;
use IGK\System\Regex\Replacement;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use JsonSerializable;
use PhpParser\Node\Stmt\Continue_;
use ReflectionClass;
use stdClass;

/**
 * helper to encode in json 
 * @package IGK\Helper
 */
class JSon
{
    /**
     * Constant: map to object method.
     * @var mixed
     */
    const _map_to_object_method = '_map_to_object';
    /**
     * Constant: json pretty view.
     * @var mixed
     */
    const JSON_PRETTY_VIEW = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
    /**
     * encoding option
     * @var JSonEncodeOption
     */
    protected $m_options;
    /**
     * auto generate doc.
     * @var mixed
     */
    protected $m_data;
    /**
     * auto generate doc.
     * @var mixed
     */
    protected $m_path;
    /**
     * encode
     * @param int $encode 
     * @return string|false 
     */
    public function enc(int $encode)
    {
        if (is_null($this->m_data)) {
            return false;
        }
        $root = $this->get_root_data($this->m_data);
        return $root ? json_encode($root, $encode) : null;
    }
    /**
     * Filter array.
     * @param mixed & $tv
     */
    protected function _filter_array(&$tv)
    {
        if ($fc = $this->m_options->filter_array_listener) {
            $tv = array_values(array_filter(array_map($fc, $tv)));
        } else if ($this->m_options->ignore_empty) {
            $tv = array_filter(array_map([$this, 'filter_array'], $tv));
        }
    }
    /**
     * auto generate doc.
     * @param mixed $data
     * @return mixed
     */
    public function get_root_data($data)
    {
        $root = $keys = $c = null;
        if (is_array($data)) {
            $is_assoc = false;
            $mkeys = array_keys($data);
            $c = [];
            while (count($mkeys) > 0) {
                $k = array_shift($mkeys);
                $tv = $data[$k];
                if (!is_numeric($tv) && $this->m_options->ignore_empty && empty($tv)) {
                    continue;
                }
                if (!is_numeric($k)) {
                    $is_assoc = true;
                    $root = (object)$c;
                    $c =  $root;
                    array_unshift($mkeys, $k);
                    break;
                }
                if (is_object($tv)) {
                    if ($tv instanceof IToArrayResolver) {
                        $tv = $tv->to_array();
                    } else {
                        $tv = (array)$tv;
                    }
                }
                if (is_array($tv)) {
                    $this->_filter_array($tv);
                    $tv = array_map([$this, self::_map_to_object_method], $tv);
                }
                $c[] = $tv;
            }
            if (!$is_assoc) {
                return $c;
            }
            $keys = $mkeys;
            $c = (object)$c;
            if (empty($keys)) {
                $root = $c;
                return $root;
            }
        } else if (($data instanceof IToArrayResolver) || method_exists($data, 'to_array')) {
            $data = $data->to_array();
            $keys = array_keys($data);
        } else {
            $keys = array_keys((array)$data);
        }
        $this->_filter_array_map($data, $keys, $c, $root);
        $root = $data;
        return $root;
    }
    /**
     * Map to object.
     * @param mixed $data
     */
    protected function _map_to_object($data)
    {
        if (is_object($data) && (($data instanceof IToArrayResolver) || method_exists($data, 'to_array'))) {
            $data = array_map([$this, __FUNCTION__], $data->to_array());
        }
        return $data;
    }
    /**
    * auto generate doc.
    * @param mixed $a
    * @param mixed $option
    * @param int $flag
    * @return mixed
    */
    private static function _ConvertItemObject($a, $option=null, int $flag=0)
    {
        if ($a instanceof JsonSerializable) {
            $a = $a->jsonSerialize();
        } 
        if ($a instanceof IToArrayResolver) {
            $a = $a->to_array();
        } else if (!($a instanceof stdClass)) {
            $a = (object)(array)$a;
        }
        return $a;
    }
    /**
     * filter array 
     * @param mixed $a 
     * @return mixed 
     * @throws IGKException 
     */
    public function filter_array($a)
    {
        if (is_object($a)) {
            $a = self::_ConvertItemObject($a);
            $c = $this->get_root_data($a);
            return $c;
        } else if (is_array($a)) {
            if ($this->m_options->ignore_empty) {
                $this->_filter_array_map($a);
            }
        }
        return $a;
    }
    /**
    * auto generate doc.
    * @param mixed & $tv
    * @param null|mixed $keys
    * @param null|mixed $c
    * @param null|mixed $root
    * @return mixed
    */
    private function _filter_array_map(&$tv, $keys = null, $c = null, $root = null)
    {
        $root =  $root;
        $is_object = false;
        $parent = null;
        $tq = [['d' => $tv, 'keys' => $keys, 'c' => $c]];
        list($allow_empty_array) = igk_extract($this->m_options, 'allow_key_assoc_empty_array');
        $ignore_empty =  $this->m_options->ignore_empty;
    
        while (count($tq) > 0) {
            $q = array_shift($tq);
            extract($q,  EXTR_OVERWRITE | EXTR_REFS);
            $v = $d;
            $keys = $keys ?? array_keys((array)$d);
            $is_object = (isset($is_object) ? $is_object : null) ?? is_object($v);
            $end = false;
            if(empty($keys)){
                $c = $parent ? $parent : null;
                $parent = null;
            }
            while (!$end  && (count($keys) > 0)) {
                $k = array_shift($keys);
                if (strpos($k, "\0") === 0) {
                    // + | skip private member 
                    continue;
                }
                $tv = igk_getv($v, $k);
                if ((!is_bool($tv) && !is_numeric($tv)) && $ignore_empty && empty($tv)) {
                    if (!is_array($tv) || !$allow_empty_array)
                        continue;
                }
                if (is_null($tv) && $this->m_options->ignore_null) {
                    continue;
                }
                if (is_null($root)) {
                    $root = (object)[];
                    $c = $root;
                }
                if ($tv instanceof IToArrayResolver) {
                    $tv = $tv->to_array();
                }
                if (is_array($tv)) {
                    if ($fc = $this->m_options->filter_array_listener) {
                        $tv = array_values(array_filter(array_map($fc, $tv)));
                    } else if ($this->m_options->ignore_empty) {
                        $tv = array_filter(array_map([$this, 'filter_array'], $tv));
                    } else {
                        $tv = array_map(function ($a) {
                            if (is_object($a)) {
                                $a = self::_ConvertItemObject($a);
                            }
                            return $a;
                        }, $tv);
                    }
                } else if (is_object($tv)) {
                    if (empty((array)$tv)){
                        if (!$ignore_empty)
                            $c->$k = $tv;
                        continue;
                    }
                    if ($tv instanceof IJSonLitteral) {
                        $c->$k = $tv;
                        continue;
                    }
                    if ($tv instanceof IJSonEncodeArrayDefinition) {
                        if ($tv->isEmpty()) {
                            if ($tv->isRequired()) {
                                $c->$k = [];
                                continue;
                            }
                        }
                    }
                    if ($tv instanceof JsonSerializable) {
                        if ((($m = $tv->jsonSerialize()) === null) && ($this->m_options->ignore_null)) {
                            continue;
                        }
                        $tv = $m;
                    }
                    array_unshift($tq, ['d' => $d, 'keys' => $keys, 'c' => &$c, 'is_object' => $is_object]);
                    if (is_array($tv)){
                        $c->$k = array_filter(array_map(function ($s) {
                            return $this->_encode_data($s);
                        }, $tv));
                    } else {
                        $c->$k = new stdClass;
                        array_unshift($tq, ['d' => $tv, 'keys' => null, 'c' => &$c->$k, 'is_object' => true, 'parent'=>$c]);
                    }
                    $end = true;
                    break;
                }
                $c->$k = $tv;
            }
        }
        if (!$is_object) {
            $root = (array)$root;
        }
        $tv = $root;
    }
    /**
    * auto generate doc.
    * @param mixed $tv
    * @return mixed
    */
    private function _encode_data($tv)
    {
        if (is_object($tv)) {
            if ($tv instanceof IJSonLitteral) {
                return $tv;
            }
            if ($tv instanceof JsonSerializable) {
                if ((($m = $tv->jsonSerialize()) === null) && ($this->m_options->ignore_null)) {
                    return null;
                }
                if ($this->m_options->ignore_null){
                    $m = array_filter($m);
                }
                $tv = $m;
            }
        }
    return $tv;
    }
    /**
     * encode data
     * @param mixed $data 
     * @param mixed|JSonEncodeOption $options 
     * @param int $encode 
     * @return string|false 
     */
    public static function Encode($data, $options = null, int $encode = JSON_UNESCAPED_SLASHES)
    {
        if (is_null($options)) {
            $options = new JSonEncodeOption;
        } else if (!($options instanceof JSonEncodeOption)) {
            $options = Activator::CreateNewInstance(JSonEncodeOption::class, $options);
        }
        if ($data instanceof IToJSon) {
            return $data->to_json($options, $encode);
        }
        if ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        }
        $e = new static;
        $e->m_options = $options;
        $e->m_data = $data;
        $e->m_path = '/';
        $r = $e->enc($encode);
        // + | --------------------------------------------------------------------
        // + | treat js data
        // + | 
        if ($r) {
            $container = new RegexMatcherContainer;
            $container->begin("\"\[\[js-data:", "}]]\"", "block");
            $pos = 0;
            $o = $r;
            while ($g = $container->detect($r, $pos)) {
                $g = $container->end($g, $r, $pos);
                switch ($g->tokenID) {
                    case 'block':
                        $rp = new Replacement;
                        $rp->add("/\"\[\[js-data:\{(.+)\}\]\]\"/m", "{\\1}");
                        $t = $rp->replace($g->value);
                        $t = str_replace("\\\"", "\"", $t);
                        $t = JSTreatment::RemoveOutsideSymbol($t);
                        $o = str_replace($g->value, $t, $o);
                        break;
                }
            }
            $r = $o;
        }
        return $r;
    }
    /**
     * .ctr
     */
    protected function __construct() {}
    /**
     * code for html attribute
     * @param mixed $data 
     * @param mixed $encode_options 
     * @param int $js_options 
     * @return string|false 
     */
    public static function EncodeForHtmlAttribute($data, $encode_options, int $js_options = JSON_UNESCAPED_SLASHES)
    {
        $s = self::Encode($data, $encode_options);
        if ($s)
            return htmlentities($s);
        return false;
    }
    /**
    * bind object to data
    * @param mixed $object_or_class
    * @param mixed $data
    * @param mixed $throw_error
    * @throws JSonBindAsException missing required properties
    * @return mixed
    */
    public static function BindData($object_or_class, $data, $throw_error = true)
    {
        if (is_string($object_or_class) && class_exists($object_or_class)) {
            $object_or_class = new $object_or_class();
        }
        if ($data) {
            $tprop_class = [];
            $tprop = [['o' => $object_or_class, 'd' => $data]];
            $options = Activator::CreateNewInstance(JSonBindingValueOption::class, [
                'bindReference' => null,
                'handle' => false,
                'property' => null,
                'source' => null
            ]);
            $options->bindReference = &$tprop;
            while (count($tprop) > 0) {
                $q = array_shift($tprop);
                $obj = $q['o'];
                $class_name = get_class($obj);
                $v_use_annotation = ($class_name != \stdClass::class);
                $ref =  $v_use_annotation ? igk_sys_reflect_class($class_name) ?? new ReflectionClass($class_name) : null;
                $uses = $v_use_annotation ? AnnotationHelper::GetUses($class_name) : null;
                if ($v_use_annotation) {
                    $options->resolveTypeListener = function ($type) use (&$uses, $ref, $class_name) {
                        $v_reflect = $ref;
                        if (!($NS = $v_reflect->getNamespaceName())) {
                            return $type;
                        }
                        $d = dirname($v_reflect->getFileName());
                        $path = Path::Combine($d, $type . ".php");
                        if (igk_io_file_exists($path)) {
                            include_once($path);
                            return igk_ns_name($NS . "\\" . $type);
                        }
                    };
                    $props = igk_getv($tprop_class, $class_name, function () use ($class_name, $uses, &$tprop_class) {
                        $b = JSonBindAsAnnotation::GetJSonByAsProperties($class_name, $uses);
                        $tprop_class[$class_name] = $b;
                        return $b;
                    });
                    $ld = $q['d'];
                    foreach ($props as $k => $p) {
                        $v = igk_getv($ld, $k);
                        if ($p->required && !igk_in($ld, $k)) {
                            if (!$throw_error) continue;
                            throw new JSonBindAsException(sprintf('missing required properties [%s]', $k));
                        }
                        $options->handle = false;
                        $options->property = $k;
                        $options->source = $obj;
                        $v = $p->Convert($v, $options);
                        if ($options->handle) {
                            $options->handle = false;
                        }
                        $obj->{$k} = $v;
                    }
                    continue;
                }
                foreach ($q['d'] as $k => $v) {
                    if (!property_exists($obj, $k))
                        continue;
                    if ($ref) {
                        if ($_a = AnnotationHelper::GetPropertyAnnotation($obj, $k, $uses)) {
                            $c = igk_getv(array_filter($_a, function ($b) {
                                return $b instanceof JSonBindAsAnnotation;
                            }), 0);
                            if ($c) {
                                $options->handle = false;
                                $options->property = $k;
                                $options->source = $obj;
                                $v = $c->Convert($v, $options);
                                if ($options->handle) {
                                    $options->handle = false;
                                    $obj->{$k} = $v;
                                    continue;
                                }
                            }
                        }
                    } else {
                        if (is_object($v)) {
                            array_unshift($tprop, ['o' => $v, 'd' => $v]);
                        }
                    }
                    $obj->{$k} = $v;
                }
                $ref && igk_sys_reflect_class_unset($ref);
            }
        }
        return $object_or_class;
    }
    /**
     * auto generate doc.
     * @param mixed $data data to encode 
     * @return string|false
     */
    public static function EncodeWithNoEmpty($data)
    {
        return self::Encode($data, JSonEncodeOption::IgnoreEmpty(), JSON_UNESCAPED_SLASHES);
    }
}