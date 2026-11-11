<?php
// @author: C.A.D. BONDJE DOUE
// @file: HookRegister.php
// @date: 20221116 00:53:02
namespace IGK\System\Applications;

use Closure;
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
    private static function _ExtractLog($data){
        extract($data);
        return [
            \IGK\Models\LoginLogs::FD_LOGLOGS_USER_GUID=> $user->clGuid,
            \IGK\Models\LoginLogs::FD_LOGLOGS_AGENT => $agent,
            \IGK\Models\LoginLogs::FD_LOGLOGS_IP => $ip,
            \IGK\Models\LoginLogs::FD_LOGLOGS_GEO_X => $geox,
            \IGK\Models\LoginLogs::FD_LOGLOGS_GEO_Y => $geoy,
            \IGK\Models\LoginLogs::FD_LOGLOGS_REGION=> $region,
            \IGK\Models\LoginLogs::FD_LOGLOGS_CODE => $country_code,
            \IGK\Models\LoginLogs::FD_LOGLOGS_COUNTRY_NAME => $country_name,
            \IGK\Models\LoginLogs::FD_LOGLOGS_CITY => $city,
            \IGK\Models\LoginLogs::FD_LOGLOGS_STATUS => $status,
            \IGK\Models\LoginLogs::FD_LOGLOGS_DESCRIPTION => $description
        ];
    }
    /**
     * system hook registration 
     * @return void 
     */
    public static function Init()
    {
        $_env = igk_environment();
        igk_reg_hook(IGKEvents::HOOK_USER_LOGIN, function ($e) {
            extract($e->args);
            $og = self::_extractLog(get_defined_vars());
            \IGK\Models\LoginLogs::Insert($og);
        });
        igk_reg_hook(IGKEvents::HOOK_USER_LOGOUT, function ($e) {
            extract($e->args);
            $og = self::_extractLog(get_defined_vars());
            \IGK\Models\LoginLogs::Insert($og);

        });

        igk_reg_hook(IGKEvents::HOOK_LOGIN_NEW_PROFILE, function ($e) {
            $user = $e->args['user'];
            if (IGKValidator::IsEmail($user->clLogin)) {
                // + | register to mail list  
                Mailinglists::createIfNotExists([
                    Mailinglists::FD_CLML_EMAIL => $user->clLogin,
                    Mailinglists::FD_CLML_STATE => 1,
                    Mailinglists::FD_CLML_SOURCE => igk_configs()->website_domain,
                    Mailinglists::FD_CLML_AGENT => IGKUserAgent::Agent(),
                    Mailinglists::FD_CLML_LOCALE => $user->clLocale,
                ]);
            }
        });

        if ($_COOKIE && preg_match('/\\b__blf_/', implode('|', array_keys($_COOKIE)))) {
            igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, function () {
                $cl = IGKServices::Get('CookieManager') ?? CookieManager::class;
                $cl::Handle();
            });
            igk_reg_hook(IGKEvents::HOOK_INIT_APP, function () use ($_env) {
                $v_k = 'session-flag';
                $flag = $_env->{$v_k};
                igk_app()->session->{$v_k} = $flag;
                $_env->set($v_k, null);
            });
        }
        igk_reg_hook(IGKEvents::HOOK_INIT_APP, Closure::fromCallable([self::class, 'OnAppInitialized']));
    }
    /**
    * raise on application initialized
    * @param mixed $e
    * @return void
    */
    protected static function OnAppInitialized($e)
    {
        $_env = igk_environment();
        if ($_env->isDev()) {
            igk_reg_hook(IGKEvents::HOOK_HTML_BODY, function ($e) {
                $options = igk_getv($e->args, 'options');
                if (!$options || !($doc = $options->Document))
                    return;
                if ($doc->noColorPalette)
                    return; 
                
                $t = igk_create_notagnode();
                $type = igk_configs()->get('css.colorspace') ??  'hsl';
                igk_sys_css_inject_colors_space($t, $type);
                $t->renderAJX();
            });
        }
    }
}
