<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssPhpDetector.php
// @date: 20260830 15:04:26
namespace IGK\System\Html\Css;

use IGK\System\Text\RegexMatcherContainer;
use IGK\Core\Components\ComponentInfo;
use IGK\System\Html\Css\Compiler\CssThemeBuilder;
use IGK\System\Html\Dom\HtmlDocTheme;

/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
/**
 * auto generate doc.
 * @package IGK\System\Html\Css
 */
class CssPhpDetector
{
    /**
     * auto generate doc.
     * @var mixed
     */
    private $m_regex;
    /**
     * auto generate doc.
     * @var mixed
     */
    private $m_fcall;
    /**
     * auto generate doc.
     * @var mixed
     */
    private $m_tsrc;
    /**
     * auto generate doc.
     * @var mixed
     * @return void
     */
    private $m_keys = [];

    /**
     * list of classe definition every '.'.name info  
     * @var mixed
     */
    public $list;
    /**
     * auto generate doc.
     * @var mixed
     * @return void
     */
    var $autoReferenceRegex = HtmlDocTheme::AUTO_CSS_PREFIX_REGEX;

    // /**
    //  * append information to list 
    //  * @param string $key 
    //  * @param mixed $info 
    //  * @return void 
    //  */
    // public function append(string $key, $info){
    //     foreach(explode('.', $key) as $t){
    //         $this->list['.'.$t] = $info;
    //     }
    // }
    /**
     * 
     * @param CssPhpDetector $detector 
     * @param array &$list 
     * @return void 
     */
    public static function Attach($detector, & $list){
        $detector->list = & $list;
    }
    /**
     * .ctr
     * @return void
     */
    public function __construct() {}
    /**
     * auto generate doc.
     * @return ?RegexMatcherContainer
     */
    protected function _regex()
    {
        if ($this->m_regex)
            return $this->m_regex;
        $def_theme_regex = implode('', [
            '(?:(?:dark|light):)?',
            '(?:(?:x?sm|(?:x(?:x))?lg)-)?'
        ]);
        $v_opacity = '(?P<opacity>(\\\\)?\\/\\d+)?';
        $regex = new RegexMatcherContainer;
        // define
        $regex->appendSingleLineComment();
        $regex->appendMultilineComment();
        $regex->match('\\$[_a-zA-Z]+[_a-zA-Z0-9]*', 'php-var');

        $regex->match("(->)?\\b([a-z]+(-[a-z\\-0-9]+)?)\\b", "word-outside-detected")->last();
        $regex->autoStore = false;
        $uri = $regex->match("\\b[a-zA-Z_]+[a-zA-Z0-9]*(\/[a-zA-Z_]+[a-zA-Z0-9]*)+", "uri-skip")->last();

        $word_detect_class = $regex->match("(\\.)?" . CssUtils::CLASS_NAME_UTIL_REGEX . '(?:-[a-zA-Z0-9])*'.$v_opacity, "word-detected")->last();
        $word_detect_mark = $regex->begin(
            "(\\.)?" . CssUtils::CLASS_NAME_UTIL_REGEX . '(?:-[a-zA-Z0-9])*(?:-\[)',
            "\](?P<pseudo>(:[a-zA-Z0-9][a-zA-Z0-9\-_]*)*)?".$v_opacity,
            "word-detected"
        )->last();
        $eword_detect_mark = $regex->begin(
            "(\\.)?" . CssUtils::CLASS_NAME_UTIL_REGEX . '(?:-[a-zA-Z0-9])*(?:-\\\\\[)',
            "\\\\\](?P<pseudo>(:[a-zA-Z0-9][a-zA-Z0-9\-_]*)*)?".$v_opacity,
            "word-detected"
        )->last();
        $word = $regex->match($def_theme_regex . "\\b[a-z]+(-[a-z\\-0-9]+)?\\b", "word-detected")->last();
        $in_str = $regex->appendStringDetection('string', true)->last();

        $regex->autoStore = true;
        $str = $regex->appendStringDetection('string', true)->last();
        $str->patterns[] = $regex->createPattern([
            'begin' => "\{\\s*(sys)\\s*:\\s*",
            'end' => "\}",
            'tokenID' => 'skip-bcss-replacement'
        ]);
        // $str->patterns[] = $regex->createPattern([
        //     'begin' => "\(",
        //     'end' => "\)",
        //     'tokenID' => 'skip-bcss-replacement'
        // ]);
        $str->patterns[] = $uri;
        $str->patterns[] = $word_detect_mark;
        $str->patterns[] = $eword_detect_mark;
        $str->patterns[] = $word_detect_class;
        $str->patterns[] = $word;

        $eword_detect_mark->patterns = $word_detect_mark->patterns = [
            $in_str
        ];

        $this->m_regex = $regex;
        return $this->m_regex;
    }
    /**
     * auto generate doc.
     * @return void
     */
    protected function &_types()
    {
        if (is_null($this->m_tsrc)) {

            //$b = array_fill_keys(igk_sys_get_html_components(), 0) ?? igk_die('missing components');
            $this->m_tsrc = array_fill_keys(array_map(function ($g) {
                return ltrim($g, '.');
            }, array_keys($this->list ?? [])), 0);
        }

        return $this->m_tsrc;
    }
    /**
     * auto generate doc.
     * @return void
     */
    protected function &_fcall()
    {
        if (is_null($this->m_fcall)) {
            $tsrc = &$this->_types();
            $tab = ComponentInfo::ListComponentInfo();
            $func_call = [];
            foreach ($tab as $n => $c) {
                if ($cl = $c->class) {
                    $ctab = explode(" ", $cl);
                    while (count($ctab) > 0) {
                        $q = array_shift($ctab);
                        $tsrc[$q] = 0;
                        $func_call[$n][$q] = 0;
                    }
                }
            }
            $this->m_fcall = $func_call;
        }
        return $this->m_fcall;
    }
    /**
     * from source - detect expression
     * @param string $source
     * @return ?array
     */
    public function resolve(string $source)
    {
        $regex = $this->_regex();
        $tsrc = &$this->_types();
        $func_call = &$this->_fcall();
        $keys = &$this->m_keys;
        $src = $source;
        $pos = 0;

        $detected = false;
        $v_is_debug = igk_is_debug();
        $regex->resetTreatment();

        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {

                $id = $e->tokenID;
                $v = $e->value;
                $v_is_debug &&  \IGK\System\Console\Logger::info('[css-detector] - ' . $id . ': detected :' . $v);
                if ($id == 'word-outside-detected') {
                    // + | possibility function call call outside 
                    $tv = $e->captures[2][0];
                    if (($v_rg = igk_getv($func_call, $tv))) {
                        foreach (array_keys($v_rg) as $s) {
                            if (!isset($tsrc[$s])) {
                                $tsrc[$s] = 0;
                            }
                            $tsrc[$s]++;
                            $func_call[$tv][$s]++;
                        }
                        $detected = true;
                    }
                } else {
                    if ($id == 'word-detected') {
                        if ($v[0] == '.') {
                            $v = substr($v, 1);
                        }
                        // igk_wln(__FILE__.":".__LINE__ , 'detect: ' . $id . ' : '.$v);
                        $v = self::_ToCssClassDeclaration($v);

                        if (isset($tsrc[$v])) {
                            $tsrc[$v]++;
                            $detected = true;
                        } else {
                            if ($reg = $this->autoReferenceRegex) {
                                $tc = stripslashes($v);
                                if (preg_match($reg, $tc)) {
                                    $tsrc[$v] = 1;
                                    $detected = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($detected) {
            $keys = array_merge($keys, array_filter($tsrc, function ($a) {
                return $a !== 0;
            }));
            return $keys;
        }
    }
    /**
     * the reflection
     * @param string $v 
     * @return string 
     */
    protected static function _ToCssClassDeclaration(string $v)
    {
        $v = stripslashes($v);
        return CssUtils::ToClassClassDeclaration($v);
    }
}
