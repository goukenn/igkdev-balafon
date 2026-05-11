<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitManagement.php
// @date: 20251112 14:45:20
namespace IGK\Database\Helpers;
use IGK\Controllers\BaseController;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
/**
* auto generate doc.
* @package IGK\Database\Helper
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Database\Helpers
*/
class DbInitManagement
{
    /**
    * auto generate doc.
    * @param null|BaseController $controller
    */
    public static function RegisterGroupAndAuth(string $name, ?BaseController $controller)
    {
        $fd_name = IGK_FD_NAME;
        $top = [];
        if ($controller) {
            $top['clController'] = igk_uri(get_class($controller));
        }
        $top[$fd_name] = $name;
        $group = Groups::createIfNotExists($top);
        $top[$fd_name] = $controller::authName($name);
        $auth = Authorizations::createIfNotExists($top);
        return [$group, $auth];
    }
    /**
    * auto generate doc.
    * @param BaseController $controller
    * @param ?bool $reset
    * @return void
    */
    public static function InitControllerProfile(BaseController $controller, ?bool $reset = false)
    {
        $fd_name = IGK_FD_NAME;
        $keyname = $controller ? igk_uri(get_class($controller)) : null;
        if ($reset){
        }
        $tpro = null;
        $pro = $controller->configFile('profiles');
        if ($pro && igk_io_file_exists($pro)) {
            $tpro = include($pro);
        }
        if (!is_array($tpro))
            return;
        $roles = []; 
        $auths = []; 
        $v_auths = [];
        if ($keyname) {
            $v_auths['clController'] = $keyname;
        }
        foreach ($tpro as $k => $c) {
            if (!isset($roles[$k])) {
                $roles[$k] = self::RegisterGroupAndAuth($k, $controller);
            }
            if (is_string($c)) {
                igk_wln_e("not ok: " . $pro);
            }
            foreach ($c as $m) {
                if (!isset($auths[$m])) {
                    if ($auth = igk_getv($roles, $m)) {
                        $auth = $auth[1];
                    }
                    $v_auths[$fd_name] =  $controller::authName($m);
                    $auths[$m] = $auth ?? Authorizations::createIfNotExists($v_auths);
                }
                if ($auths[$m] && $roles[$k][0]) {
                    Groupauthorizations::createIfNotExists([
                        "clGrant" => 1,
                        "clGroup_Id" => $roles[$k][0]->clId,
                        "clAuth_Id" => $auths[$m]->clId,
                    ]);
                } else {
                    igk_ilog("can't create pofile setting : " . get_class($controller));
                }
            }
        }
    }
}