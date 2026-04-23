<?php
// @author: C.A.D. BONDJE DOUE
// @file: IActionDispatcher.php
// @date: 20230520 19:56:07
namespace IGK\Actions;

/**
* auto generate doc.
* @package IGK\Actions
*/
interface IActionDispatcher{
    /**
    * Sets Base Action Name.
    * @param string $actionName
    * @return string
    */
    function setBaseActionName(string $actionName);
    function getBaseActionName():string;
    /**
    * Invoke.
    * @param string $action
    * @param mixed ...$args
    */
    function invoke(string $action, ...$args);
}