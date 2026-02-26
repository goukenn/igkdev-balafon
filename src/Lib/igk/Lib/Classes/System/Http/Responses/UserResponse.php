<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserResponse.php
// @date: 20230427 16:54:31
namespace IGK\System\Http\Responses;
use IGK\Controllers\BaseController;
use IGK\Database\Mapping\SysDbMapping;
use IGK\Helper\ActionHelper;
use IGK\Helper\Activator;
use IGK\Models\Users;
use IGK\System\Database\IUserProfile;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\IAuthenticatorService;
use IGKException;
use ReflectionException;
/**
 * 
 * @package IGK\System\Http\Responses
 */
class UserResponse
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $user;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $groups;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $auths;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $token_info;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $message;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $user_app;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $controller;
    private static function _CreateUserData(Users $user){
        return $user->CreateUserApiResponseData();
    }

    /**
    * auto generate doc.
    * @param Users $user
    */
    public static function CreateResponseFromUserModel(Users $user){
        $data = self::_CreateUserData($user);
        return $data;
    }
    /**
     * 
     * @param IUserProfile $profile 
     * @param BaseController $ctrl 
     * @param IAuthenticatorService $authenticator 
     * @param bool $rememberme 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public static function CreateResponse(IUserProfile $profile, BaseController $ctrl, IAuthenticatorService $authenticator, bool $rememberme=false)
    {
        $app = $profile->user();
        $user = $profile->model();
        $token = $authenticator->getNewToken($user, $ctrl, $rememberme); 
        return self::CreateResponseFromSystemUser($ctrl, $user, $app, $token); 
    }

    /**
    * auto generate doc.
    * @param BaseController $ctrl
    * @param mixed $user
    * @param null|mixed $app_user
    * @param null|mixed $token
    */
    public static function CreateResponseFromSystemUser(BaseController $ctrl, $user, $app_user=null, $token=null){
        $data = array_merge(self::_CreateUserData($user), [
            'user_app'=>$app_user ? SysDbMapping::CreateMapping($app_user)->map($app_user) :null,
            'token_info'=>$token,
            'controller'=>$ctrl->getName(),
        ]); 
        igk_hook('filter_user_response_data', (object)['data'=>& $data]);
        return Activator::CreateNewInstance(static::class, $data);
    }

    /**
    * Used by var_dump() to customize debug output.
    */
    function __debugInfo()
    {
        return [];
    }
}