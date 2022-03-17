<?php
namespace IGK\System\Http;
// @author: C.A.D. BONDJE DOUE
// @filename: StatusCode.php
// @date: 20220311 08:32:47
// @desc: 

class StatusCode {

    public static function GetStatus(int $code){
        static $t = null;
    if ($t === null) {
        $t = array(
            400 => "HTTP/1.0 400 Bad request condition",
            401 => "HTTP/1.0 401 unauthorized",
            402 => "HTTP/1.0 402 payement required",
            403 => "HTTP/1.0 403 forbidden",
            404 => "HTTP/1.0 404 not found",
            405 => "HTTP/1.0 405 method not allowed",
            406 => "HTTP/1.0 406 not acceptable",
            407 => "HTTP/1.0 407 Proxy Authentication Required",
            408 => "HTTP/1.0 408 request time out",
            409 => "HTTP/1.0 409 Confict",
            410 => "HTTP/1.0 410 Gone",
            411 => "HTTP/1.0 411 Length required",
            500 => "HTTP/1.0 500 Internal server error",
            503 => "HTTP/1.0 503 Service not available",
            505 => "HTTP/1.0 505 Version not supported"
        );
    }
    return igk_getv($t, $code, "HTTP/1.0 200 Ok");
    }
}
