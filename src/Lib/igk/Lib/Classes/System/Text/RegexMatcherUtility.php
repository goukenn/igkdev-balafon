<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherUtility.php
// @date: 20241031 17:45:12
namespace IGK\System\Text;
use Closure;
use IGKException;
use Exception;
/**
 * regex utility method
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
abstract class RegexMatcherUtility
{

    /**
    * Constant: regex option.
    * @var mixed
    */
    const REGEX_OPTION = RegexMatcherContainer::REGEX_OPTION;

    /**
    * Constant: regex movement capture.
    * @var mixed
    */
    const REGEX_MOVEMENT_CAPTURE  = "/(|)?\(\?(=|<|!).+?[^\\\]\)(|)?/";

    /**
    * Constant: regex empty line.
    * @var mixed
    */
    const REGEX_EMPTY_LINE = '^\\h*(?=\\n)';

    /**
    * Constant: regex capture replace.
    * @var mixed
    */
    const REGEX_CAPTURE_REPLACE = "/^\\s*(.+)\\s*$/";

    /**
    * Escape char list.
    * @param array $list
    */
    public static function EscapeCharList(array $list){
        $t = str_split('.)(*+[]/', 1);
        return array_map(function($a)use ($t){
                return in_array($a , $t) ? '\\'.$a: $a;
        }, $list);
    }

    /**
    * auto generate doc.
    * @param mixed $replaceCapturedDataCallback
    * @return void
    */

    public static function ReplaceWith(string $source, $replacement, $pattern, $g , ?Closure $replaceCapturedDataCallback = null){
            if ($replacement instanceof Closure) {
                $source = $replacement($source, $g, $pattern);
            } else {
                if (is_array($replacement)){
                    $source = strtr($source, $replacement);
                } else {
                    if ($replaceCapturedDataCallback){
                        // return $replaceCapturedDataCallback($s, $e, $rp); // update e with g
                        return $replaceCapturedDataCallback($source, $pattern, $replacement); // update e with g
                    }
                }
            }
            return $source;
    }

    /**
    * auto generate doc.
    */
    public static function ReplaceWithOnly(string $s, $rp, $e){
        $g = '/^(.+)$/m';
        if ($rp instanceof Closure){
            $s = $rp($s, $g, $e);
        } else if (is_array($rp)){
            if (is_array($rp)){
                $s = strtr($s, $rp);
            } 
        }
        return $s;
    }
    /**
     * remove movement capture
     * @param string $regex 
     * @return string|string[]|null 
     */

    public static function RemoveMovementCapture(string $regex)
    {
        $src = $regex;
        $regex = new RegexMatcherContainer;
        $cbranck = $regex->begin('(\|)?\(\?((<|!)?=)', '\)(\|)?', 'c-branket')->last();
        $sbranck = $regex->createPattern([
            "begin" => '\(',
            "end" => '\)',
            "tokenID" => 'c-simple-brank'
        ]);
        $sbranck->patterns = [
            $regex->createPattern(["match" => "\\\\."]),
            $sbranck,
        ];
        $cbranck->patterns = [
            $regex->createPattern(["match" => "\\\\."]),
            $sbranck,
            $cbranck,
        ];
        $pos = 0;
        // define
        $sb = '';
        $toffset = 0;
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if (!$e->parentInfo) {
                    $sb .= substr($src, $toffset, $e->from - $toffset);
                    $toffset = $e->to;
                }
            }
        }
        $sb .= substr($src, $toffset);
        $sb = RegexMatcherUtility::RemoveEmptyGroup($sb);
        return $sb;
        // while (false !== ($i = strpos($regex, '(?'))) {
        //     $f = igk_str_read_brank($regex, $i, ')', '(', null, 1);
        //     $regex = rtrim(substr($regex, 0, $i - strlen($f) + 1), '|') . ltrim(substr($regex, $i + 1), '|');
        // }
        // return $regex;
    }
    /**
     * retrieve capture to treat
     * @param mixed $info 
     * @return mixed 
     * @throws Exception 
     */

    public static function GetEndCaptures($info)
    {
        list($endCaptures, $captures) = igk_extract($info->match, 'beginCaptures|endCaptures|captures');
        $_ecap = $endCaptures ?? $captures;
        return $_ecap;
    }
    /**
     * convert to regex pattern
     * @param string $match 
     * @return string 
     * @throws Exception 
     * @throws IGKException 
     */

    public static function ConverToRegex(string $match): string
    {
        $o = '';
        $b = $match;
        if (preg_match(self::REGEX_OPTION, $b, $tab)) {
            $a = $tab['add'];
            $x = false;
            if ($a) {
                if (strpos($a, 'i') !== false) {
                    $o .= 'i';
                }
                if (strpos($a, 'm') !== false) {
                    $o .= 'm';
                }
                if (strpos('x', $a) !== false) {
                    $x = true;
                }
            }
            $a = igk_getv($tab, 'remove');
            if ($a) {
                if (strpos('i', $a) !== false) {
                    $o .= str_replace('i', '', $o);
                }
                if (strpos('m', $a) !== false) {
                    $o .= str_replace('m', '', $o);
                }
                if (strpos('x', $a) !== false) {
                    $x = false;
                }
            }
            // TODO : handle extra data
            $b = substr($b, strlen($tab[0]));
            if ($x) {
                $b = RegexMatcherUtility::TreatExtended($b);
            }
        }
        $b = sprintf("/%s/%s", $b, $o);
        return $b;
    }

    /**
    * auto generate doc.
    * @param string $haystack the string to operate
    */

    public static function TreatByRemoveRootScopePattern(RegexMatcherContainer $ctn, string $haystack)
    {
        $ch = '';
        $npos = 0;
        $def = [];
        $ctn->treat($haystack, function ($g, $next_pos, $data) use (&$npos, &$ch, &$def) {
            if ($g->parentInfo == null) {
                RegexMatcherUtility::Skip($g, $next_pos, $data, $npos, $ch);
                $def[] = $g->value;
            }
        });
        $ch .= substr($haystack, $npos);
        $sb = $ch;
        return [$sb, $def];
    }
    /**
     * create a parameter reference
     * @param string $begin 
     * @param string $end 
     * @return RegexMatcherContainer 
     * @throws IGKException 
     * @throws Exception 
     */

    public static function ParameterReference($begin = '\(', $end = '\)')
    {
        $ctn = new RegexMatcherContainer;
        $param_block = $ctn->begin($begin, $end, 'parameter')->last();
        $param_block->patterns = [
            $param_block
        ];
        return $ctn;
    }

    /**
    * Extracts First.
    * @param string $match
    * @param RegexMatcherContainer $ref
    * @param mixed & $pos
    */
    public static function ExtractFirst(string $match, RegexMatcherContainer $ref, &$pos)
    {
        $v = '';
        $ref->treat($match, function ($g, $next_pos) use (&$v, &$pos) {
            if (!$g->parentInfo) {
                $v = $g->value;
                $pos = $next_pos;
                return true;
            }
        });
        return $v;
    }

    /**
    * auto generate doc.
    * @return RegexMatcherContainer
    */

    public static function CodeCommentMatcherReference()
    {
        $ctn = new RegexMatcherContainer;
        $ctn->match('\/\/.+', 'single-line');
        $ctn->begin('\/\*', '\*\/', 'multiline')->last();
        return $ctn;
    }
    /**
     * remove comment 
     * @param string $match 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     */

    public static function RemoveComment(string $match)
    {
        $cnf = self::CodeCommentMatcherReference();
        $v = '';
        $pos = 0;
        $cnf->treat($match, function ($g, $next_pos, $data) use (&$v, &$pos) {
            $v .= rtrim(substr($data, $pos, $g->from - $pos));
            $pos = $next_pos;
        });
        $v .= substr($match, $pos);
        return $v;
    }
    /**
     * skip value 
     * @param mixed $g 
     * @param mixed $next_pos 
     * @param mixed $data 
     * @param mixed &$pos 
     * @param mixed &$ch 
     * @return void 
     */

    public static function Skip($g, $next_pos, $data, &$pos, &$ch)
    {
        $ch .= rtrim(substr($data, $pos, $g->from - $pos));
        $pos = $next_pos;
    }
    /**
     * treat begin end capture
     * @param string $source 
     * @param null|string $begin 
     * @param null|string $end 
     * @param int $startLength 
     * @param null|int $endPos 
     * @return string 
     */

    public static function TreatBeginEndCapture(string $source, ?string $begin, ?string $end, ?int $startLength = 0, ?int $endPos = null): string
    {
        $endPos = $endPos ?? strlen($source);
        $startLength = $startLength ?? strlen($source);
        $offset = 0;
        $n = '';
        $n = $begin . substr($source, $startLength); // because of offset 
        $offset = $startLength - strlen($begin);
        $n = substr($n, 0, abs($endPos - $offset)) . $end;
        return $n;
    }
    /**
     * treat extended 
     * @param string $c 
     * @return string 
     */

    public static function TreatExtended(string $c): string
    {
        $ctn = new RegexMatcherContainer;
        $l = $ctn->begin('\(\?#', '\)')->last();
        $l->patterns = [
            $l
        ];
        $tr = igk_getv(RegexMatcherUtility::TreatByRemoveRootScopePattern($ctn, $c), 0);
        // remove white space 
        $tr = str_replace(" ", '', $tr);
        $l = explode("\n", $tr);
        $tload = [];
        array_map(function ($i) use (&$tload) {
            if (preg_match("/^#/", $i)) return; // skip comment
            $i = preg_replace("/^\s+\|\s+/", "|", $i);
            $tload[] = $i;
        }, $l);
        return trim(implode('', $tload));
    }
    /**
     * match type inclusion
     * @param mixed $k 
     * @return 'include'|'match'|'begin/while'|'begin/end' 
     * @throws Exception 
     */

    public static function GetPatternType($k)
    {
        list($match, $include, $begin, $while) = igk_extract($k, 'match|include|begin|while|end');
        $_t = RegexMatcherContainer::MATCH_TYPE;
        if ($include) {
            $_t = RegexMatcherContainer::INCLUDE;
        } else if ($match) {
            $_t = RegexMatcherContainer::MATCH_TYPE;
        } else if ($begin && $while) {
            $_t = RegexMatcherContainer::BEGIN_WHILE_TYPE;
        } else if ($begin) {
            $_t = RegexMatcherContainer::BEGIN_END_TYPE;
        }
        return $_t;
    }

    /**
    * auto generate doc.
    * @param array &$patterns
    * @return void
    */

    public static function AppendPhpHereDoc($regex, &$patterns = [])
    {
        $patterns[] = $regex->begin('(<<<)([a-zA-Z][a-zA-Z\-0-9]*)', "^\\2", 'here-doc')->last();
        $patterns[] = $regex->begin('(<<<)\'([a-zA-Z][a-zA-Z\-0-9]*)\'', "^\\2", 'here-doc')->last();
        $patterns[] = $regex->begin('(<<<)"([a-zA-Z][a-zA-Z\-0-9]*)"', "^\\2", 'here-doc')->last();
    }

    /**
    * auto generate doc.
    * @param IRegexMatcherEndDetectionInfo $e
    * @return array
    */

    public static function GetChainUntil(&$v_plc, $e)
    {
        $chain = [];
        while (($tc = count($v_plc)) > 0) {
            if ($v_plc[$tc - 1][0]->from <= $e->from) {
                break;
            }
            $r = array_pop($v_plc);
            array_unshift($chain, $r);
        }
        return $chain;
    }
    /**
     * remove empty captured group ()
     * @param string $regex 
     * @return string 
     */

    public static function RemoveEmptyGroup(string $regex)
    {
        $sb = $regex;
        $toff = 0;
        while (false !== ($cpos = strpos($sb, '()', $toff))) {
            if (($cpos > 0) && ($sb[$cpos - 1] == '\\')) {
                $toff = $cpos + 2;
            } else {
                $sb = substr($sb, 0, $cpos) . substr($sb, $cpos + 2);
            }
        }
        return $sb;
    }
}