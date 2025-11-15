<?php
// @author: C.A.D. BONDJE DOUE
// @file: CookieManager.php
// @date: 20251114 08:23:36
namespace IGK\System\Core;

use IGK\Helper\StringUtility;
use PHPStan\PhpDocParser\Parser\StringUnescaper;

/**
 * 
 * @package IGK\System\Core
 * @author C.A.D. BONDJE DOUE
 */
class CookieManager
{
    /**
     * 
     * @return void 
     */
    protected function __construct()
    {
    }
    /**
     * handle start cookie manager 
     * @return void 
     */
    public static function Handle()
    {
        $c = new static;

        $keys = array_keys($_COOKIE);
        while (count($keys)) {
            $q = array_shift($keys);
            if (preg_match('/^__blf_(?P<name>.+)/', $q, $tab)) {
                $fn = StringUtility::FuncName($tab['name']);
                if (method_exists($c, $fn)) {
                    call_user_func_array([$c, $fn], [$_COOKIE[$q], $q]);
                    igk_clear_real_cookie($q);
                    unset($_COOKIE[$q]);
                }
            }
        }
    }
    /**
     * handle 
     * @param $value
     * @return void 
     */
    protected function setting_ajx_ref_uri($value)
    {
        igk_environment()->setArray('session-flag', __FUNCTION__, $value);
    }
}
