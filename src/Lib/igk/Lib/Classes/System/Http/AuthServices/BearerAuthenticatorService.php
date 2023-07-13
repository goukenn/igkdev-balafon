<?php
// @author: C.A.D. BONDJE DOUE
// @file: BearerAuthenticatorService.php
// @date: 20230707 21:30:43
namespace IGK\System\Http\AuthServices;

use IGK\Actions\Traits\Authenticator\BearerAuthenticatorTrait;
use IGK\Controllers\BaseController;
use IGK\Models\Users;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
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

    protected function getUserFromToken(bool $update = true) { 

    }

    public function resolveBearerToken(bool $update){
        return $this->getUserFromToken();
    }
    /**
     * 
     * @param Users system user 
     * @param BaseController $ctrl controller 
     * @param bool $rememberme remember me
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getNewToken(Users $user, BaseController $ctrl, bool $rememberme=false){
        return $this->bearerAuthenticatorRegisterToken($user, $ctrl, $rememberme); 
    }
}