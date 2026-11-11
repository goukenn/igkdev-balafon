<?php
// @author: C.A.D. BONDJE DOUE
// @filename: LoginServiceEvents.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Services;

use IGK\Controllers\BaseController;
use IGK\System\Http\Request;

/**
 * represent base service provider event
 * @package IGK\System\Services
 */
class LoginServiceEvents
{
    /**
     * Constant: login with social button.
     * @var mixed
     */
    const LoginWithSocialButton = "LoginWithSocialButton";

    /**
     * retrieve authentication controller 
     * @param mixed $ctrl 
     * @return mixed 
     */
    public static function AuthenticationController($ctrl)
    {
        $t = igk_app()->getConfigs()->authentication_controller;
        if ($t) {
            $t = igk_getctrl($t) ?? igk_die('missing controller');
        }
        $lf = $t ?? igk_get_defaultwebpagectrl() ?? $ctrl;
        return $lf;
    }
    /**
     * 
     * @param string $uri 
     * @return void 
     */
    public static function StoreRedirect(string $uri)
    {
        igk_app()->session->{self::class . '_community_redirect'} = $uri;
    }
    /**
     * 
     * @param mixed $card 
     * @param mixed $request 
     * @param mixed $ctrl 
     * @param mixed $style arrayof<{host_or:string, community: string}> 
     * @return void 
     */
    public static function BuildViewButton($card, Request $request, BaseController $ctrl, $style = null)
    {
        $community = [];
        $lf = LoginServiceEvents::AuthenticationController($ctrl);
        igk_hook(LoginServiceEvents::LoginWithSocialButton, ['filter' => &$community, 'ctrl' => $lf]);
        if ($community) {
            

            list($host_or, $c_s) = igk_extract($style ?? [], 'host_or|community');
            LoginServiceEvents::StoreRedirect($request->redirect ?? $ctrl::uri('@/'));
            $card->add("p")->setAttributes(["class" => $host_or ?? "vdm-muted vdm-or"])->Content = __('or');
            igk_html_build_community_button($card, $community, $lf, $card->add("div")->setAttributes(["class" => $c_s ?? "vdm-community"]));
        }
    }
}
