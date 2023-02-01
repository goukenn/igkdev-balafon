<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ExtensionUtils.php
// @date: 20220803 13:48:54
// @desc: 


namespace IGK\System;

use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;
use ReflectionMethod;

class ExtensionUtils{

    /**
     * retrieve extension method for 
     * @param mixed $array 
     * @param mixed $class 
     * @param mixed $topClass 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function LoadMethods(& $array, $class, $topClass){
        $ref = igk_sys_reflect_class($class);   
        foreach($ref->GetMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC) as $k){
            $params = $k->getParameters();  
            if((count($params)==0) || !($params[0]->hasType() && is_a($params[0]->getType()->getName() , $topClass, true))){
                continue;
            }
            $array[$k->getName()] = [$class, $k->getName(), $params]; 
        }
    }
}