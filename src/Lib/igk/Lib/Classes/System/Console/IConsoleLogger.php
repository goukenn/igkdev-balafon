<?php

namespace IGK\System\Console;


/**
 * protocol used to inject a logger
 * @package IConsoleLogger
 */
interface IConsoleLogger{
    function log($msg); 
    function info($msg);
    function warn($msg);
    function success($msg);
}