<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserResponse.php
// @date: 20230427 16:54:31
namespace IGK\System\Http\Responses;

use IGK\Controllers\BaseController;
use IGK\Helper\ActionHelper;
use IGK\Helper\Activator;
use IGK\Models\Users;
use IGK\System\Database\IUserProfile;
use IGK\System\Http\IAuthenticatorService;

///<summary></summary>
/**
 * 
 * @package IGK\System\Http\Responses
 */
class UserResponse
{
    var $user;
    var $groups;
    var $auths;
    var $token_info;
    var $message;
    var $app;

    private static function _CreateUserData(Users $user){
        return $user->CreateUserApiResponseData();
    }
    public static function CreateResponseFromUserModel(Users $user){
        $data = self::_CreateUserData($user);
        return $data;
    }
    public static function CreateResponse(IUserProfile $profile, BaseController $ctrl, IAuthenticatorService $authenticator, bool $rememberme=false)
    {
        $app = $profile->user();
        $user = $profile->model();
        $token = $authenticator->getNewToken($user, $ctrl, $rememberme); 
        $data = array_merge(self::_CreateUserData($user), [
            'app'=>$app,
            'token_info'=>$token
        ]); 
        igk_hook('filter_user_response_data', (object)['data'=>& $data]);
        return Activator::CreateNewInstance(static::class, $data);
    }
    function __debugInfo()
    {
        return [];
    }
}
