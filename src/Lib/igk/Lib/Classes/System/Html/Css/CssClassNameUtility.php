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
    public static function ReadBrank(string $value, $begin = '[', $end = ']')
    {
        $regex = new RegexMatcherContainer;
        $pos = 0;
        list($begin, $end) = RegexMatcherUtility::EscapeCharList([$begin, $end]);
        // define
        $g = $regex->begin($begin, $end, 'brank')->last();
        $g->patterns = [$g];
        $src = $value;

        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if ($e->tokenID == 'brank') {
                    return substr($e->value, 1, -1);
                }
            }
        }
        return null;
    }
    /**
     * just read class name from css selector.
     * @param string $n 
     * @return ?string 
     */
    public static function ReadClassName(string $n, int &$pos = 0): ?string
    {
        $sb = '';
        $regex = new RegexMatcherContainer;
        $src = $n;
        // define 
        $regex->match("((?:\\.)[a-zA-Z_][a-zA-Z0-9\-_]*|\\*)", 'selector');
        $regex->match("\\\\.", 'escaped');
        $regex->match('(?<![:\w])(:)?:([a-zA-Z-]+)(?![\w(])', 'rn-pseudo-selector');
        $regex->match('~|\+|>|,|:|\\s+|\)|\(', 'split');
        $regex->appendStringDetection('string', true);

        $fc_return_selector = function($e, $offset, $src){
            $s = substr($src, $offset, $e->from -$offset);
            $pos = $e->from;
            return $s;
        };
        $offset = $pos;
        $selector = false;
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                igk_is_debug() && Logger::info('[cssclassutility] - id : ' . $id . ', value: [' . $e->value.']');
                if ($id == 'split') {
                    if ($selector) {
                        $r = trim(substr($src, $offset, $e->from - $offset));
                        if (!empty($r)) {
                            $sb = $r;
                            return $sb;
                        }
                    }
                    if (!$selector) {
                        $offset = $e->to;
                        continue;
                    }
                    break;
                }
                if ('selector' == $id) {
                    if ($selector){
                        $pos = $e->from;
                        return $fc_return_selector($e, $offset, $src);
                    }
                    if ($e->value=='*'){
                        return $e->value;
                    }
              
                    $selector = true;
                    $offset = $e->from;
                }
                if ($id == 'rn-pseudo-selector'){
                    if ($selector){
                        $pos = $e->from;
                        return $fc_return_selector($e, $offset, $src);
                    }
                    return $e->value;
                }
            }
        }
        if ($selector) {
            $sb = substr($src, $offset);
            if ($sb == '.drop)'){
                igk_die("kjd : ".$n);
            }
            return $sb;
        }
        return null;
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
        $regex->match(":(:)?[a-zA-Z_][a-zA-Z0-9_\-]*\\b" . $v_opacity, "class-selector");
        $regex->match("(\.)[a-zA-Z_][a-zA-Z0-9_\-]*\\b" . $v_opacity, "class-selector");
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
                if ($id == 'class-selector') {
                    if ($mark) {
                        $s = trim(substr($src, $offset, $e->from - $offset));
                        if (!empty($s)) {
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
        if (!empty($s) && (in_array($s[0], ['*', ':', '.']))) {
            $_t[] = $s;
        }
        return $_t;
    }
}
