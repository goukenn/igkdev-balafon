<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReigsterUserProfileControllerActionTrait.php
// @date: 20221117 15:39:50
namespace IGK\System\Controllers\Traits;
use IGK\Controllers\BaseController;
/**
* 
* @package IGK\Systems\Controllers\Traits
*/
trait ReigsterUserProfileControllerActionTrait{

    /**
    * Registers.
    * @param BaseController $controller
    * @param string $login
    * @param string $pwd
    * @param string $firstName
    * @param string $lastName
    * @param int $level
    * @param int $status
    * @param mixed $locale
    */
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