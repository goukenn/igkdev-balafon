<?php
namespace IGK\System\Console;
/**
 * protocol used to inject a logger
 * @package IConsoleLogger
 */
interface IConsoleLogger{

    /**
    * auto generate doc.
    * @param mixed $msg
    */
    function log($msg); 
    function info($msg);
    function warn($msg);
    function success($msg);
    function danger($msg);
    /**
     * represent offscreen 
     * @return mixed 
     */
    function offscreen();
}