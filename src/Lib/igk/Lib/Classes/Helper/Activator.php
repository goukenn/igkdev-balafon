<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Activator.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\Helper;
/**
 * 
 * @package IGK\Helper;
 */
class Activator{
    /**
     * create class instance. \
     *      class must context a public constructor \
     *      data pass to it will be used to initialize public properties
     * 
     * @param string|callable|array $classame 
     * @param mixed $data 
     * @param bool $fullfill fullfield with data 
     * @return object 
     * @throws IGKException 
     * @throws Exception class not found
     */
    public static function CreateNewInstance($classame, $data = null,bool $fullfill=false){
        if ($data instanceof $classame){
            return $data;
        }
        if (is_callable($classame)){
            $g = $classame();
        }else{
            $g = new $classame();
        }
        if ($data){
            
            if ($fullfill){
                foreach ($data as $k => $value){
                    if (property_exists($g, $k)){
                        $g->{$k} = $value;
                    }
                }
            }else{
                foreach(get_class_vars(get_class($g)) as $k=>$v){                 
                    $g->{$k} = igk_getv($data, $k, $g->$k) ?? $v;
                }
            }
        }
        if ($g instanceof IActivatorMandatory){
            foreach($g->getMandatory() as $k){
                if (!isset($g->{$k})){
                    return null;
                }
            }
        }
        return $g;
    }
}