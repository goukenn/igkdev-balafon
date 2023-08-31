<?php
// @author: C.A.D. BONDJE DOUE
// @file: IAuthenticatorService.php
// @date: 20230707 21:20:27
namespace IGK\System\Http;

use IGK\Controllers\BaseController;
use IGK\System\IInjectable;

///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
interface IAuthenticatorService extends IInjectable{
    function getNewToken(\IGK\Models\Users $user, BaseController $ctrl, bool $remember_me=false):?array;
}