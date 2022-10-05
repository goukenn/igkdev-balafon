<?php
// @author: C.A.D. BONDJE DOUE
// @file: IControllerManagerObject.php
// @date: 20220909 11:14:19
namespace IGK\Controllers;


///<summary></summary>
/**
* 
* @package IGK\Controllers
*/
interface IControllerManagerObject{
    function getController($name, bool $throwException = true): ?BaseController;
    function register(BaseController $controller); 
    function getDefaultController(): ?BaseController;
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
     * @return array 
     */
    function getUserControllers():array;

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
}