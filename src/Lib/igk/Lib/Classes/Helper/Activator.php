<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Activator.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\Helper;

use Exception;
use IGK\Actions\IActionRequestValidator; 
use IGK\System\Http\IContentSecurityProvider;
use IGK\System\IToArray;
use IGK\System\IToJSon;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Traits\DynamicActivableTrait;
use IGKException;
use JsonSerializable;

/**
 * 
 * @package IGK\Helper;
 */
class Activator
{
    private static $sm_dyn_sources;
    private static $sm_dyn_class;

    /**
     * register class source 
     * @param string $interface 
     * @return true|void 
     */
    private static function __GetClassSrc(string $interface){
        if (is_null(self::$sm_dyn_sources)){
            self::$sm_dyn_sources = [];
        }
        if (isset(self::$sm_dyn_sources[$interface])){
            return true;
        }
        $nuclass = basename(igk_dir($interface)); 
        $p = strtolower('___igk_dynamic_class_'.$nuclass);
        if(is_null(self::$sm_dyn_class)){
            self::$sm_dyn_class = [];
        }
        if (isset(self::$sm_dyn_class[$p])){
            $p .= '_'.(self::$sm_dyn_class[$p]++);
        }else{
            self::$sm_dyn_class[$p] = 1;  
        }

        $dyn_trait = DynamicActivableTrait::class;
        $ref = [];
        $ref[] = JsonSerializable::class;
        $ref[] = IToArray::class;
        $ref[] = IToJSon::class;
        $ref[] = $interface;
      $ref = implode(", ", $ref );
        $src = <<<EF
?><?php
final class {$p} implements {$ref}{
    use {$dyn_trait};
    public function __construct(& \$d){
        \$this->data = \$d;
    } 
} 
EF;
        self::$sm_dyn_sources[$interface] = [$src, $p];
        eval($src); 
    }
    /**
     * 
     * @param string $interface 
     * @param mixed $resolver 
     * @return object 
     * @throws Exception 
     * @throws IGKException 
     */
    public static function CreateFromInterface(string $interface, $resolver=null){
        $root = $g = igk_sys_reflect_class($interface);
        $properties = [];
        // create a container that will handle component 
        $container = new RegexMatcherContainer;
        $patterns = [
            ["match"=>"(?i)\\$[a-z_][a-z0-9_]*\b", "tokenID"=>"name"],
            ["match"=>"\b\w+(\s*\|\s*\w+)*\b", "tokenID"=>"type"],
        ];
        $container->begin('@property\\b', '$', 'prop-detect', null, $patterns);
        $resolver = $resolver ?? function(){return null;};
        
        $v_handler =  function($comment) use($container, & $properties, $resolver){
            $offset = 0;
             /**
             * @var ?string
             */
            $type = null;
            /**
             * @var ?string
             */
            $name = null;
            while($g = $container->detect($comment, $offset)){ 
               if( $e = $container->end($g, $comment, $offset)){
                    switch($e->tokenID){
                        case 'type':                   
                            if (!$type){
                                $type = $e->value;
                            }
                        break;
                        case 'name':
                            $name = $e->value;
                            break;
                        default:
                        $properties[substr($name, 1)] = $resolver($type);
                        $name = $type = null;
                        break;
                    }
                    // Logger::print("sample : ".$e->tokenID . " value=[".$e->value.']');
               }
            }
        };
        $v_load = [];
        $tq = [$g];
        while(count($tq)>0){
            $g = array_shift($tq);
            if ($comment = $g->getDocComment()){ 
                $v_handler($comment);
            }
            if ($g->isInterface()){
                // 
                foreach($g->getInterfaceNames() as $r){
                    if (!isset($v_load[$r])){
                        array_unshift($tq, igk_sys_reflect_class($r));
                    }
                }
            } else {
                $cv = get_class_vars($g->getName());
                foreach($cv as $k=>$value){
                    $properties[$k] = $value;
                }
                foreach($g->getInterfaceNames() as $r){
                    if (!isset($v_load[$r])){
                        array_unshift($tq, igk_sys_reflect_class($r));
                    }
                }
                if($c = $g->getParentClass()){
                    if (!isset($v_load[$c])){
                        array_unshift($tq, igk_sys_reflect_class($c));
                    } 
                }
            }
        } 
        ksort($properties);
        if ($root->isInterface()){
            $cl = $root->getName();
            self::__GetClassSrc($cl);
            if ($_dyn_cl =  igk_getv(self::$sm_dyn_sources[$cl], 1)){
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
     * @param string|callable|array $classame 
     * @param mixed $data . numeric association key will be used as contructor argument
     * @param bool $fullfill fullfield with data 
     * @return object|mixed association data
     * @throws IGKException 
     * @throws Exception class not found
     */
    public static function CreateNewInstance($classame, $data = null, bool $fullfill = false)
    {
        if ($data instanceof $classame) {
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

        if (is_callable($classame)) {
            $g = $classame(...$args);
        } else {
            $g = new $classame(...$args);
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
            } else {
                foreach (get_class_vars(get_class($g)) as $k => $v) {
                    $v = igk_getv($data, $k, $g->$k) ?? $v;
                    if (method_exists($g, $fc = 'set' . ucfirst($k))) {
                        $g->$fc($v);
                        continue;
                    }
                    $g->{$k} = $v;
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
 

        return $g;
    }
    /**
     * 
     * @param callable $callable 
     * @param mixed $inf instance
     * @param mixed $def definition
     * @return void 
     */
    public static function InitPrivatePropety(callable $callable, $inf, $def){
        if ($fc = $callable->bindTo($inf)){
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
}
