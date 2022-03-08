<?php
//
// @file: MacrosHelper.php
// @author: C.A.D BONDJE DOUE
// version: 1.0
//
namespace IGK\Helper;

use IGK\Models\Groupauthorizations;
use IGK\Models\Users;
use IGKEvents;
use IGKObjStorage;

/**
 * macro helper expressions
 * @package IGK\Helper
 */
class MacrosHelper
{
    private static $macros;

    public static function Get($name)
    {
        return self::__callStatic($name, null);
    }
    public static function __callStatic($name, $arguments)
    {
        if (self::$macros == null) {
            //init global macros function 
            self::$macros = [
                "auth" => function ($auths, $strict = false) {
                    // igk_wln_e(__FILE__.":".__LINE__, "auth context ");
                    /**
                     * @var \IGK\Models\Users $q 
                     */
                    $q = $this;
                    return self::GetAuth($q, $auths, $strict);                    
                },
                "currentUser"=>function()
                {
                    if ($u = igk_app()->session->getUser()){
                        return \IGK\Models\Users::createFromCache($u);
                    }
                    return null;
                },
                "addUser2"=>function($data){
                    /**
                     * @var \IGK\Models\Users $q 
                     */
                    $q = $this;
                    return self::AddUser($q, $data);
                }
            ];
        }
        return igk_getv(self::$macros, $name);
    }


    private static function GetAuth(\IGK\Models\Users $user, $auths, $strict= false){
        /// MARK: auth users 
        // if (igk_environment()->is("DEV"))
        //     return true;
        /**
         * @var ModelBase $q; current model object 
         * */
        $q = $user;
        if (!is_array($auths)) {
            if (!is_string($auths)) {
                return false;
            }
            $auths = [$auths];
        } 
        $data = $q->to_array();
        if (($g = $q->{"::auth"}) === null) {
            $g = [];
            if ($q->clId !==null){
                if ($b = Groupauthorizations::getUserAuths($q->clId)) {
                    foreach ($b as $t) {
                        $g[] = $t->auth_name;
                    }
                }
                $q->set("::auth", $g);
            } else 
                return false;
        }
        if (($is_auths = count($g) > 0)) {

            if ($strict) {
                while ($is_auths && ($auth = array_shift($auths))) {
                    // check all auths
                    if (!($is_auths = in_array($auth, $g))) {
                        break;
                    }
                }
            } else {
                $is_auths = false;
                while ($auth = array_shift($auths)) {
                    if (in_array($auth, $g)) {
                        $is_auths = true;
                        break;
                    }
                }
            }
        }
        return $is_auths;
    }

    public static function AddUser(\IGK\Models\Users $user, $data){
        $storage = new IGKObjStorage($data); 
        $r = null; 
        // Users::delete($id);
        if (!empty($storage->clLogin) && ($r = Users::select_row(["clLogin"=>$storage->clLogin]))){
            // user aleady exists
            igk_hook(IGKEvents::HOOK_USER_EXISTS, [$r]);
            $r = Users::select_row(["clId"=>$r->clId]);
        } else {
            if ($r = Users::create($storage->to_array())){
                igk_hook(IGKEvents::HOOK_USER_ADDED, [$r]);
                $r = Users::select_row(["clId"=>$r->clId]);
            }
        }
        return $r;
        
    }
}
