<?php
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