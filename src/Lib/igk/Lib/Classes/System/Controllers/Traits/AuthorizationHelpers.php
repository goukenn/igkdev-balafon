<?php
// @author: C.A.D. BONDJE DOUE
// @file: AuthorizationHelpers.php
// @date: 20251210 18:59:08
namespace IGK\System\Controllers\Traits;
use IGK\Controllers\BaseController;
use IGK\Models\Users;
/**
* auto generate doc.
* @package IGK\System\Controllers\Traits
* @author C.A.D. BONDJE DOUE
*/
trait AuthorizationHelpers{
    /**
     * check if user is allowed to / check user auth demand level
     * @param BaseController $ctrl 
     * @param string $auth 
     * @param null|Users $user 
     * @return bool 
     */
    public static function isUserAllowedTo(BaseController $ctrl, string $auth, $user =null):bool{
        $user = $user ?? $ctrl->getUser();
        if (!$user){
            return false;
        }
        if ($user->clLevel == -1)
            return true;
        return $user->auth($ctrl->authName($auth));
    }
} 