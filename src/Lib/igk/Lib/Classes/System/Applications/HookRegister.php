<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookRegister.php
// @date: 20221116 00:53:02
namespace IGK\System\Applications;
use IGK\System\Core\CookieManager;
use IGKEvents;
use IGKServices;
/**
* auto generate doc.
* @package IGK\System\Applications
*/
class HookRegister
{
    /**
     * system hook registration 
     * @return void 
     */
    public static function Init()
    {
        igk_reg_hook(IGKEvents::HOOK_USER_LOGIN, function ($e) {
            extract($e->args);
            \IGK\Models\LoginLogs::Add(
                $user->clGuid,
                $agent,
                $ip,
                $geox,
                $geoy,
                $region,
                $country_code,
                $country_name,
                $city,
                $status,
                $description
            );
        });
        igk_reg_hook(IGKEvents::HOOK_USER_LOGOUT, function ($e) {
            extract($e->args);
            \IGK\Models\LoginLogs::Add(
                $user->clGuid,
                $agent,
                $ip,
                $geox,
                $geoy,
                $region,
                $country_code,
                $country_name,
                $city,
                $status,
                $description
            );
        });
        if ($_COOKIE && preg_match('/\\b__blf_/', implode('|', array_keys($_COOKIE)))) {
            igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, function () {
                $cl = IGKServices::Get('CookieManager') ?? CookieManager::class;
                $cl::Handle();
            });
            igk_reg_hook(IGKEvents::HOOK_INIT_APP, function () {
                $v_k = 'session-flag';
                $flag = igk_environment()->{$v_k};
                // passeing to application 
                igk_app()->session->{$v_k} = $flag;
                igk_environment()->set($v_k, null);
            });
        }
    }
}