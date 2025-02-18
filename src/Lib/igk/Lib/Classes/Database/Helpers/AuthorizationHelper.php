<?php
// @author: C.A.D. BONDJE DOUE
// @file: AuthorizationHelper.php
// @date: 20250210 16:17:11
namespace IGK\Database\Helpers;

use IGK\Controllers\BaseController;

///<summary></summary>
/**
* 
* @package IGK\Database\Helpers
* @author C.A.D. BONDJE DOUE
*/
class AuthorizationHelper{
    /**
     * map authorization keys
     * @param mixed $auths 
     * @param BaseController|null $ctrl 
     * @return array<string|int, mixed> 
     */
    public static function Map($auths, BaseController $ctrl=null){
        $ctrl = $ctrl ?? igk_current_ctrl();
        if (!is_array($auths)){
            $auths = [$auths];
        }
        return array_filter(array_map(function($u) use($ctrl){
            if (empty($u)){
                return null;
            }
            if (strpos($u, '@')!==false){
                return $u;
            }
            return $ctrl::authName($u);
        }, $auths));
    } 
}