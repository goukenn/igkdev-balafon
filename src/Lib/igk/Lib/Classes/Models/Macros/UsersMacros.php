<?php


namespace IGK\Models\Macros;

use IGK\Models\Users;
use IGK\System\Number;

class UsersMacros {
    public static function register(Users $model, $o){
        if (empty($pwd = igk_getv($o, "clPwd"))){
            $pwd = sha1( IGK_PWD_PREFIX. date("Ymd").microtime(true));
            igk_setv($o, "clPwd", $pwd);
        }
        return $model::create($o);
    }
}