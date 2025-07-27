<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatRegexMatcherTrait.php
// @date: 20250727 12:03:04
namespace IGK\System\Text\Traits;

use IGK\System\Text\RegexMatcherUtility;

/**
 * 
 * @package IGK\System\Text\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait FormatRegexMatcherTrait
{
    protected function formatSubPattern($e, string $format, &$replacement, $g=null)
    { 
       if (!($captures = RegexMatcherUtility::GetEndCaptures($e))){ 
            $replacement[] = [$e,'-|-'];
            return; 
       }
        ksort($captures); 
        $replacement[] = [$e, function ($s, $g, $e) use ($captures, $format) {
            $root = null;
            $ts = '';
            $offset = 0;
            $treat = $e->match->getMatcher()->captureTreatmentListener ?? function (string $s, $cap) {
                return $s;
            };
            foreach ($captures as $k => $v) {
                if ($k == 0) {
                    $root = $v;
                    continue;
                }
                if ($cap = igk_getv($e->captures, $k)) {
                    $ts .= substr($s, $offset, $cap[1] - $offset);
                    $ts .= $treat($cap[0], $v);
                    $offset += strlen($cap[0]);
                }
            }
            $ts .= substr($s, $offset);
            if ($root) {
                $ts = $treat($ts, $root, $format, $e->from);
            }
            return $ts;
        }];
    }
    protected function treatCapture(string $value, $cap, string $sourceValue, int $pos){
        return $value;
    }
}
