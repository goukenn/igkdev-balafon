<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DispatcherService.php
// @date: 20230706 10:43:00
// @desc: dispatcher service 

namespace IGK\Actions;
use IGK\Controllers\BaseController;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IInjectable;
use ReflectionException;

abstract class DispatcherService{
    static $sm_services = [];

    /**
     * 
     * @param BaseController $ctrl 
     * @param mixed $rtype mixed type
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function CreateOrGetServiceInstance(BaseController $ctrl, $rtype, string $typecheck = IInjectable::class){
        $arguments = null;
        $m = null;
        if (is_array($rtype)){
            $nkey = igk_getv(array_keys($rtype), 0);
            $m = (array)$rtype[$nkey];
            $arguments = igk_getv($m, 'arguments') ?? [];            
            $rtype = $nkey;
        }
        if (is_string($rtype)){ 
            is_subclass_of($rtype, $typecheck) || igk_die('misconfiguration target type not injectable');
        }
        $p =  DispatcherService::GetServiceInstance($ctrl, $rtype, $arguments);
        if ($m && $p){
            unset($m['arguments']);
            foreach ($m as $key => $value) {
                if (method_exists($p, $fc = 'set'.ucfirst($key))){
                    $p->$fc($value);
                } else if (property_exists($p, $key)){
                    $p->$key = $value;
                }
            }
        }

        return $p;
    }
    public static function  GetServiceInstance(BaseController $ctrl, string $class_name, ...$args){
        $key= $ctrl->name(igk_uri('/services/'.$class_name));
        if (!isset(self::$sm_services[$key])){
            self::$sm_services[$key] = new $class_name(...$args);
        }
        $m = igk_getv(self::$sm_services, $key);
        return $m;
    }
}