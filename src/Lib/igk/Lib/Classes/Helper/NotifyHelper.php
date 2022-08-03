<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NotifyHelper.php
// @date: 20220803 13:48:58
// @desc: 


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