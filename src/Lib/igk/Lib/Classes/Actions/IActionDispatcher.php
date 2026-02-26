<?php
// @author: C.A.D. BONDJE DOUE
// @file: IActionDispatcher.php
// @date: 20230520 19:56:07
namespace IGK\Actions;
/**
* 
* @package IGK\Actions
*/
interface IActionDispatcher{

    /**
    * auto generate doc.
    * @param string $actionName
    * @return string
    */
    function setBaseActionName(string $actionName);
    function getBaseActionName():string;

    /**
    * auto generate doc.
    * @param string $action
    * @param mixed ...$args
    */
    function invoke(string $action, ...$args);
}