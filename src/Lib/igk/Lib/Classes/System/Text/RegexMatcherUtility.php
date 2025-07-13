<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherUtility.php
// @date: 20241031 17:45:12
namespace IGK\System\Text;
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
     * 
     * @param RegexMatcherContainer $ctn the container
     * @param string $haystack the string to operate 
     * @return (string|array)[] 
     * @throws Exception 
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
    public static function GetPatternType($k){
        list($match, $include, $begin, $while) = igk_extract($k, 'match|include|begin|while|end');
        $_t = RegexMatcherContainer::MATCH_TYPE;
        if ($include){
            $_t = RegexMatcherContainer::INCLUDE;
        }
        else if ($match) {
            $_t = RegexMatcherContainer::MATCH_TYPE; 
        } else if ($begin && $while) {
            $_t = RegexMatcherContainer::BEGIN_WHILE_TYPE;  
        } else if ($begin) {
            $_t = RegexMatcherContainer::BEGIN_END_TYPE;   
        }
        return $_t;
    }
    /**
     * 
     * @param mixed $regex 
     * @param array &$patterns 
     * @return void 
     */
    public static function AppendPhpHereDoc($regex, & $patterns = []){
        $patterns[] = $regex->begin('<<<([a-zA-Z][a-zA-Z\-0-9]*)',"^\\1" ,'here-doc')->last();
        $patterns[] = $regex->begin('<<<\'([a-zA-Z][a-zA-Z\-0-9]*)\'',"^\\1" ,'here-doc')->last();
        $patterns[] = $regex->begin('<<<"([a-zA-Z][a-zA-Z\-0-9]*)"',"^\\1" ,'here-doc')->last();
    }
    /**
     * 
     * @param mixed &$v_plc 
     * @param IRegexMatcherEndDetectionInfo $e 
     * @return array 
     */
    public static function GetChainUntil(& $v_plc, $e){
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
}