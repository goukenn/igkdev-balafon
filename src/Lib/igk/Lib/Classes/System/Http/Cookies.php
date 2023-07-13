<?php
// @author: C.A.D. BONDJE DOUE
// @file: Cookies.php
// @date: 20230705 11:57:31
namespace IGK\System\Http;

// + | --------------------------------------------------------------------
// + | cookie manipulation
// + | Apache: configuration for php < 7.3
// + |  Header always edit Set-Cookie (.*) "$1; SameSite=Lax"
// + |  https://stackoverflow.com/questions/39750906/php-setcookie-samesite-strict

///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
class Cookies{
    const USER_ID = 'uid';

    /**
     * store cookies
     * @param string $name 
     * @param null|string $value 
     * @param mixed $options 
     * @return void 
     */
    public static function StoreCookie(string $name, ?string $value=null, $options=null){
        if (!is_null($value)){
            if (is_null($options)){
                $options = [
                    'expires'=>time()+86400,
                    'path'=>'/',
                    'domain'=>igk_sys_domain_name(),
                    'httponly'=>'true',
                    'samesite'=>'None',
                    'secure'=>igk_server()->is_secure()
                ];
            }
        }
        if (version_compare(PHP_VERSION, "7.3", ">=")){
            setcookie($name, $value, $options);
            return;
        }        
        setcookie($name, $value, time()+86400, "/", igk_sys_domain_name(), igk_server()->is_secure(), true );
    }
}