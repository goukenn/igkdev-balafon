<?php

namespace IGK\Helper;


class NotifyHelper{

    public static function Notify(string $name, $condition, $success, $error){
        if ($condition){
            if (igk_is_ajx_demand()){
                igk_ajx_toast($success);
            }else {
                igk_notifyctrl($name)->addSuccess($success);
            }
        } else {
            if (igk_is_ajx_demand()){
                igk_ajx_toast($error, "danger");
            }
            else 
                igk_notifyctrl($name)->addDanger($error);
        }
    }
}