<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReigsterUserProfileControllerActionTrait.php
// @date: 20221117 15:39:50
namespace IGK\Systems\Controllers\Traits;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\Systems\Controllers\Traits
*/
trait ReigsterUserProfileControllerActionTrait{
    public static function Register(
        BaseController $controller,
        string $login,
        string $pwd = '',
        string $firstName = '',
        string $lastName = '',
        int $level = 0,
        int $status = 0,
        $locale = 'fr'
    ) {
        return false;
    }
}