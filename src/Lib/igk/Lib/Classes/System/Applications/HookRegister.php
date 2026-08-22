<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookRegister.php
// @date: 20221116 00:53:02
namespace IGK\System\Applications;

use IGK\Models\Mailinglists;
use IGK\System\Core\CookieManager;
use IGKEvents;
use IGKServices;
use IGKUserAgent;
use IGKValidator;

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

        igk_reg_hook(IGKEvents::HOOK_LOGIN_NEW_PROFILE, function ($e) {
            $user = $e->args['user'];
            if (IGKValidator::IsEmail($user->clLogin)) {
                // + | register to mail list  
                Mailinglists::createIfNotExists([
                    Mailinglists::FD_CLML_EMAIL => $user->clLogin,
                    Mailinglists::FD_CLML_STATE => 1,
                    Mailinglists::FD_CLML_SOURCE => igk_configs()->website_domain,
                    Mailinglists::FD_CLML_AGENT=>IGKUserAgent::Agent(),
                    Mailinglists::FD_CLML_LOCALE=>$user->clLocale,
                ]);
            }
        });

        if ($_COOKIE && preg_match('/\\b__blf_/', implode('|', array_keys($_COOKIE)))) {
            igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, function () {
                $cl = IGKServices::Get('CookieManager') ?? CookieManager::class;
                $cl::Handle();
            });
            igk_reg_hook(IGKEvents::HOOK_INIT_APP, function () {
                $v_k = 'session-flag';
                $flag = igk_environment()->{$v_k};
                igk_app()->session->{$v_k} = $flag;
                igk_environment()->set($v_k, null);
            });
        }
    }
}
