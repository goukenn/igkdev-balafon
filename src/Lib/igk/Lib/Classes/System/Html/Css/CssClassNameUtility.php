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
        // pure function of ($n, $pos): memoize, the tokenizer below is expensive
        $key = $pos . ':' . $n;
        if (isset(self::$sm_readClassNameCache[$key])) {
            list($r, $pos) = self::$sm_readClassNameCache[$key];
            return $r;
        }
        $r = self::_ReadClassName($n, $pos);
        if (count(self::$sm_readClassNameCache) > 50000) {
            self::$sm_readClassNameCache = [];
        }
        self::$sm_readClassNameCache[$key] = [$r, $pos];
        return $r;
    }
    /**
     * fast path for plain selector (letters, digits, '_', '-', '.', spaces) : extract class names with a single regex
     * instead of the char by char matcher.
     * @param string $def
     * @return ?string[] null if the selector is not eligible
     */
    public static function FastSimpleClassList(string $def, bool $single = false): ?array
    {
        $id = '\\.[a-zA-Z_][a-zA-Z0-9_]*(?:-+[a-zA-Z0-9_]+)*';
        if ($single) {
            // comma separated list of compound class selectors
            $ok = preg_match('/^\s*(?:' . $id . ')+(?:\s*,\s*(?:' . $id . ')+)*\s*$/', $def);
        } else {
            // class names, descendant and child/sibling combinators
            $ok = preg_match('/^[a-zA-Z0-9_.\s,>+~-]+$/', $def)
                && !preg_match('/-(?:[\s.,>+~]|$)|\.[^a-zA-Z_]/', $def);
        }
        if (!$ok) {
            return null;
        }
        preg_match_all('/\.[a-zA-Z_][a-zA-Z0-9_-]*/', $def, $m);
        return $m[0];
    }
    /**
     * find the end offset (exclusive) of a [...] or (...) group starting at $i, nested groups and quoted strings are skipped
     * @return int|false false when unbalanced or when an escape is found
     */
    private static function _SkipGroup(string $def, int $i, int $len)
    {
        $open = $def[$i];
        $close = $open == '[' ? ']' : ')';
        $depth = 0;
        for (; $i < $len; $i++) {
            $c = $def[$i];
            if ($c == '\\') {
                return false;
            }
            if ($c == '"' || $c == "'") {
                $j = strpos($def, $c, $i + 1);
                if ($j === false) {
                    return false;
                }
                $i = $j;
            } else if ($c == $open) {
                $depth++;
            } else if ($c == $close) {
                if (--$depth == 0) {
                    return $i + 1;
                }
            }
        }
        return false;
    }
    /**
     * native port of the matcher based ReadAllDetectedClassesFromSourceDefinition for selectors that only
     * contain class names, tags, pseudo, plain combinators and [..] / (..) groups.
     * @param string $def
     * @return ?string[] null if the selector is not eligible
     */
    public static function FastReadAllClasses(string $def): ?array
    {
        $len = strlen($def);
        if ($len == 0 || strpbrk($def, '\\') !== false) {
            return null;
        }
        $_t = [];
        $offset = 0;
        $mark = null;
        $i = 0;
        while ($i < $len) {
            $c = $def[$i];
            $from = $i;
            if ($c == '.' || $c == ':') {
                if (!preg_match('/\G(?:\.|::?)[a-zA-Z_][a-zA-Z0-9_-]*/', $def, $m, 0, $i) || substr($m[0], -1) == '-') {
                    return null;
                }
                if ($mark) {
                    $s = trim(substr($def, $offset, $from - $offset));
                    if ($s !== '' && $s !== '0') {
                        $_t[] = $s;
                    }
                }
                $offset = $from;
                $mark = true;
                $i += strlen($m[0]);
                continue;
            }
            if ($c == '[' || $c == '(') {
                $to = self::_SkipGroup($def, $i, $len);
                if ($to === false) {
                    return null;
                }
                $to_split = true;
            } else if (preg_match('/\G(?:\s+|,|\+|>|~)/', $def, $m, 0, $i)) {
                $to = $i + strlen($m[0]);
                $to_split = true;
            } else if (preg_match('/\G[a-zA-Z0-9_*-]/', $def, $m, 0, $i)) {
                $i++;
                continue;
            } else {
                return null;
            }
            $s = trim(substr($def, $offset, $from - $offset));
            $offset = $to;
            if ($s !== '' && $s !== '0') {
                $_t[] = $s;
            }
            $mark = false;
            $i = $to;
        }
        $s = substr($def, $offset);
        if ($s !== '' && $s !== '0' && in_array($s[0], ['*', ':', '.'])) {
            $_t[] = $s;
        }
        return $_t;
    }
    /**
     * native port of the loop `while ($g = ReadClassName($n, $pos))` : every class name (in order, duplicates kept)
     * @param string $n
     * @return ?string[] null if the selector is not eligible (escape, unbalanced string)
     */
    public static function FastReadClassNames(string $n): ?array
    {
        $len = strlen($n);
        if (strpbrk($n, '\\') !== false) {
            return null;
        }
        $r = [];
        $offset = 0; // start of the current scan
        $sel = false;
        $i = 0;
        $to = null;
        $val = null;
        while ($i < $len) {
            $c = $n[$i];
            $from = $i;
            $id = null;
            if ($c == '.') {
                if (preg_match('/\G\.[a-zA-Z_][a-zA-Z0-9_-]*/', $n, $m, 0, $i)) {
                    $id = 'selector';
                    $to = $i + strlen($m[0]);
                    $val = $m[0];
                }
            } else if ($c == '*') {
                $id = 'selector';
                $to = $i + 1;
                $val = '*';
            } else if ($c == '"' || $c == "'") {
                $j = strpos($n, $c, $i + 1);
                if ($j === false) {
                    return null;
                }
                $i = $j + 1;
                continue;
            } else if ($c == ':' && preg_match('/\G(?<![:\w])(:)?:([a-zA-Z-]+)(?![\w(])/', $n, $m, 0, $i)) {
                $id = 'rn-pseudo-selector';
                $to = $i + strlen($m[0]);
                $val = $m[0];
            } else if (strpos('~+>,:)(', $c) !== false) {
                $id = 'split';
                $to = $i + 1;
            } else if (ctype_space($c)) {
                $id = 'split';
                $to = $i + 1;
                while ($to < $len && ctype_space($n[$to])) {
                    $to++;
                }
            }
            if ($id === null) {
                $i++;
                continue;
            }
            if ($id == 'split') {
                if ($sel) {
                    $x = trim(substr($n, $offset, $from - $offset));
                    $sel = false;
                    if ($x !== '' && $x !== '0') {
                        $r[] = $x;
                        $offset = $to;
                        $i = $to;
                        continue;
                    }
                    // empty : the loop ends and returns the remaining part
                    $x = substr($n, $offset);
                    if ($x !== '' && $x !== '0') {
                        $r[] = $x;
                    }
                    return $r;
                }
                $offset = $to;
            } else if ($id == 'selector') {
                if ($sel) {
                    $x = substr($n, $offset, $from - $offset);
                    $r[] = $x;
                    // the next call restarts on this selector
                    $offset = $from;
                    $i = $to;
                    continue;
                }
                if ($val == '*') {
                    $r[] = '*';
                    $offset = $to;
                    $i = $to;
                    continue;
                }
                $sel = true;
                $offset = $from;
            } else { // rn-pseudo-selector
                if ($sel) {
                    $r[] = substr($n, $offset, $from - $offset);
                    $sel = false;
                    // the next call restarts on the pseudo with no selector : it is returned as value
                    $r[] = $val;
                    $offset = $to;
                    $i = $to;
                    continue;
                }
                $r[] = $val;
                $offset = $to;
            }
            $i = $to;
        }
        if ($sel) {
            $x = substr($n, $offset);
            $r[] = $x;
        }
        return $r;
    }
    private static $sm_readClassNameCache = [];
    private static $sm_readAllCache = [];
    private static $sm_classNameMatcher;
    private static function _GetClassNameMatcher(): RegexMatcherContainer
    {
        if ($regex = self::$sm_classNameMatcher) {
            $regex->resetTreatment();
            return $regex;
        }
        $regex = new RegexMatcherContainer;
        $regex->match("((?:\\.)[a-zA-Z_][a-zA-Z0-9\-_]*|\\*)", 'selector');
        $regex->match("\\\\.", 'escaped');
        $regex->match('(?<![:\w])(:)?:([a-zA-Z-]+)(?![\w(])', 'rn-pseudo-selector');
        $regex->match('~|\+|>|,|:|\\s+|\)|\(', 'split');
        $regex->appendStringDetection('string', true);
        return self::$sm_classNameMatcher = $regex;
    }
    private static function _ReadClassName(string $n, int &$pos = 0): ?string
    {
        $sb = '';
        $regex = self::_GetClassNameMatcher();
        $src = $n;
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
        if (isset(self::$sm_readAllCache[$def])) {
            return self::$sm_readAllCache[$def];
        }
        if (count(self::$sm_readAllCache) > 50000) {
            self::$sm_readAllCache = [];
        }
        $r = self::FastReadAllClasses($def) ?? self::_ReadAllDetectedClassesFromSourceDefinition($def);
        return self::$sm_readAllCache[$def] = $r;
    }
    private static $sm_allClassMatcher;
    private static function _GetAllClassMatcher(): RegexMatcherContainer
    {
        if ($regex = self::$sm_allClassMatcher) {
            $regex->resetTreatment();
            return $regex;
        }
        $v_opacity = '(?P<opacity>\\\\\/\\d+)?';
        $regex = new RegexMatcherContainer;
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
        return self::$sm_allClassMatcher = $regex;
    }
    private static function _ReadAllDetectedClassesFromSourceDefinition(string $def)
    {
        $_t = [];
        $src = $def;
        $regex = self::_GetAllClassMatcher();
        $pos = 0;
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
