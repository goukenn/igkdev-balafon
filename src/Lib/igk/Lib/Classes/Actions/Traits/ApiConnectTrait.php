<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApiConnectTrait.php
// @date: 20230725 10:47:20
namespace IGK\Actions\Traits;

use Exception;
use IGK\Controllers\BaseController;
use IGK\System\Database\IUserProfile;
use IGK\System\Http\Request;
use IGK\System\Http\WebResponse;

///<summary></summary>
/**
 * 
 * @package IGK\Actions\Traits
 */
trait ApiConnectTrait
{
    /**
     * bearer connect
     * @responses()
     * @security(['BearerAuth'])
     * @request({"login":{"type":"string"}, "password":{"type":"string", "format":"password"}})
     * @return Reponse
     */
    public function connect_post(Request $request, BaseController $ctrl)
    {

        try {
            $user = $this->getUserFromToken();
            // + | already connected 
            if ($user) {
                return WebResponse::Create('json', ['user' => 1]);
            }
        } catch (Exception $ex) {
        }

        if ($data = $request->getJsonData()) {
            $rdata = null;
            $error = null;
            if ($g = $this->getValidator()->validate($data, ['login', 'password'], null, null, $rdata, $error)) {
                if ($rg = $ctrl->login($rdata->login, $rdata->password, false)) {
                    if (($u_profile = $ctrl->getUser()) instanceof IUserProfile) {
                        $user = $u_profile->model();
                        if ($token = $this->bearerAuthenticatorRegisterToken($user, $ctrl, $request->get('remember_me', false))) {
                            $userinfo = $this->bearerAuthenticatorGetUserProfileInfo($user);
                            return compact('token', 'userinfo');
                        }
                    }
                }
            }
        }
        $this->die("connection failed", 401);
    }

    /**
     * check bearer connection
     * @responses()
     * @security(['BearerAuth'])
     * @request({"login":{"type":"string"}, "password":{"type":"string", "format":"password"}})
     * @return mixed 
     */
    public abstract function check_login_post();
}
