<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReplaceUtilityTrait.php
// @date: 20250730 08:33:24
namespace IGK\System\Text\Traits;

use Closure;
use IGK\System\Text\Formatters\IRegexFormatterCaptureInfo;
use IGK\System\Text\IReplaceCapturedFormatDefinition;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherPattern;
use IGK\System\Text\RegexMatcherUtility;

/**
 * 
 * @package IGK\System\Text\Traits
 * @author C.A.D. BONDJE DOUE
 */

/**
* auto generate doc.
* @package IGK\System\Text\Traits
*/
trait ReplaceUtilityTrait
{

    /**
     * resolve capture to glue for format
     * @param mixed $e 
     * @param array &$v_def 
     * @param mixed $replacement 
     * @return mixed|void 
     * @throws Exception 
     */
    public static function ResolveCapture($e, array &$v_def)
    {

        $v_type = RegexMatcherUtility::GetPatternType($e->match);
        if ($v_type == RegexMatcherPattern::BEGIN_END_TYPE) {
            $b_cap = $e->match->beginCaptures ?? $e->match->captures ?? [];
            $e_cap = $e->match->endCaptures ?? $e->match->captures ?? [];
            $v_def['begin'] = self::TreatFormatCapture($e->beginCaptures[0][0], $e, $b_cap, $e->beginCaptures, $e->beginCaptures[0][0]);
            if ($e->endCaptures)
            {
                $d = $e->endCaptures;
                $v_def['end'] = self::TreatFormatCapture($d[0][0], $e, $e_cap, $e->endCaptures, $e->endCaptures[0][0], $d[0][1]);
            }

            $v_def['begin_captures'] = $b_cap;
            $v_def['end_captures'] = $e_cap;
        } else if ($v_type == RegexMatcherPattern::BEGIN_WHILE_TYPE) {
            $b_cap = $e->match->beginWhileCaptures ?? $e->match->captures;
            $e_cap = $e->match->endWhileCaptures ?? $e->match->captures;
            $v_def['begin'] = self::TreatFormatCapture($e->beginCaptures[0][0], $e, $b_cap, $e->beginCaptures, $e->beginCaptures[0][0]);
            $v_def['end'] = self::TreatFormatCapture($e->endCaptures[0][0], $e, $e_cap, $e->endCaptures, $e->endCaptures[0][0]);

            $v_def['begin_captures'] = $b_cap;
            $v_def['end_captures'] = $e_cap;
        } else {
            if ($tcap = $e->captures) {
                $_cap = igk_array_merge_assoc($e->match->beginCaptures ?? [], $e->match->captures ?? []);
                $ls = self::TreatFormatCapture($e->value, $e, $_cap, $tcap, $e->value);
                $v_def['captures'] = $_cap;
                return $ls;
            }
        }
    }

    /**
    * auto generate doc.
    * @param Closure(string $s):string|string|null|array<string> $rp
    * @return string
    */
    static function ReplaceData(string $s, $e, $rp = null, $property = 'replaceWith')
    {

        if (!is_null($rp = $rp ?? igk_getv($e->match, $property))) {
            $v_t = RegexMatcherContainer::GetPatternType($e->match);
            $g = '/^(.+)$/m';
            if ($v_t == RegexMatcherContainer::MATCH_TYPE) {
                $g = igk_extract_first_not_null($e->match, 'replacementMatch|match');
                $g = RegexMatcherUtility::ConverToRegex($g);
                $g = RegexMatcherUtility::RemoveMovementCapture($g);
            }
            $callback = Closure::fromCallable([self::class, 'ReplaceCaptureData']) ?? igk_die('missing captured data');
            $s = RegexMatcherUtility::ReplaceWith($s, $rp, $g, $e, $callback); 
        }
        return $s;
    }

    /**
    * auto generate doc.
    * @param string $replace replace data
    * @return string
    */
    public static function ReplaceCaptureData(string $s, string $pattern, string $replace): string
    {
        if (false !== strpos($s, "\n")) {
            $pattern = '/^([\s\S]+)\s*$/m';
            return self::ReplaceGlobal($pattern, $replace, $s);
        }
        $s = preg_replace($pattern, $replace, $s);
        return $s;
    }

    /**
    * auto generate doc.
    * @param string $s
    * @return string
    */
    public static function ReplaceGlobal(string $pattern, $rp, string $s): string
    {
        if (preg_match($pattern, $s, $tab)) {
            $rp = preg_replace_callback("/(?!<=\\\\)(?:\\\\|\\$)(\\d+)/", function ($a) use ($tab) {
                if ($l = igk_getv($tab, $a[1])) {
                    return $l;
                }
            }, $rp);
        }
        return $rp;
    }

    /**
    * auto generate doc.
    * @param array $tab
    */
    public static function ReplaceRegexMatcherCaptureGlobal(string $rp, array $tab)
    {

        $rp = preg_replace_callback("/(?!=\\\\)(?:\\$(\\d+))/", function ($a) use ($tab) {
            if ($l = igk_getv($tab, $a[1])) {
                return $l[0];
            }
        }, $rp);

        return $rp;
    }

    /**
    * auto generate doc.
    * @param mixed $format
    * @return mixed
    */
    public static function TreatFormatCapture(string $s, $e, array $captures, array $matches, string $format, 
        ?int $from=null)
    {
        $v_key = 'replaceWith';
        $root = null;
        $ts = '';
        $offset = 0;
        $from = $from ?? $e->from;
        $treat = $e->match->getMatcher()->captureTreatmentListener ?? function (string $s, $cap, $sourceValue, $pos) {
            return self::TreatCaptureReplace($s, $cap, $sourceValue, $pos) ?? $s;
        };

        /**
        * auto generate doc.
        * @var IRegexFormatterCaptureInfo $v
        */
        foreach ($captures as $k => $v) {
            if (($cap = igk_getv($matches, $k)) && ($cap[1] != -1)) {
                if ($rpw = igk_getv($v, $v_key)) {
                    // + update the replace with global regex matches - data
                    if (is_string($rpw)) {
                        $tv = self::ReplaceRegexMatcherCaptureGlobal($rpw, $matches);
                        $v[$v_key] = $tv;
                    }
                }
                if ($k == 0) {
                    $root = $v;
                    continue;
                }
                $ts .= substr($s, $offset, $cap[1] - $offset - $from);
                $ts .= $treat($cap[0], $v, $format, $from, 'in');
                $offset = ($cap[1] - $from) + strlen($cap[0]);
            }
        }
        $ts .= substr($s, $offset);
        if ($root) {
            $ts = $treat($ts, $root, $format, $from, 'root');
        }
        return $ts;
    }
    public static function TreatCaptureReplace(string $s, $cap, $sourceValue, $pos, $property = 'replaceWith'): ?string
    {
        if ($rp = igk_getv($cap, $property)) {
            if ($rp instanceof Closure) {
                return $rp($s, $cap, $sourceValue, $pos);
            }
            $is_empty = strlen(trim(trim($s))) == 0;
            $l = preg_replace(RegexMatcherUtility::REGEX_CAPTURE_REPLACE, $rp, $is_empty ? $s : trim($s));
            return $l;
        }
        return null;
    }

    /**
     * update captured definition
     * @param IReplaceCapturedFormatDefinition $e 
     * @param array $v_def 
     * @param string $s 
     * @return string 
     */
    public static function UpdateCaptureDef(IReplaceCapturedFormatDefinition $e, array $v_def, string $s, ?callable $treat = null)
    {
        $lb = '';
        $le = '';
        if (isset($v_def['begin'])) {
            $v_s = $e->beginCaptures[0][0];
            if (igk_str_startwith($s, $v_s)) {
                $lb = $v_def['begin'];
                $s = igk_str_rm_start($s, $v_s, 1);
            }
        }
        if (isset($v_def['end'])) {
            $v_end = $e->endCaptures[0][0];
            if (igk_str_endwith($s, $v_end)) {
                $s = igk_str_rm_last($s, $v_end, 1);
                $le = $v_def['end'];
            }
        }
        if ($treat) {
            $s = $treat($e, $s);
        }
        $s = sprintf('%s%s%s', $lb, $s, $le);
        return $s;
    }
}
