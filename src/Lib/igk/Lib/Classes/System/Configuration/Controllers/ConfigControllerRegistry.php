<?php

namespace IGK\System\Configuration\Controllers;
 
use IGKControllerManagerObject;
use IGKEvents;
use IGK\Controllers\IRegisterOnInitController;
use IGK\System\Diagnostics\Benchmark;

class ConfigControllerRegistry{
    const LOADED_CONFIG_CTRL = "config_controllers";
    private static $sm_regComplete;

 

    ///<summary>RegisterInitComplete . if Ctrl is not null add it to base controller list</summary>
    ///<param name="ctrl">if null return the count number of the registrated controller. else register the controller to iniList</param>
    /**
    * RegisterInitComplete . if Ctrl is not null add it to base controller list
    * @param mixed $ctrl if null return the count number of the registrated controller. else register the controller to iniList
    */
    public static function RegisterInitComplete($ctrl=null){
        if(self::$sm_regComplete === null)
            self::$sm_regComplete=array();
        $register = $ctrl && ($ctrl instanceof IRegisterOnInitController); // in_array(IRegisterOnInitController::class,  class_implements($ctrl, false));
     
        if(($ctrl !== null) && (!$register || $ctrl->getCanRegisterOnInit())){
            self::$sm_regComplete[]=$ctrl;
        }
        return igk_count(self::$sm_regComplete);
    }

     ///<summary></summary>
    /**
    * 
    */
    public static function InvokeRegisterComplete(){
        if(self::$sm_regComplete){
            // $ctime = igk_sys_request_time();
            // $ttime = 0;
            foreach(self::$sm_regComplete as  $v){ 
                Benchmark::mark(get_class()."::initComplete");
                $v->initComplete();
                Benchmark::expect(get_class()."::initComplete", 0.01);
                // $time = igk_sys_request_time();
                // $duration = ($time-$ctime);
                // $ttime += $duration;
                // igk_wln("build : ".get_class($v). " : ". $time ." : ", $duration > 0.0005 ? "<div style='color:red' >mark : {$duration} </div>": $duration);
                // $ctime = $time;
            }
            //igk_wln("duration:".$ttime," Count ".count(self::$sm_regComplete));
        }
        self::$sm_regComplete=null;
    }
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