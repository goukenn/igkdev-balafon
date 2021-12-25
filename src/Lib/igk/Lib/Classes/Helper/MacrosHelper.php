<?php

namespace IGK\Helper;

use IGK\Models\Groupauthorizations;

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
                    return self::GetAuth($this, $auths, $strict);                    
                },
                "currentUser"=>function()
                {
                    if ($u = igk_app()->session->getUser()){
                        return \IGK\Models\Users::createFromCache($u);
                    }
                    return null;
                }
            ];
        }
        return igk_getv(self::$macros, $name);
    }


    private static function GetAuth(\IGK\Models\Users $user, $auths, 
    $strict= false){
        /// MARK: auth users 
        if (igk_environment()->is("DEV"))
        return true;
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
        // igk_wln("check ". implode(", ", $auths));
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
}
