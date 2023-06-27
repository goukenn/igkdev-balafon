<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormAJXLoginPostActionTrait.php
// @date: 20230427 12:51:05
namespace IGK\System\Html\Forms\Actions\Traits;

use IGK\Actions\ActionBase;
use IGK\Database\Macros\UsersMacros;
use IGK\Models\Users;
use IGK\System\Http\Request;
use IGK\System\Http\Responses\UserResponse;
use IGK\System\Http\WebResponse;
use IGKActionBase;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Forms\Actions\Traits
 */
trait FormAJXLoginPostActionTrait
{
    protected function ajx_login(Request $request, $login, $password, bool $redirect=false){
        $ctrl = $this->getController();
        $g = $ctrl->login($login, $password, false);
        if ($g) {
            $user_profile = $ctrl->getUser();
            $user = $user_profile->model();
            $token = self::RegisterToken($user);
            igk_set_cookie('token', $token, true, 3600);
            $rep = UserResponse::CreateResponse($user_profile);
            $rep->token = $token;
            return WebResponse::Create('json', array_merge([
                    'status' =>  1,
                    ], (array)$rep));
        } else {
            $this->die("login failed.---", 403);
        }
    }
    public function login_post(Request $request)
    {
        /**
         * @var ?object $requestData
         */      
        $requestData = null;
        $x = $this;
        $error = [];
        if ($data = $request->getJsonData()) {
            if (($x instanceof IGKActionBase ) && $x->getValidator()->validate($data, [
                'email' => $request->getContentSecurity('Email'),
                'password' => $request->getContentSecurity('Password')
            ], null, null, $requestData, $error)) {
                return $this->ajx_login($request, $requestData->email, $requestData->password, false);               
            }
        }
        return $this->die("Unauthenticated", 401);
    }

    static abstract function RegisterToken(Users $user): string;
}
