<?php
// @author: C.A.D. BONDJE DOUE
// @file: IActionDispatcher.php
// @date: 20230520 19:56:07
namespace IGK\Actions;


///<summary></summary>
/**
* 
* @package IGK\Actions
*/
interface IActionDispatcher{
    function setBaseActionName(string $actionName);
    function getBaseActionName():string;
    function invoke(string $action, ...$args);
}