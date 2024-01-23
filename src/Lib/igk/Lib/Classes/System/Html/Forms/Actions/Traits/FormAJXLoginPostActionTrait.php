<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormAJXLoginPostActionTrait.php
// @date: 20230427 12:51:05
namespace IGK\System\Html\Forms\Actions\Traits;

use IGK\Actions\ActionBase;
use IGK\Database\Macros\UsersMacros;
use IGK\Models\Users;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Http\IAuthenticatorService;
use IGK\System\Http\Request;
use IGK\System\Http\Responses\UserResponse;
use IGK\System\Http\WebResponse;
use IGKActionBase;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Forms\Actions\Traits
 */
trait FormAJXLoginPostActionTrait
{
    /**
     * 
     * @param Request $request 
     * @param string $login 
     * @param string $password 
     * @param null|IAuthenticatorService $authenticator 
     * @param bool $remember_me 
     * @param bool $redirect 
     * @return object|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function ajx_login(Request $request, string $login, string $password, ?IAuthenticatorService $authenticator, bool $remember_me=false, bool $redirect=false){
        $ctrl = $this->getController();
        $g = $ctrl->login($login, $password, false);
        if ($g) {
            $user_profile = $ctrl->getUser();
            $user = $user_profile->model();
            $token = self::RegisterToken($user);
            igk_set_cookie('token', $token, true, 3600);
            $rep = UserResponse::CreateResponse($user_profile, $ctrl, $authenticator, $remember_me);
            $rep->token = $token;
            return WebResponse::Create('json', array_merge([
                    'status' =>  1,
                    ], (array)$rep));
        } else {
            $this->die("login failed.---", 403);
        }
    }
    /**
     * 
     * @param Request $request 
     * @param IAuthenticatorService $authenticator 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function login_post(Request $request, IAuthenticatorService $authenticator)
    {
        /**
         * @var ?object $requestData
         */      
        $requestData = null;
        $v_self = $this;
        $error = [];
        if ($data = $request->getJsonData()) {
            if (($v_self instanceof IGKActionBase ) && $v_self->getValidator()->validate($data, [
                'email' => $request->getContentSecurity('Email'),
                'password' => $request->getContentSecurity('Password')
            ], null, null, $requestData, $error)) {
                return $this->ajx_login($request, $requestData->email, $requestData->password, $authenticator, false);               
            }
        }
        return $this->die("Unauthenticated", 401);
    }

    static abstract function RegisterToken(Users $user): string;
}
