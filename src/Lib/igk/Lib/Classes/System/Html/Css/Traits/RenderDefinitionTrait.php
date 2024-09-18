<?php
// @author: C.A.D. BONDJE DOUE
// @file: RenderDefinition.php
// @date: 20221202 12:04:24
namespace IGK\System\Html\Css\Traits;

use Error;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\StringBuilder;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Css\Traits
 */
trait RenderDefinitionTrait
{
    /**
     * 
     * @param mixed $def 
     * @param ?ICssRenderOption $option      
     * @return string 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function RenderDefinition($def, $option = null)
    {
        $sb = new StringBuilder;
        $lf = $option ? igk_getv($option, 'lf') : "\n";
        foreach ($def as $k => $v) {
            if (is_array($v)) {
                $sb->append($k . "{" . $lf);
                foreach ($v as $l => $m) {
                    $sb->append(sprintf("%s:%s;%s", $l, $m, $lf));
                }
                $sb->append("}" . $lf);
            } else {
                igk_ilog("bad > " . __METHOD__);
            }
        }
        return $sb . '';
    }
}
