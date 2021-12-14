<?php

namespace IGK\System;

use ReflectionClass;
use ReflectionMethod;

class ExtensionUtils{

    public static function LoadMethods(& $array, $class, $topClass){
        $ref = igk_sys_reflect_class($class);   
        foreach($ref->GetMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC) as $k){
            $params = $k->getParameters();  
            if((count($params)==0) || !($params[0]->hasType() && is_a($params[0]->getType()->getName() , $topClass, true))){
                continue;
            }
            $array[$k->getName()] = [$class, $k->getName()]; 
        }
    }
}