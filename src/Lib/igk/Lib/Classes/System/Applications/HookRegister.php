<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookRegister.php
// @date: 20221116 00:53:02
namespace IGK\System\Applications;

use IGKEvents;

///<summary></summary>
/**
* 
* @package IGK\System\Applications
*/
class HookRegister{
    public static function Init(){
        igk_reg_hook(IGKEvents::HOOK_USER_LOGIN, function($e){
            extract($e->args);
            \IGK\Models\LoginLogs::Add(
                $user->clGuid, $agent, 
                $ip, $geox, $geoy,
                $region, $country_code, $country_name, $city, $status,
                $description);
        });


        igk_reg_hook(IGKEvents::HOOK_USER_LOGOUT, function($e){
            extract($e->args);
            \IGK\Models\LoginLogs::Add(
                $user->clGuid, $agent, 
                $ip, $geox, $geoy,
                $region, $country_code, $country_name, $city, $status,
                $description);
        });
    }
}