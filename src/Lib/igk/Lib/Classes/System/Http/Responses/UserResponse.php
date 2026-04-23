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
* auto generate doc.
* @package IGK\System\Http\Responses
*/
/**
* auto generate doc.
* @package IGK\System\Http\Responses
*/
class UserResponse
{
    /**
    * Property: user.
    * @var mixed
    */
    var $user;
    /**
    * Property: groups.
    * @var mixed
    */
    var $groups;
    /**
    * Property: auths.
    * @var mixed
    */
    var $auths;
    /**
    * Property: token info.
    * @var mixed
    */
    var $token_info;
    /**
    * Property: message.
    * @var mixed
    */
    var $message;
    /**
    * Property: user app.
    * @var mixed
    */
    var $user_app;
    /**
    * Property: controller.
    * @var mixed
    */
    var $controller;
    /**
    * auto generate doc.
    * @param Users $user
    * @return
    */
    private static function _CreateUserData(Users $user){
        return $user->CreateUserApiResponseData();
    }
    /**
    * Creates Response From User Model.
    * @param Users $user
    */
    public static function CreateResponseFromUserModel(Users $user){
        $data = self::_CreateUserData($user);
        return $data;
    }
    /**
    * auto generate doc.
    * @param bool $rememberme
    * @return mixed
    */
    public static function CreateResponse(IUserProfile $profile, BaseController $ctrl, IAuthenticatorService $authenticator, bool $rememberme=false)
    {
        $app = $profile->user();
        $user = $profile->model();
        $token = $authenticator->getNewToken($user, $ctrl, $rememberme); 
        return self::CreateResponseFromSystemUser($ctrl, $user, $app, $token); 
    }
    /**
    * Creates Response From System User.
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