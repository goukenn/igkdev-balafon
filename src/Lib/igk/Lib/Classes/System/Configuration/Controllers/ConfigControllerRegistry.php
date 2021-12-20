<?php

namespace IGK\System\Configuration\Controllers;

use IGKControllerManagerObject;
use IGKEvents;

class ConfigControllerRegistry{
    const LOADED_CONFIG_CTRL = "config_controllers";
    /**
     * register configuration class
     * @param string $class 
     * @return bool 
     * @throws EnvironmentArrayException 
     */
    public static function Register(string $class, $name=null){
    
        if (is_subclass_of($class, ConfigControllerBase::class)){
            $key = $name ? $name : $class; 
            igk_environment()->setArray(self::LOADED_CONFIG_CTRL, $key, $class);
            return true;
        }
        return false;
    }
    public static function GetResolvController(){
        // merge controller view configuration controllers. 
        $resolv_ctrl = IGKControllerManagerObject::GetResolvController();
        if ($jump = igk_environment()->get(self::LOADED_CONFIG_CTRL)){ 
            $resolv_ctrl = array_merge($resolv_ctrl, $jump);// array_combine(array_keys($jump), array_values($jump)));
        } 
        return $resolv_ctrl;
    }
    public static function GetConfigurationControllers(){
        $v_load_controller = igk_app()->getControllerManager()->getControllerRef();
        $resolv_ctrl = self::GetResolvController();
        foreach($resolv_ctrl as $k=>$v){
            if (!isset($v_load_controller[$v])){
                $ctrl= igk_getctrl($k, false) ?? igk_init_ctrl($v);
                $v_load_controller[get_class($ctrl)] = $ctrl;
            }
        } 

        igk_hook(IGKEvents::HOOK_CONFIG_CTRL, [
            "loaded"=> & $v_load_controller
        ]);
        $v_load_controller = array_unique(array_values($v_load_controller));
        return $v_load_controller;
    }
}