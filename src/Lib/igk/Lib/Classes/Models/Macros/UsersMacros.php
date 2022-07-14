<?php


namespace IGK\Models\Macros;

use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Number;
use IGKException;

class UsersMacros {
    /**
     * register user helpers
     * @param Users $model 
     * @param object|array|IUserRegisterInfo $o 
     * @return ModelBase 
     * @throws IGKException 
     */
    public static function register(Users $model, $o){
        if (!is_array($o) && !is_object($o)){
            igk_die(__METHOD__." object not valid");
        }
        if (empty($guid = igk_getv($o, "clGuid"))){
            $guid = igk_create_guid();
            igk_setv($o, "clGuid", $guid);
        }
        if (empty($pwd = igk_getv($o, "clPwd"))){
            $pwd = sha1( IGK_PWD_PREFIX. date("Ymd").microtime(true));
            igk_setv($o, "clPwd", $pwd);
        }
        if ($login = igk_getv($o, "clLogin")){
            if ($model::select_row(["clLogin"=>$login])){
                return false;
            }
        }
        return $model::create($o);
    }
}