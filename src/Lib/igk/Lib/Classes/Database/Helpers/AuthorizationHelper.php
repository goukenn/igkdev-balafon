<?php
// @author: C.A.D. BONDJE DOUE
// @file: AuthorizationHelper.php
// @date: 20250210 16:17:11
namespace IGK\Database\Helpers;

use IGK\Controllers\BaseController;
use IGK\Helper\StringUtility;

/**
 * auto generate doc.
 * @package IGK\Database\Helpers
 * @author C.A.D. BONDJE DOUE
 */
class AuthorizationHelper
{
    /**
     * map authorization keys
     * @param mixed $auths 
     * @param BaseController|null $ctrl 
     * @return array<string|int, mixed> 
     */
    public static function Map($auths, ?BaseController $ctrl = null)
    {
        $ctrl = $ctrl ?? igk_current_ctrl();
        if (!is_array($auths)) {
            $auths = [$auths];
        }
        return array_filter(array_map(function ($u) use ($ctrl) {
            if (empty($u)) {
                return null;
            }
            if (strpos($u, '@') !== false) {
                return $u;
            }
            return $ctrl::authName($u);
        }, $auths));
    }

    /**
     * get full named autorization list
     * @param BaseController $ctrl 
     * @param array $auths 
     * @param null|string $prefix 
     * @return array 
     */
    public static function AuthorizationList(BaseController $ctrl, array $auths, ?string $prefix = null): array
    {
        return array_merge(...array_map(function ($i, $k) use ($ctrl, $prefix): array {
            return [StringUtility::AutoPrefix($i, $prefix)=>$ctrl::authName($i)];
        }, $auths, array_keys($auths)));
    }
}
