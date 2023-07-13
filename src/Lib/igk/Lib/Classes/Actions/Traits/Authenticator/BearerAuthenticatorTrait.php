<?php
// @author: C.A.D. BONDJE DOUE
// @file: BearerAuthenticatorTrait.php
// @date: 20230515 10:40:52
namespace IGK\Actions\Traits\Authenticator;

use IGK\Controllers\BaseController;
use IGK\Models\Users;
use IGK\System\Http\ErrorRequestResponse;
use IGK\System\Http\Responses\UserResponse;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits\Authenticator
*/
trait BearerAuthenticatorTrait{

    protected $_bearerAuthenticatorCookieLife = 3600;
    protected $_bearerAuthenticatorTokenHash = "-t-!#@4746QD-";
    protected $_bearerAuthenticatorCookieLifeConstants = 60*60*60*24;    // 60 days
    protected abstract function getUserFromToken(bool $update = true );
  /**
     * get token user or die
     * @return ?Users 
     * @throws IGKException 
     */
    protected function getUserFromTokenOrDie(){
        $user = $this->getUserFromToken() ?? igk_do_response(new ErrorRequestResponse(401, "unauthenticated"));
        return $user;
    } 

    /**
     * generate token hash code 
     * @param mixed $user 
     * @return string 
     */
    protected function bearerAuthenticatorCreateToken($user, string $prefix="blf-"){
        $str = $this->_bearerAuthenticatorTokenHash.date('YmdHis').$user->clGuid;
        return $prefix.sha1($str);
    }
    /**
     * create and register bearer token for active user
     * @param mixed $users 
     * @return void 
     */
    protected function bearerAuthenticatorRegisterToken(Users $user, BaseController $ctrl, bool $rememberme=false){
        if ($user->clStatus != 1){
            return null;
        } 
        $connexion = $ctrl->model(\Connections::class) ?? igk_die('missing connection model'); 
        $format = $connexion->getDataAdapter()->getDateTimeFormat();
        $token = ''; 
        $token = $this->bearerAuthenticatorCreateToken($user);
        $condition = ['cnx_token' => $token];
        $row = $connexion::select_row($condition);
        $time = ($this->_bearerAuthenticatorCookieLifeConstants / 60.0);
        $v_time = time();
        $expiration_date = strtotime("+$time minutes", $v_time);
        $exp_format = date('Y-m-d H:i:s', $expiration_date);
        $rememberbe = false;
        $start = null;

        if ($row){
            $info = json_decode($row->cnx_token_info);
            // if (!property_exists($info, 'start')){
            //     $info->start = $info->
            // }
            $info->expire = $exp_format;
            $rememberme = $info->remembeme;
            $connexion::update([
                'cnx_Expire_At' => $exp_format
            ],$condition);

        }else{
            $tokeninfo = (object)[
                'agent'=>igk_server()->HTTP_USER_AGENT,
                'ip'=>igk_server()->REMOTE_ADDR,
                'expire'=>$exp_format,
                'start'=>$start = date($format, $v_time),
                'rememberme'=>$rememberme
            ];
            // connexion token - 
            
            $connexion::create([
                'cnx_user_guid' => $user->clGuid,
                'cnx_token' => $token,
                'cnx_token_info' => json_encode($tokeninfo),
                'cnx_logout_At' => NULL,
                'cnx_Expire_At' =>  $tokeninfo->expire, 
            ]);
        }  
        igk_set_cookie('token', $token, true, $this->_bearerAuthenticatorCookieLife);
        return ['token'=>$token, 'expire'=>$exp_format, 'start'=>$start, 'remember-me'=>$rememberme];
    }
    protected function bearerAuthenticatorGetUserProfileInfo(\IGK\Models\Users $user){
        return UserResponse::CreateResponseFromUserModel($user); 
    }
}