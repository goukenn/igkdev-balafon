<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MacrosHelper.php
// @date: 20220803 13:48:58
// @desc: 

//
// @file: MacrosHelper.php
// @author: C.A.D BONDJE DOUE
// version: 1.0
//
namespace IGK\Helper;

use IGK\Models\Groupauthorizations;
use IGK\Models\Users;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKEvents;
use IGKException;
use IGKObjStorage;
use ModelBase;
use ReflectionException;

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

    /**
     * get user auth 
     * @param Users $user 
     * @param mixed $auths 
     * @param bool $strict 
     * @return bool 
     */
    private static function GetAuth(\IGK\Models\Users $user, $auths, $strict= false):bool{
        /// MARK: auth users 
        // if (igk_environment()->isDev())
        //     return true;
        /**
         * @var ModelBase $q; current model object 
         * */
        if ($user->is_mock()){
            return false;
        }
        $q = $user;
        $key = \IGK\Models\ModelBase::AuthKey;
        $is_auth = false;
        if (!is_array($auths)) {
            if (!is_string($auths)) {
                return false;
            }
            $auths = [$auths];
        } 
        // $data = $q->to_array();
        if (($g = $q->{$key}) === null) {
            $g = [];
            if ($q->clId !==null){
                if ($b = $user->auths()) {
                    // ::getUserAuths($q->clId)
                    foreach ($b as $t) {
                        $g[] = $t->name;
                    } 
                }
                $q->set($key, $g);
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

    /**
     * add User with storage data
     * @param Users $user 
     * @param null|array $data 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function AddUser(\IGK\Models\Users $user, ?array $data){
        $storage = new IGKObjStorage($data); 
        $r = null; 
        // Users::delete($id);
        if (!empty($storage->clLogin) && ($r = Users::select_row([Users::FD_CL_LOGIN=>$storage->clLogin]))){
            // user aleady exists
            igk_hook(IGKEvents::HOOK_USER_EXISTS, [$r]);
            $r = Users::select_row(["clId"=>$r->clId]);
        } else {
            $r = Users::Register($storage->to_array());
        }
        return $r;
    }
}
