<?php
// @author: C.A.D. BONDJE DOUE
// @file: AuthenticationFormData.php
// @date: 20230707 17:48:38
namespace IGK\FormData;

use IGK\System\Http\IContentSecurityProvider;
use IGK\System\Http\Request;
use IGK\System\Security\Web\MapContentValidatorBase;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\FormData
*/
class AuthenticationFormData extends FormBase {
    /**
     * user login - email 
     * @var mixed
     */
    var $login;
    /**
     * attached password
     * @var mixed
     */
    var $password;   
    /**
     * remember me 
     * @var bool
     */
    var $rememberme = false; 


    /**
     * get validation data
     * @param Request $request 
     * @return MapContentValidatorBase[] 
     * @throws IGKException 
     */
    public static function ValidationData(IContentSecurityProvider $request){
    
        return [
            'login'=>$request->getContentSecurity('LoginAccess'),          
            'password'=>$request->getContentSecurity('Password'),
            'rememberme'=>function($o){ 
                return $o ? true : false;
            },
        ];
    }
}