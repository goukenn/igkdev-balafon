<?php
// @author: C.A.D. BONDJE DOUE
// @file: Authorization.php
// @date: 20221118 10:57:56
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;
use IGK\Models\Usergroups;
use IGK\Models\Users;
use IGK\Models\Authorizations;

///<summary></summary>
/**
* autorisation helper class 
* @package IGK\Helper
*/
class Authorization{
    /**
     * bind user to group
     * @param BaseController $controller 
     * @param Users $user system user 
     * @param string $group controller's group without key identification. 
     * @return object|null|false 
     */
    public static function BindUserToGroup(BaseController $controller, Users $user, string $group){
        $keyname = StringUtility::GetControllerKeyName($controller);
        $gid = Groups::createIfNotExists(["clName"=>$group, "clController"=>$keyname]);
        if (!$gid){
            return false;
        }
        $gid = $gid->clId;
        $gp =  Usergroups::insertIfNotExists([
            'clUser_Id' => $user->clId,
            'clGroup_Id' => $gid,
        ]);
        return $gp;
    }
    /**
     * remove user attached to controller groups
     * @param BaseController $controller 
     * @param Users $user 
     * @param string $group 
     * @return bool 
     */
    public static function UnbindUserFromGroup(BaseController $controller, Users $user, string $group){
        $keyname = StringUtility::GetControllerKeyName($controller);
        $gid = Groups::createIfNotExists(["clName"=>$group, "clController"=>$keyname]);
        if (!$gid){
            return false;
        }
        $gid = $gid->clId;
        $gp =  Usergroups::delete([
            'clUser_Id' => $user->clId,
            'clGroup_Id' => $gid,
        ]);
        return $gp;
    }
    /**
     * grant autorization to group
     * @param BaseController $controller 
     * @param string $autorizationName 
     * @return void 
     */
    public static function BindControllerAuth(?BaseController $controller, string $autorizationName, string $groupName, $grant=1){
        $name = is_null($controller) ? null : igk_uri(get_class($controller));
        $auth_name = StringUtility::AuthorizationPath($autorizationName, $name);
        $auth = Authorizations::createIfNotExists(['clController'=>$name, 'clName'=>$auth_name]);
        $group = Groups::createIfNotExists(['clController'=>$name, 'clName'=>$groupName]);

        return Groupauthorizations::createIfNotExists([
            "clGroup_Id"=>$group->clId,
            "clAuth_Id"=>$auth->clId,
            "clGrant"=>$grant
        ]);
    }
}