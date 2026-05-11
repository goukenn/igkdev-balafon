<?php
// @author: C.A.D. BONDJE DOUE
// @file: IControllerManagerObject.php
// @date: 20220909 11:14:19
namespace IGK\Controllers;

/**
* auto generate doc.
* @package IGK\Controllers
*/
interface IControllerManagerObject{
    /**
    * Returns Controller.
    * @param mixed $name
    * @param bool $throwException
    * @return ?BaseController
    */
    function getController($name, bool $throwException = true): ?BaseController;
    /**
    * Registers.
    * @param BaseController $controller
    * @return ?BaseController
    */
    function register(BaseController $controller); 
    function getDefaultController(): ?BaseController;
    /**
    * Sets Default Controller.
    * @param null|BaseController $controller
    * @return ?array
    */
    function setDefaultController(?BaseController $controller);
    function invokeUri(?string $uri=null, bool $render=false);
    function getControllerRef(): ?array;
    /**
     * array of all controllers
     * @return array 
     */
    function getControllers():array;
    /**
    * list of project controller
    * @param mixed $filter
    * @return array
    */
    function getUserControllers($filter = null):array;
    /**
     * get registrated named controller
     * @param string $name 
     * @return null|BaseController 
     */
    function getRegistratedNamedController(string $name): ?BaseController;
    /**
     * register named controller
     * @param string $name 
     * @param BaseController $controller 
     * @return mixed 
     */
    function registerNamedController(string $name, BaseController $controller);
    /**
     * invoke pattern
     * @param mixed $pattern 
     * @return mixed 
     */
    function InvokePattern($pattern);
}