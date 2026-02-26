<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserApiChangePwdFormData.php
// @date: 20230505 11:04:34
namespace IGK\Actions\Api\FormData;
use IGK\System\Http\Request;
use IGK\System\WinUI\Forms\FormData;
/**
* 
* @package IGK\Actions\Api\FormData
*/
class UserApiChangePwdFormData extends FormData{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $password;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $confirmPassword;

    /**
    * auto generate doc.
    * @param Request $request
    * @return ?array
    */
    function getContentSecureFormRequest(Request $request): ?array
    {
        return [
            'password'=>$request->getContentSecurity(self::SC_PASSWORD),
            'confirmPassword'=>$request->getContentSecurity(self::SC_PASSWORD)
        ];
    }
}