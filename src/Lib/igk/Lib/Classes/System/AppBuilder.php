<?php

// @author: C.A.D. BONDJE DOUE
// @filename: AppBuilder.php
// @date: 20220704 12:38:36
// @desc: application builder that implement extensible macros

namespace IGK\System;

use Closure;
use IGK\System\Traits\MacrosConstant;
use IGK\System\Traits\MacrosTrait;
use IGKServices;

use function igk_resources_gets as __;

///<summary>help to build and register application definition entry</summary>
/**
 * help to build and register application definition entry
 */
class AppBuilder extends MacrosConstant{
    use MacrosTrait;

    /**
     * 
     * @param string $name 
     * @return mixed 
     */
    public function getService(string $name){
        return igk_app()->getService($name);
    }
    /**
     * register services
     */
    public function registerService(string $name, string $instance_class){
        return IGKServices::Register($name, $instance_class);
    }
    static function _InvokeMacros($macros, $name, $arguments ){ 
        $key = static::class .self::StaticSeparator. $name;
        $instance = igk_getv($arguments, 0); 
        if ($fc = igk_getv($macros, $key)){
            // static closure
            // array_unshift($arguments, $instance); 
            return $fc(...$arguments);
        }
        $key = static::class .self::ClosureSeparator. $name;
        if ($fc = igk_getv($macros, $key)){
            // instance closure          
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc);
            }
            $fc = $fc->bindTo($instance);
            return $fc(...$arguments);
        }

        if ($fc = igk_getv($macros, $name)) {
            if (is_callable($fc)) {
                $fc = Closure::fromCallable($fc);
            }
            array_unshift($arguments, $instance);
            return $fc(...$arguments);
        }
        igk_dev_wln_e($name, array_keys($macros));
        igk_die(sprintf(__("extension or macro [%s] not found"), $name));
    }
}
