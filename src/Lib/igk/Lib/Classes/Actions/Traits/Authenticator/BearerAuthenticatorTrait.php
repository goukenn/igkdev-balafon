<?php
// @author: C.A.D. BONDJE DOUE
// @file: BearerAuthenticatorTrait.php
// @date: 20230515 10:40:52
namespace IGK\Actions\Traits\Authenticator;

use IGK\System\Http\ErrorRequestResponse;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits\Authenticator
*/
trait BearerAuthenticatorTrait{

    protected $_bearerAuthenticatorCookieLife = 3600;
    protected $_bearerAuthenticatorTokenHash = "-t-!#@4746QD-";
    
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
    protected function bearerAuthenticatorCreateToken($user){
        $str = $this->_bearerAuthenticatorTokenHash.date('YmdHis').$user->clGuid;
        return sha1($str);
    }
    /**
     * 
     * @param mixed $users 
     * @return void 
     */
    protected function bearerAuthenticatorRegisterToken($user){
        if ($user->clStatus != 1){
            return null;
        }
        $connexion = $this->getController()->model(\Connections::class) ?? igk_die('missing connection model'); 
        $token = ''; 
        $token = $this->bearerAuthenticatorCreateToken($user);
        $condition = ['cnx_token' => $token];
        $row = $connexion::select_row($condition);
        $time = ($this->_bearerAuthenticatorCookieLifeConstants / 60.0);
        $expiration_date = strtotime("+$time minutes", time());
        if ($row){
            $connexion::update([
                'cnx_Expire_At' =>  date('Y-m-d H:i:s', $expiration_date)
            ],$condition);
        }else{
            $tokeninfo = (object)[
                'agent'=>igk_server()->HTTP_USER_AGENT,
                'ip'=>igk_server()->REMOTE_ADDR,
                'expire'=>date('Y-m-d H:i:s', $expiration_date)
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
        return $token;
    }
    protected function bearerAuthenticatorGetUserProfileInfo(\IGK\Models\Users $user){
        $userinfo = [];
        $userinfo['user'] = $user;
        $userinfo['groups']= array_map(function($a){ return $a['clName']; }, $user->groups());
        $userinfo['auths'] = array_map(function($a){ return $a['clName']; }, $user->auths());
        return $userinfo;
    }
}