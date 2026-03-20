<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpScriptUtility.php
// @date: 20260320 14:22:13
namespace IGK\System\Php\Helper;

use IGK\Helper\Activator;

/**
 * 
 * @package IGK\System\Php\Helper
 * @author C.A.D. BONDJE DOUE
 */
abstract class PhpScriptUtility
{
    /**
     * remove global functtion 
     * @param string $src 
     * @return ?string 
     */
    public static function RemoveGlobalFunc(string $src, $options=null): ?string
    {
        $f = Activator::CreateNewInstance(PhpRemoveGlobaFunc::class, $options ?? []);
        return $f->remove($src);
    }
}
