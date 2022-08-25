<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKRoutes.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\System\Http\Request;

class IGKRoutes
{
    const REG_KEY = "sys://reg/systemuri";
    static $request_entry;
    public static function Register($u, $callback, $prehandle = 1)
    {
        // TODO : remove register
        if (is_null(self::$request_entry)){
            $request_entry = Request::getInstance()->requestEntry();
        }
        igk_environment()->setArray(self::REG_KEY, $u, $callback);
        if ($prehandle && $request_entry){
            self::Invoke($request_entry, $u, $callback);
        }
    }
  /**
     * invoke registrated uri
     * @param mixed $uri 
     * @param mixed $u 
     * @param mixed $callback 
     * @return void 
     */
    public static function Invoke($uri, $u, $callback)
    {
        $uri_key = self::REG_KEY;
        if ($uri === $u) {
            $callback();
        } else {
            $sk = $u;
            if (preg_match("/\[%q%]/i", $u)) {
                $sk = str_replace("[%q%]", "(\?(:query))?", $u);
            }
            $p = igk_sys_ac_create_pattern(null, null, $sk);
            if ($p->matche($uri)) {
                $request = explode("?", $u)[0];
                if (!isset($b[$request])) {
                    $b[$request] = $u;
                    igk_set_env($uri_key, $b);
                }
                $args = $p->getQueryParams();
                if (PHP_VERSION > "8.0.0"){
                    $args = array_values($args);
                } 
                call_user_func_array($callback, $args);
            }
        }
    }
}
