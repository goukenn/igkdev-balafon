<?php

namespace IGK\Helper;

use IGK\System\Configuration\Controllers\ConfigControllerBase;
use IGK\System\Configuration\Controllers\ConfigControllerRegistry;
use IGKApp;
use IGKEvents;
use IGKException;
use ReflectionMethod;

class SysUtils{
    /**
     * get application module from entry file
     * @param mixed $file 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetApplicationModule($file){
        return igk_get_module(igk_get_module_name(dirname($file)));
    }
    /**
     * @return array list of controller installed in project folder
     */
    public static function GetProjectControllers(callable $filter=null){
        if (!IGKApp::IsInit()) {
            return null;
        }
        $c = igk_app()->getControllerManager()->getControllers();
        $dir = igk_io_collapse_path(igk_io_projectdir());
        $projects_ctrl = [];
        foreach ($c as $k){
            $ccpath = igk_io_collapse_path($k->getDeclaredDir());;
            
            if (strstr($ccpath, $dir)) {
                if (!$filter || $filter($k))
                    $projects_ctrl[] = $k;
            }
        }
        return $projects_ctrl;
    }
    public static function GetDeclaredMethods($class){
        $ref = igk_sys_reflect_class($class);
        return  array_filter(array_map(function($m) use ($class){
            $n = $m->getName();
            if (strpos($n , "__")===0){
                return null;
            }
            if ($m->getDeclaringClass()->getName() == $class)
                return $n;
            return null;
        },$ref->getMethods( ReflectionMethod::IS_PUBLIC)));


    }
     /**
     * 
     * @param array|\IIGKArrayObject $n  item to convert
     * @return array 
     */
    public static function ToArray($n){
        if (!$n){
            return null;
        }
        if (is_array($n))
            return $n;
        return $n->to_array();
    }

    /**
     * get configuration controllers
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetConfigurationControllers(){
        return ConfigControllerRegistry::GetConfigurationControllers();        
    }

     ///<summary>Notifify message</summary>
     public static function Notify($message, $type="default"){
        if (igk_is_ajx_demand()){
            igk_ajx_toast($message, $type);
        }else {
            igk_notifyctrl()->bind($message, $type);
        }
    }
    ///<summary>exist on ajx deman</summary>
    /**
     * exit on ajx demand
     * @return void 
     * @throws IGKException 
     */
    public static function exitOnAJX(){
        if (igk_is_ajx_demand()){
            igk_hook(IGKEvents::HOOK_AJX_END_RESPONSE, []);
            igk_environment()->isAJXDemand = null;
        } 
        igk_exit();
    }

    public static function InitClassFields($c, $object){
        $properties = igk_relection_get_properties_keys(get_class($c)); 
        foreach($object as $k=>$v){
            if (key_exists($k = strtolower($k), $properties)){
                $m = $properties[$k]->getName();
                $c->$m = $v;
            }
        }
    }
    /***
     * init class variable
     */
    public static function InitClassVars($n, $tag){ 
        foreach(get_class_vars(get_class($n)) as $k=>$c){ 
            $n->$k = igk_getv($tag, $k, $c);
        } 

    }
    public static function assert_notify($condition, $successmsg, $errormessage, $name=null){
        $check = igk_check($condition);
        $notify = igk_notifyctrl($name);
        if ($check){
            $notify->success($successmsg);
        } else {
            $notify->error($errormessage);
        }
    }
    ///<summary>assert toation on ajx demand condition</summary>
    /**
     * assert toation on ajx demand condition
     * @param mixed $condition 
     * @param mixed $successmsg 
     * @param mixed $errormessage 
     * @return void 
     * @throws Exception 
     * @throws ReflectionException 
     */
    public static function assert_toast($condition, $successmsg, $errormessage){
        if (!igk_is_ajx_demand())
            return;
        $check = igk_check($condition);
        $d = ["msg"=>$successmsg, "type"=>"igk-success"];
        if (!$check){
            $d["msg"] =$errormessage;
            $d["type"]="igk-danger";
        }
        igk_ajx_toast($d["msg"], $d["type"]);
    }

    /**
     * get subdomain controller 
     * @return null|BaseController subdomain controller
     */
    public static function GetSubDomainCtrl(){
        $v_c = igk_app()->getApplication();
        if ($v_c->lib("subdomain")){
            return $v_c->getLibrary()->subdomain->subdomain; 
        }
        return null;
    }
    /**
      * @return null|BaseController subdomain controller
     */
    public static function CurrentBaseController(){
        // $a = igk_app();

        return igk_environment()->subdomainctrl ??
            igk_app()->getBaseCurrentCtrl() ?? igk_get_defaultwebpagectrl();
        // if ($g !== null) {
        //     return $g;
        // }
        // return null;
    }

    public static function GetApplicationLibrary(string $name){
        return igk_app()->getApplication()->getLibrary()->$name;
    }
}