<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Utils.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests;

use IGK\Controllers\BaseController;

/**
 * utility test class 
 * @package IGK\Tests
 */
class Utils{
    /**
     * create a controller
     * @param string $classname 
     * @return object 
     * @throws mixed 
     */
    public static function CreateController(string $classname): ?BaseController{
    
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
    /**
     * get default controller 
     * @param mixed $classname 
     * @return object|null 
     */
    public static function GetDefaultController(string $classname): ?BaseController{
        $controller = $app = igk_get_defaultwebpagectrl();
        if (!$app || (get_class($app)!== $classname))
            $controller = Utils::CreateController($classname);
        return $controller;
    }

    /**
     * 
     * @param mixed $test 
     * @param mixed $controllerClass 
     * @param mixed $model 
     * @return bool|void
     */
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
                    , "Table '$table' not present. please initialaze controller db");
            } 
        }else {
            $test->fail("no tables: ".get_class($controller));
        }

    }

    /**
    * Post view.
    * @param BaseController $controller
    * @param mixed $view
    * @param null|mixed $params
    */
    public static function PostView(BaseController $controller, $view="default", $params=null){
        self::SendView($controller, $view, $params, "POST");
    }

    /**
    * Returns View.
    * @param BaseController $controller
    * @param mixed $view
    * @param null|mixed $params
    */
    public static function GetView(BaseController $controller, $view="default", $params=null){
        self::SendView($controller, $view, $params, "GET");
    }

    /**
    * Sends View.
    * @param BaseController $controller
    * @param mixed $view
    * @param null|mixed $params
    * @param mixed $method
    */
    public static function SendView(BaseController $controller, $view="default", $params=null, $method="GET"){
        igk_server()->REQUEST_METHOD = $method;
        $controller->loader->View($view, ["params"=>self::_GetParams($params)]);
    }
    /**
     * 
     * @param mixed ...$params 
     * @return string[]|mixed[]|array 
     */
    private static function _GetParams(...$params){
        if (is_string($params)){
            return explode("/", $params);
        }
        if (is_array($params)){
            return array_values($params);
        }
        return array();
    }
    
}