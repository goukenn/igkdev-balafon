<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IMiddleWareAction.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Installers;

interface IMiddleWareAction{
    /**
     * process the action. call next to continue. if next throw and exception or return false.
     * abort the action
     * @return mixed 
     */
    function invoke();
    /**
     * move to next action and update
     * @return mixed 
     */
    function next();
    /**
     * call it to abort the action
     * @return mixed 
     */
    function abort();
}