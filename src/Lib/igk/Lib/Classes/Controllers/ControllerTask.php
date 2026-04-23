<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerTask.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;
use Exception;
use IGK\System\Http\Route;
use IGK\System\Http\RouteActionHandler;

/**
 * controller task : routable controller action
 * note: a Balafon Page is a ControllerTask
 * @package IGK\Controllers
 */
abstract class ControllerTask{
    /**
    * Property: controller.
    * @var mixed
    */
    protected $controller;
    /**
    * auto generate doc.
    * @var RouteActionHandler
    */
    protected $route;
    /**
    * .ctr
    * @param mixed $controller
    * @param null|RouteActionHandler $route
    */
    public function __construct($controller, ?RouteActionHandler $route=null)
    {
        $this->controller = $controller;
        $this->route = $route;  
        $this->init(); 
    }
    /**
    * Initializes.
    */
    protected function init(){
        if (!$this->route){
            Route::LoadConfig($this->controller);
        }
    }
    /**
     * index start entry task
     * @return mixed 
     */
    abstract function index();
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $args
    */
    public function __call($name, $args)
    {
        $n = $name."_".igk_server()->REQUEST_METHOD;
        if (method_exists($this, $n)){
            return $this->$n(...$args);
        }
        array_unshift($args, $name);
        return $this->index(...$args);
    }
}