<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Utils.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests;

use IGK\Controllers\BaseController;

class Utils{
    public static function CreateController($classname){
    
        if (class_exists($classname) && is_subclass_of($classname , BaseController::class) && !igk_reflection_class_isabstract($classname)){
        
            $o = new $classname();
            if (!isset(igk_environment()->AutoLoad[$classname])){ 
                $o::register_autoload();
                igk_environment()->setArray("AutoLoad", $classname, 1); 
            }            
            return $o;
        } 
        throw new \Exception("Class [ {$classname} ] not found");
    }
    public static function GetDefaultController($classname){
        $controller = $app = igk_get_defaultwebpagectrl();
        if (!$app || (get_class($app)!== $classname))
            $controller = Utils::CreateController($classname);
        return $controller;
    }

    public static function CheckControllerDataBase($test, $controllerClass, $model=null){        
        $controller = $controllerClass;
        if (is_string($controllerClass) && !($controller = self::CreateController($controllerClass))){
            $test->fail("controller not created");
            return false;
        }
        $v_definition = $controller->getDataTableDefinition();
        $v_tb = $v_definition->tables;
        // get model or db utility 
        $model = $model ?? $controller->getDb(); 
        if ($v_tb){
            foreach(array_keys($v_tb) as $table){                
                $test->assertTrue(
                    $model->select_count(null,$table) !== -1                        
                    , "Table $table not present");
            } 
        }else {
            $test->fail("no tables : ".get_class($controller));
        }

    }
    public static function PostView(BaseController $controller, $view="default", $params=null){
        self::SendView($controller, $view, $params, "POST");
    }
    public static function GetView(BaseController $controller, $view="default", $params=null){
        self::SendView($controller, $view, $params, "GET");
    }
    public static function SendView(BaseController $controller, $view="default", $params=null, $method="GET"){
        igk_server()->REQUEST_METHOD = $method;
        $controller->loader->View($view, ["params"=>self::GetParams($params)]);
    }
    private static function GetParams(...$params){
        if (is_string($params)){
            return explode("/", $params);
        }
        if (is_array($params)){
            return array_values($params);
        }
        return array();
    }
    
}