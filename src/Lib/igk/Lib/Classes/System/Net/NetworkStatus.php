<?php
// @author: C.A.D. BONDJE DOUE
// @file: NetworkStatus.php
// @date: 20230322 23:50:21
namespace IGK\System\Net;


///<summary></summary>
/**
* 
* @package IGK\System\Net
*/
class NetworkStatus{
    private static $sm_is_alive;

    public static function IsConnectionAlive(){
        if (!is_null(self::$sm_is_alive)){
            return self::$sm_is_alive;
        }
        $uri = $default = 'igkdev.com';
        if (defined("IGK_CHECK_NETWORK_DOMAIN")){
            $uri = constant('IGK_CHECK_NETWORK_DOMAIN') ?? $default;
        }
        self::$sm_is_alive = false;
        if (function_exists('fsockopen')){
            if ($h = @fsockopen($uri,80, $error, $msg)){
                fclose($h);
                self::$sm_is_alive = true;
            } 
        }
        return self::$sm_is_alive;
    }
}