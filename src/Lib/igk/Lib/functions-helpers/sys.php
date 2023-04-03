<?php
// @author: C.A.D. BONDJE DOUE
// @filename: sys.php
// @date: 20230323 12:53:54
// @desc: system helper function 

if (!function_exists('igk_sys_request_time')){
    function igk_sys_request_time(){
        $time = $_SERVER["REQUEST_TIME_FLOAT"];
        return (microtime(true) - $time);
    }
}