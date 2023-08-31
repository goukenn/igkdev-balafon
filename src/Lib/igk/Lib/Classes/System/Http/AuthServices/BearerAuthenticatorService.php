<?php
// @author: C.A.D. BONDJE DOUE
// @file: BearerAuthenticatorService.php
// @date: 20230707 21:30:43
namespace IGK\System\Http\AuthServices;

use IGK\Actions\Traits\Authenticator\BearerAuthenticatorTrait;
use IGK\Controllers\BaseController;
use IGK\Models\ModelBase;
use IGK\Models\Users;
use IGK\System\Database\IUserProfile;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Http\IAuthenticatorService;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\System\Http\AuthServices
*/
class BearerAuthenticatorService implements IAuthenticatorService{
    use BearerAuthenticatorTrait;

    /**
     * get user from token
     * @param bool $update 
     * @param mixed $token 
     * @return null|ModelBase 
     * @throws IGKException 
     */
    protected function getUserFromToken(bool $update = true, & $token=null) : ?ModelBase{ 
        throw new NotImplementException(__METHOD__);    
    }
    /**
     * create use profile from application'user
     */
    protected function userProfileFromApplicationUser(ModelBase $app_user): ?IUserProfile{
        throw new NotImplementException(__METHOD__);
    }


    public function resolveBearerToken(bool $update, & $token =null){
        return $this->getUserFromToken($update, $token);
    }
    /**
     * 
     * @param Users system user 
     * @param BaseController $ctrl controller 
     * @param bool $rememberme remember me
     * @return ?array token info
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getNewToken(Users $user, BaseController $ctrl, bool $rememberme=false): ?array{
        return $this->bearerAuthenticatorRegisterToken($user, $ctrl, $rememberme); 
    }
}