<?php
// @author: C.A.D. BONDJE DOUE
// @file: UriResolver.php
// @date: 20250925 12:56:28
namespace IGK\System;

use IGK\Helper\ViewHelper;
use IGK\System\IO\Path;

/**
* auto generate doc.
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
class UriResolver
{
    /**
     * helper to resolve ResourceController resource logic
     * @param string $src 
     * @param mixed $ctrl 
     * @return mixed 
     */
    public static function ResolveControllerResource(string $src, $ctrl = null)
    {
        if (igk_str_startwith($src, '@/') && ($ctrl = $ctrl ?? ViewHelper::CurrentCtrl())) {
            $u = substr($src, 2);
            if (file_exists($f = Path::Combine($ctrl->getDataDir(), $u))) {
                $src = $f; // $ctrl::asset($f);
            } else {
                $src = $ctrl::uri($src);
            }
        }
        return $src;
    }
}
