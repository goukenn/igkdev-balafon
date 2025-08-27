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
  
   

}
