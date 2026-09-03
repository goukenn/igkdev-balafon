<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssPhpDetector.php
// @date: 20260830 15:04:26
namespace IGK\System\Html\Css;

use IGK\System\Text\RegexMatcherContainer;
use IGK\Core\Components\ComponentInfo;

/**
 * 
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssPhpDetector
{
    /**
     * 
     * @var mixed
     */
    private $m_regex;
    /**
     * 
     * @var mixed
     */
    private $m_fcall;
    /**
     * 
     * @var mixed
     */
    private $m_tsrc;
    private $m_keys = [];

    /**
     * list of classed definition 
     * @var mixed
     */
    var $list;

    var $autoReferenceRegex = '/((x)?sm|(x(x)?)?lg|dark|text|bg|no|br|mag|pad)-/';
    public function __construct() {}

    protected function _regex()
    {
        if ($this->m_regex)
            return $this->m_regex;

        $regex = new RegexMatcherContainer;
        $pos = 0;
        // define
        $regex->appendSingleLineComment();
        $regex->appendMultilineComment();
        $regex->match('\\$[_a-zA-Z]+[_a-zA-Z0-9]*', 'php-var');

        $regex->match("(->)?\\b([a-z]+(-[a-z\\-0-9]+)?)\\b", "word-outside-detected")->last();
        $regex->autoStore = false;
        $word = $regex->match("\\b[a-z]+(-[a-z\\-0-9]+)?\\b", "word-detected")->last();
        $regex->autoStore = true;
        $str = $regex->appendStringDetection('string', true)->last();
        $str->patterns[] = $word;

        $this->m_regex = $regex;
        return $this->m_regex;
    }
    protected function &_types()
    {
        if (is_null($this->m_tsrc)) {

            //$b = array_fill_keys(igk_sys_get_html_components(), 0) ?? igk_die('missing components');
            $this->m_tsrc = array_fill_keys(array_map(function ($g) {
                return ltrim($g, '.');
            }, array_keys($this->list)), 0);
        }

        return $this->m_tsrc;
    }
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
                        if (isset($tsrc[$v])) {
                            $tsrc[$v]++;
                            $detected = true;
                        } else {
                            if ($reg = $this->autoReferenceRegex){
                                if (preg_match($reg, $v)){
                                    $tsrc[$v] = 1;
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
}
