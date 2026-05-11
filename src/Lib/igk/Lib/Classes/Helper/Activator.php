<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Activator.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\Helper;
use Exception;
use IGK\Actions\IActionRequestValidator;
use IGK\System\Console\Logger;
use IGK\System\DynamicActivableReference;
use IGK\System\Http\IContentSecurityProvider;
use IGK\System\IToArray;
use IGK\System\IToJSon;
use IGK\System\Polyfill\JsonSerializableTrait;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Traits\DynamicActivableTrait;
use IGKException;
use IGKType;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

/**
 * 
 * @package IGK\Helper;
 */
/**
* auto generate doc.
* @package IGK\Helper
*/
class Activator
{
    /**
    * Property: dyn sources.
    * @var mixed
    */
    private static $sm_dyn_sources;
    /**
    * Property: dyn class.
    * @var mixed
    */
    private static $sm_dyn_class;
    /**
     * register class source 
     * @param string $interface 
     * @return true|void 
     */
    private static function __GetClassSrc(string $interface)
    {
        if (is_null(self::$sm_dyn_sources)) {
            self::$sm_dyn_sources = [];
        }
        if (isset(self::$sm_dyn_sources[$interface])) {
            return true;
        }
        $nuclass = basename(igk_dir($interface));
        $p = strtolower('___igk_dynamic_class_' . $nuclass);
        if (is_null(self::$sm_dyn_class)) {
            self::$sm_dyn_class = [];
        }
        if (isset(self::$sm_dyn_class[$p])) {
            $p .= '_' . (self::$sm_dyn_class[$p]++);
        } else {
            self::$sm_dyn_class[$p] = 1;
        }
        $dyn_trait = DynamicActivableTrait::class;
        $p_trait = JsonSerializableTrait::class;
        $ref = [];
        $ref[] = JsonSerializable::class;
        $ref[] = IToArray::class;
        $ref[] = IToJSon::class;
        $ref[] = $interface;
        $ref = implode(", ", $ref);
        $src = implode("\n", [
            '?><?php ',
            "final class {$p} implements {$ref}{",
            "    use {$dyn_trait};",
            "    use {$p_trait};",
            '    public function __construct(& $d){',
            '        $this->data = $d;',
            '    } ',
            '} ',
        ]);
        self::$sm_dyn_sources[$interface] = [$src, $p];
        eval($src);
    }
    /**
    * auto generate doc.
    * @param string $interface
    * @param mixed $resolver
    * @return object
    */
    public static function CreateFromInterface(string $interface, $resolver = null)
    {
        $root = $g = igk_sys_reflect_class($interface);
        $properties = [];
        $container = new RegexMatcherContainer;
        $patterns = [
            ["match" => "(?i)\\$[a-z_][a-z0-9_]*\b", "tokenID" => "name"],
            ["match" => "\b\w+(\s*\|\s*\w+)*\b", "tokenID" => "type"],
        ];
        $container->begin('@property\\b', '$', 'prop-detect', null, $patterns);
        $resolver = $resolver ?? function (string $type) {
            switch (strtolower($type)) {
                case 'int':
                    return 0;
            }
            return null;
        };
        $v_handler =  function ($comment) use ($container, &$properties, $resolver) {
            $offset = 0;
            /**
             * @var ?string
             */
            $type = null;
            /**
             * @var ?string
             */
            $name = null;
            while ($g = $container->detect($comment, $offset)) {
                if ($e = $container->end($g, $comment, $offset)) {
                    switch ($e->tokenID) {
                        case 'type':
                            if (!$type) {
                                $type = $e->value;
                            }
                            break;
                        case 'name':
                            $name = $e->value;
                            break;
                        default:
                            if (!is_null($name)){
                                $properties[substr($name, 1)] = $resolver($type ?? 'mixed');
                            }
                            $name = $type = null;
                            break;
                    }
                }
            }
        };
        $v_load = [];
        $tq = [$g];
        while (count($tq) > 0) {
            $g = array_shift($tq);
            if ($comment = $g->getDocComment()) {
                $v_handler($comment);
            }
            if ($g->isInterface()) {
                foreach ($g->getInterfaceNames() as $r) {
                    if (!isset($v_load[$r])) {
                        array_unshift($tq, igk_sys_reflect_class($r));
                    }
                }
            } else {
                $cv = get_class_vars($g->getName());
                foreach ($cv as $k => $value) {
                    $properties[$k] = $value;
                }
                foreach ($g->getInterfaceNames() as $r) {
                    if (!isset($v_load[$r])) {
                        array_unshift($tq, igk_sys_reflect_class($r));
                    }
                }
                if ($c = $g->getParentClass()) {
                    if (!isset($v_load[$c])) {
                        array_unshift($tq, igk_sys_reflect_class($c));
                    }
                }
            }
        }
        ksort($properties);
        if ($root->isInterface()) {
            $cl = $root->getName();
            self::__GetClassSrc($cl);
            if ($_dyn_cl =  igk_getv(self::$sm_dyn_sources[$cl], 1)) {
                return new $_dyn_cl($properties);
            }
        }
        return (object)$properties;
    }
    /**
     * use to get only public class variable. of the a class
     * @param mixed $class_name 
     * @return array 
     */
    public static function GetClassVar($class_name)
    {
        return get_class_vars($class_name);
    }
    /**
     * create new instanace and validate 
     * @param string $class_name 
     * @param mixed $data 
     * @param IContentSecurityProvider $request 
     * @param IActionRequestValidator $validator 
     * @param mixed &$errors 
     * @return mixed 
     */
    static function CreateNewInstanceWithValidation(string $class_name, $data, IContentSecurityProvider $request, IActionRequestValidator $validator, &$errors = null)
    {
        $validation = (method_exists($class_name, $fc = 'ValidationData') ?
            call_user_func_array([$class_name, $fc], [$request]) : null) ?? [];
        $m = $validator->validate(
            $data,
            $validation,
            null,
            null,
            $data,
            $errors
        );
        return $m ? self::CreateNewInstance($class_name, $data) : null;
    }
    /**
     * create from
     * @param mixed $options 
     * @param string $class_name 
     * @return mixed 
     */
    public static function CreateFrom($options, string $class_name)
    {
        if (is_null($options)) {
            $options = new $class_name;
        } else if (!($options instanceof $class_name)) {
            $options = Activator::CreateNewInstance($class_name, $options);
        }
        return $options;
    }
    /**
     * create class instance. \
     *      class must context a public constructor \
     *      data pass to it will be used to initialize public properties
     * 
     * @param string|callable|array $class_name 
     * @param mixed $data . numeric association key will be used as contructor argument
     * @param bool $fullfill fullfield with data 
     * @return object|mixed association data
     * @throws IGKException 
     * @throws Exception class not found
     */
    public static function CreateNewInstance($class_name, $data = null, bool $fullfill = false)
    {
        if ($data instanceof $class_name) {
            return $data;
        }
        $args = [];
        if (is_array($data) || (is_object($data))) {
            // + | numberic value will be used as contructor argument
            foreach ($data as $k => $v) {
                if (is_numeric($k)) {
                    $args[] = $v;
                }
            }
        }
        $class_vars  = null;
        $check_version = true;
        $v_interface = false;
        if (is_callable($class_name)) {
            $g = $class_name(...$args);
            if (is_object($g)){
                $class_vars = get_class_vars(get_class($g));
            }
        } else {
            if ((new ReflectionClass($class_name))->isInterface()) {
                $g = self::CreateFromInterface($class_name);
                $class_vars = $g->to_array();
                $check_version = false;
                $v_interface = true;
            } else {
                $g = new $class_name(...$args);
                $class_vars = get_class_vars(get_class($g));
            }
        }
        if ($data) {
            if ($fullfill) {
                foreach ($data as $k => $value) {
                    if (method_exists($g, $fc = 'set' . ucfirst($k))) {
                        $g->$fc($value);
                        continue;
                    }
                    if (property_exists($g, $k)) {
                        $g->{$k} = $value;
                    }
                }
            } else if ($class_vars) {
                $c_8_1 = version_compare(PHP_VERSION, '8.1', '>=');
                foreach ($class_vars as $k => $v) {
                    $v = igk_getv($data, $k, $g->$k) ?? $v;
                    if (method_exists($g, $fc = 'set' . ucfirst($k))) {
                        $g->$fc($v);
                        continue;
                    }
                    $is_reference = false;
                    if($v instanceof ActivatorReference){
                        $v = DynamicActivableReference::Create($v->getReference());
                        $is_reference = true;
                    }
                    if (!$v_interface){
                        $v_p = new ReflectionProperty($g, $k);
                        if ($check_version && $c_8_1) {
                            if ($v_p->isReadOnly()) {
                                continue;
                            }
                        }
                    }
                    $g->{$k} = $v;
                    if ($is_reference){
                        unset($v);
                    }
                }
            }
        }
        if ($g instanceof IActivatorMandatory) {
            foreach ($g->getMandatory() as $k) {
                if (!isset($g->{$k})) {
                    return null;
                }
            }
        }
        if (method_exists($g, 'activatorDidCreateNewInstance')) {
            $g->activatorDidCreateNewInstance();
        }
        return $g;
    }
    /**
     * auto generate doc.    
     * @param callable $callable 
     * @param mixed $inf 
     * @param mixed $def 
     * @return void 
     */
    public static function InitPrivatePropety(callable $callable, $inf, $def)
    {
        if ($fc = $callable->bindTo($inf)) {
            $fc($def);
        }
    }
    /**
     * bind value properties
     * @param mixed $p 
     * @param mixed $v 
     * @return void 
     * @throws IGKException 
     */
    public static function BindProperties($p, $v)
    {
        $tvar = array_keys(get_class_vars(get_class($p)));
        foreach ($tvar as $k) {
            $m = igk_getv($v, $k, $p->$k);
            $p->$k = $m;
        }
    }
    /**
    * auto generate doc.
    * @param string $className
    * @return array
    */
    public static function GetInstanceProperties(string $className): array
    {
        $props = [];
        $ref = new ReflectionClass($className);
        $q = [$ref];
        $treats = [];
        while (count($q) > 0) {
            $ref = array_shift($q);
            $id = $ref->getName();
            if (isset($treats[$id])) continue;
            $treats[$id] = 1;
            if ($comment = $ref->getDocComment()) {
                $props = array_merge(self::_GetDocumentProperties($comment), $props);
            }
            if ($ref->isInterface()) {
                $dt = array_values($ref->getInterfaces());
                array_unshift($q, ...$dt);
            } else {
                if ($d = $ref->getParentClass()) {
                    array_unshift($q, $d);
                }
            }
        }
        return $props;
    }
    /**
    * auto generate doc.
    * @param string $doc_comments
    * @return array
    */
    private static function _GetDocumentProperties(string $doc_comments)
    {
        $regex = new RegexMatcherContainer;
        $f_props = $regex->begin('@property\\b', '$', 'f-props')->last();
        $f_props->patterns = [
            $regex->createPattern(['match' => '([a-zA-Z][a-zA-Z]*)(\|[a-zA-Z][a-zA-Z]*)*', 'tokenID' => 'f-type']),
            $regex->createPattern(['match' => '\\$([a-zA-Z][a-zA-Z]*)(?:\\h+(.+))*', 'tokenID' => 'f-name-desc']),
        ];
        $pos = 0;
        $src = $doc_comments;
        $name = $type = null;
        $props = [];
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $tid = $e->tokenID;
                igk_is_debug() && Logger::info('tokenid:' . $tid . ' value [' . $e->value . ']');
                switch ($tid) {
                    case 'f-name-desc':
                        $name = $e->captures[1][0];
                        $desc = (($r = igk_getv($e->captures, 2)) ? $r[0] : null);
                        $props[$name] = (object)[
                            'type' => $type,
                            'desc' => $desc
                        ];
                        $type = $name = null;
                        break;
                    case 'f-type':
                        $type = $e->value;
                        break;
                }
            }
        }
        return $props;
    }
}