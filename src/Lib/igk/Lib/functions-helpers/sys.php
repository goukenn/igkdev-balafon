<?php


function igk_sys_request_time(){
    $time = $_SERVER["REQUEST_TIME_FLOAT"];
    return (microtime(true) - $time);
}