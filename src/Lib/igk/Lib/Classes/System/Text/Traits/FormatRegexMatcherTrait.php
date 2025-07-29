<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatRegexMatcherTrait.php
// @date: 20250727 12:03:04
namespace IGK\System\Text\Traits;

use Closure;
use Exception;
use IGK\System\Text\RegexMatcherPattern;
use IGK\System\Text\RegexMatcherUtility;

/**
 * 
 * @package IGK\System\Text\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait FormatRegexMatcherTrait
{
    /**
     * formatter subpatter 
     * @param mixed $e 
     * @param string $format 
     * @param mixed &$replacement 
     * @param mixed $g 
     * @return void 
     * @throws Exception 
     */
    protected function formatSubPattern($e, string $format, &$replacement)
    {
        if (!($captures = RegexMatcherUtility::GetEndCaptures($e))) {
            if ($e->emptyLine)
                $replacement[] = [$e, '', 'remove' => true];
            else{
                $replacement[] = [$e, $e->value];
            }
            return;
        }
        ksort($captures);
        $replacement[] = [$e, function ($s, $g, $e) use ($captures, $format) {
            return self::TreatFormatCapture($s, $e, $captures, $e->captures, $format);
        }];
    }
    protected function treatCapture(string $value, $cap, string $sourceValue, int $pos)
    {
        return $value;
    }
    public static function TreatCaptureReplace(string $s, $cap, $sourceValue, $pos): ?string
    {
        if ($rp = igk_getv($cap, 'replaceWith')) {
            if ($rp instanceof Closure) {
                return $rp($s, $cap, $sourceValue, $pos);
            }
            $is_empty = strlen(trim(trim($s)))==0;
            $l = preg_replace(RegexMatcherUtility::REGEX_CAPTURE_REPLACE, $rp, $is_empty ? $s : trim($s));
            return $l;
        }
        return null;
    }
    /**
     * 
     * @param mixed $s 
     * @param mixed $e 
     * @param array $captures capture definition 
     * @param array $matches 
     * @param mixed $format 
     * @return mixed 
     * @throws Exception 
     */
    public static function TreatFormatCapture(string $s, $e, array $captures, array $matches, string $format)
    {
        $v_key = 'replaceWith';
        $root = null;
        $ts = '';
        $offset = 0;
        $from = $e->from;
        $treat = $e->match->getMatcher()->captureTreatmentListener ?? function (string $s, $cap, $sourceValue, $pos) {
            return self::TreatCaptureReplace($s, $cap, $sourceValue, $pos) ?? $s;
        };
        foreach ($captures as $k => $v) {
            if (($cap = igk_getv($matches, $k)) && ($cap[1]!=-1)) {
                if ($rpw = igk_getv($v, $v_key)){ 
                   // + update the replace with global regex matches - data
                   $tv = self::ReplaceRegexMatcherCaptureGlobal($rpw, $matches); 
                   $v[$v_key] = $tv;
               }
                if ($k == 0) {
                    $root = $v;
                    continue;
                } 
                $ts .= substr($s, $offset, $cap[1] - $offset - $from);
                $ts .= $treat($cap[0], $v, $format, $from);
                $offset = ($cap[1] - $from) + strlen($cap[0]);
            }
        }
        $ts .= substr($s, $offset);
        if ($root) {
            $ts = $treat($ts, $root, $format, $from);
        }
        return $ts;
    }

}
