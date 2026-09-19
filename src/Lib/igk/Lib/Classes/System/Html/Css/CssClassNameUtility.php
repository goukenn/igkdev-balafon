<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameUtility.php
// @date: 20260918 09:35:50
namespace IGK\System\Html\Css;

use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;

/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
abstract class CssClassNameUtility
{
    /**
     * 
     * @param string $value 
     * @param string $begin 
     * @param string $end 
     * @return string|null 
     */
    public static function ReadBrank(string $value, $begin='[', $end=']'){
        $regex = new RegexMatcherContainer;
        $pos=0;
        list($begin, $end) = RegexMatcherUtility::EscapeCharList([$begin, $end]);
        // define
        $g = $regex->begin($begin, $end, 'brank')->last();
        $g->patterns = [$g];
        $src = $value;
        
        while($g = $regex->detect($src, $pos)){
            if ($e = $regex->end($g, $src, $pos)){
                if ($e->tokenID=='brank'){
                    return substr($e->value, 1, -1);
                }
            }
        }
        return null;
    }
    /**
     * read class name
     * @param string $n 
     * @return string 
     */
    public static function ReadClassName(string $n): string
    {
        $sb = '';
        $regex = new RegexMatcherContainer;
        $pos = 0;
        $src = $n;
        // define 
        $regex->match("\\.[a-zA-Z][a-zA-Z0-9-]*", 'selector');
        $regex->match("\\\\.", 'escaped');
        // $regex->begin('\\(', '\\)', 'c-brank');
        // $regex->begin('\\[', '\\]', 'p-brank');
        $regex->match(':|\\s+', 'split');
        $offset = 0;
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                igk_is_debug() && Logger::info('[cssclassutility] - id : ' . $id . ' value:' . $e->value);
                if ($id == 'split') {
                    $r = trim(substr($src, $offset, $e->from - $offset));
                    if (!empty($r)) {
                        $sb = $r;
                        return $sb;
                    }
                    $offset = $e->from;
                    break;
                }
            }
        }
        $sb = substr($src, $offset);
        return $sb;
    }

    /**
     * 
     * @return array 
     */
    public static function ReadAllDetectedClassesFromSourceDefinition(string $def)
    {
        $_t = [];
        $src = $def;
        $v_opacity = '(?P<opacity>\\\\\/\\d+)?';
        $regex = new RegexMatcherContainer;
        $pos = 0;
        $regex->match('\*', 'multiple');
        $str = $regex->appendStringDetection('string', true)->last();
        $regex->match("(\.)[a-zA-Z_][a-zA-Z0-9_\-]+\\b".$v_opacity, "class-selector");
        $brank = $regex->begin('\\\\\[', '\\\\]', 'escaped-brank')->last();
        $regex->match("\\\\.", 'escape');
        $pbrank = $regex->begin('\\(', '\\)', 'split')->last();
        $sbrank = $regex->begin('\\[', '\\]', 'split')->last();
        $regex->match("\\s+|,|\+|>|~", 'split');

        $brank->patterns = [
            $str
        ];
        $pbrank->patterns = [
            $str,
            $pbrank
        ];
        $sbrank->patterns = [
            $str,
            $sbrank
        ];
        // define

        $s = '';
        $offset = 0;
        $mark = null;
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                igk_is_debug() && Logger::info('[detect-selector-all-class] - ' . $id . ' v=' . $e->value);
                if ($id == 'class-selector'){
                    if ($mark){
                        $s = trim(substr($src, $offset, $e->from - $offset));
                        if (!empty($s)){ 
                            $_t[] = $s;
                        }
                        $s = '';
                    }
                    $offset = $e->from;
                    $mark = true;
                }
                if ($id == 'split') {
                    $r = trim(substr($src, $offset, $e->from - $offset));
                    $s .= $r;
                    $offset = $e->to;
                    if (!empty($s))
                        $_t[] = $s;
                    $s = '';
                    $mark = false;
                }
            }
        }
        $s .= substr($src, $offset);
        if (!empty($s) && ($s[0]=='.')) {
            $_t[] = $s;
        }
        return $_t;
    }
}
