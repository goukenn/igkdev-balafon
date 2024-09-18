<?php

namespace IGK\System\Http;
// @author: C.A.D. BONDJE DOUE
// @filename: StatusCode.php
// @date: 20220311 08:32:47
// @desc: 

class StatusCode
{

    public static function GetStatus(int $code)
    {
        static $t = null;
        if ($t === null) {
            $protocol = igk_server()->SERVER_PROTOCOL ?? "HTTP/1.0";
            $t = array(
                400 => "{$protocol} 400 Bad request.",
                401 => "{$protocol} 401 unauthorized",
                402 => "{$protocol} 402 payement required",
                403 => "{$protocol} 403 forbidden",
                404 => "{$protocol} 404 not found",
                405 => "{$protocol} 405 method not allowed",
                406 => "{$protocol} 406 not acceptable",
                407 => "{$protocol} 407 Proxy Authentication Required",
                408 => "{$protocol} 408 request time out",
                409 => "{$protocol} 409 Confict",
                410 => "{$protocol} 410 Gone",
                411 => "{$protocol} 411 Length required",
                500 => "{$protocol} 500 Internal server error",
                501 => "{$protocol} 501 Internal server error",
                502 => "{$protocol} 502 Internal server error",
                503 => "{$protocol} 503 Service not available",
                504 => "{$protocol} 504 Service not available",
                505 => "{$protocol} 505 Version not supported"
            );
        }
        $defCodeMSG = igk_server()->SERVER_PROTOCOL ." 200 Ok";
       // if(in_array("h2", explode(',', igk_server()->Upgrade ?? ""))){
             $defCodeMSG = "HTTP/2.0 200 OK - ".igk_server()->SERVER_PROTOCOL ;
        // }  
        // igk_trace();
        // igk_exit();
        return igk_getv($t, $code, $defCodeMSG); 
    }
}
