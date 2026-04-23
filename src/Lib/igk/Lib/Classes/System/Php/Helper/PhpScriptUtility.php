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
/**
 * auto generate doc.
 * @package IGK\System\Php\Helper
 */
abstract class PhpScriptUtility
{
    /**
     * skip shebang definition 
     * @param string $source 
     * @param int $pos 
     * @return int code source offset
     */
    public static function SkipShebang(string $source, int $pos = 0): int
    {
        if (strpos(substr($source, 0, 3), '#!/') === 0) {
            if (false !== ($po = strpos($source, "\n"))) {
                $pos = $po;
            }
        }
        return $pos;
    }
    /**
     * remove global functtion 
     * @param string $src 
     * @return ?string 
     */
    public static function RemoveGlobalFunc(string $src, $options = null): ?string
    {
        $f = Activator::CreateNewInstance(PhpRemoveGlobaFunc::class, $options ?? []);
        return $f->remove($src);
    }
}