<?php
// @file: IGKCssDefaultStyle.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Css\CssSupport;
use IGK\Css\ICssAddRule;
use IGK\Css\ICssStyleContainer;
use IGK\Css\ICssSupport;
use IGK\System\Console\Logger;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\System\IToArray;
use IGK\System\Text\RegexMatcherContainer;

/**
 * default style definition .
 * @package 
 */
final class IGKCssDefaultStyle implements ICssSupport, ICssAddRule, ArrayAccess, ICssStyleContainer, IToArray
{
    use \IGK\System\Polyfill\CSSDefaultArrayAccess;
    /**
     * Constant: colors rule.
     * @var mixed
     */
    const COLORS_RULE = 5;
    /**
     * Constant: declared rule.
     * @var mixed
     */
    const DECLARED_RULE = 1;
    /**
     * Constant: files bind temp rule.
     * @var mixed
     */
    const FILES_BIND_TEMP_RULE = 9;
    /**
     * Constant: files rule.
     * @var mixed
     */
    const FILES_RULE = 4;
    /**
     * Constant: flag rule.
     * @var mixed
     */
    const FLAG_RULE = 2;
    /**
     * Constant: font rule.
     * @var mixed
     */
    const FONT_RULE = 7;
    /**
     * Constant: params rule.
     * @var mixed
     */
    const PARAMS_RULE = 6;
    /**
     * Constant: properties.
     * @var mixed
     */
    const PROPERTIES = 0;
    /**
     * Constant: symbols rule.
     * @var mixed
     */
    const SYMBOLS_RULE = 3;
    /**
     * Constant: temp files rule.
     * @var mixed
     */
    const TEMP_FILES_RULE = 8;
    /**
     * Constant: set flag.
     * @var mixed
     */
    const SET_FLAG = 19;
    /**
     * append same key attribute to merge the same property
     * @var ?bool
     */
    var $noAppendDefinition;
    /**
     * flag used to disable theme dynamic rendering for controller.
     */
    const ST_NO_THEME_RENDERING_FLAG = 'no_theme_rendering';
    /**
     * Property: .
     * @var mixed
     */
    private $_;
    /**
     * clear property definition 
     * @param string $defname 
     * @return void 
     */
    public function clearPropertyDef(string $defname)
    {
        if (isset($this->_[self::PROPERTIES])) {
            $g = &$this->_[self::PROPERTIES];
            unset($g[$defname]);
        }
    }
    /**
     * auto generate doc.
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setStyleFlag(string $name, $value)
    {
        if (isset($this->_[self::SET_FLAG])) {
            $s = &$this->_[self::SET_FLAG];
            if (is_null($value)) {
                unset($s[$name]);
            } else
                $s[$name] = $value;
        } else {
            if (!is_null($value)) {
                $s = [];
                $s[$name] = $value;
                $this->_[self::SET_FLAG] = $s;
            }
        }
        return $this;
    }
    /**
     * get the stored value and unset it 
     * @param string $name 
     * @return mixed 
     * @throws IGKException 
     */
    public function unsetStyleFlag(string $name)
    {
        $v = null;
        if (isset($this->_[self::SET_FLAG])) {
            $s = &$this->_[self::SET_FLAG];
            $v = igk_getv($s, $name);
            unset($s[$name]);
            if (count($s) <= 0) {
                unset($this->_[self::SET_FLAG]);
            }
        }
        return $v;
    }
    /**
     * .ctr
     * @param mixed & $setting
     */
    public function __construct(&$setting)
    {
        $uri = igk_io_request_uri();
        $this->_ = &$setting;
    }
    /**
     * Returns Declared Rules.
     */
    public function &getDeclaredRules()
    {
        $trule = &$this->_[self::DECLARED_RULE];
        return $trule;
    }
    /**
     * request to append or not properties
     * @param mixed & $g
     * @param mixed $i
     * @param mixed $v value
     * @throws IGKException
     * @return void
     */
    protected function _bindProperties(&$g, $i, $v)
    {
        if (is_null($i)) {
            if ($v) {
                $g[] = $v;
            }
            return;
        }
        if (is_null($v)) {
            unset($g[$i]);
            return;
        }
        $r = igk_getv($g, $i);
        $new =  $this->noAppendDefinition || empty($r) || ($v == $r);
        $s = $new ? $v :  CssUtils::MergeStyleDefinition($r, $v);
        if (!$new && $s && !is_array($s) && (1 || igk_environment()->isDev())) {
            $s = self::MergeProperties($s);
        }
        $g[$i] = $s;
    }
    public static function MergeProperties(string $content)
    {
        $src = $content;
        $regex = new RegexMatcherContainer;
        $pos = 0;
        // define
        $comment = $regex->appendMultilineComment()->last();
        $str = $regex->appendStringDetection('string', true)->last();
        $bran_p = $regex->begin('\(', '\)', 'brank-parenthese')->last();
        $brank = $regex->begin('\[', '\](\\s*)?(?=;)?', 'brank')->last();
        $curl = $regex->begin('\{', '\}', 'curl-brank')->last();
        $instruct = $regex->match(';|(?=\})', 'end')->last();
        $props = $regex->match(':', 'prop')->last();

        $brank->patterns = $curl->patterns = $bran_p->patterns = [
            $comment,
            $str,
        ];



        $def = [];
        $inf = (object)[
            'buffer' => '',
            'name' => '',
            'offset' => 0,
            'src' => $src,
            'skip' => false,
            'multi_expression' => false,
            'property' => null,
            'brank_def' => []
        ];
        $fc_handle = [
            'brank' => function (&$def, $e, $inf) {
                if ($inf->skip) return;
                // $inf->buffer .= substr($inf->src, $inf->offset, -$e->from - $inf->offset);
                // $inf->buffer .= $e->value;
                $inf->offset = $e->to;
                $def[] = trim($e->value);
            },
            'curl-brank' => function (&$def, $e, $inf) {
                if ($inf->skip) return;
                // if ($inf->multi_expression){
                //     $inf->multi_expression =false;
                //     $def[] = trim($e->value);
                //     return;
                // }
                $tv = trim(substr($e->value, 1, -1));
                $inf->offset = $e->to;
                $is_expression = preg_match('/^(sys|\\w+)\\b\\s*:\\s*([\\w\-]+)(((,|;| )([\\w\-]+))+)?\\s*$/', $tv, $tab);


                if ($is_expression && (count($tg = explode(':', $tv, 2)) > 1)) {
                    $type = trim($tg[0]);
                    $tg = preg_split('/\\s*(,|;|\\s+)\\s*/', trim($tg[1]));
                    if (!isset($inf->brank_def[$type])) {
                        $inf->brank_def[$type] = [];
                    }
                    $tg = array_unique(array_merge($inf->brank_def[$type], $tg));

                    $inf->brank_def[$type] = $tg;
                } else {
                    $def[] = $e->value;
                }
            },
            'prop' => function (&$def, $e, $inf) {
                $p = trim(substr($inf->src, $inf->offset, $e->from - $inf->offset));
                if (empty($p)) {
                    igk_die('invalid syntax');
                }
                $inf->property = $p;
                $inf->offset = $e->to;
                $inf->skip = true;
            },
            'end' => function (&$def, $e, $inf) {
                $inf->skip = false;
                if (is_null($inf->property)) {
                    $inf->offset = $e->to;
                    return;
                }
                $v = trim(substr($inf->src, $inf->offset, $e->from - $inf->offset));
                $inf->offset = $e->to;
                self::_BindPropertyValue($def, $inf, $v);
                $inf->property = null;
            }
        ];

        $is_debug = igk_is_debug();
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                if (is_null($e->parentInfo)) {
                    $id = $e->tokenID;
                    $is_debug && Logger::info('token :::: ' . $id . '[' . $e->value . ']');
                    if ($fc = igk_getv($fc_handle, $id)) {
                        $fc($def, $e, $inf);
                    }
                } else {
                    igk_dev_wln('tag-mixing....');
                }
            }
        }
        if ($inf->offset < $pos) {
            $sb = trim(substr($src, $inf->offset));
            self::_BindPropertyValue($def, $inf, $sb);
        }
        /**
         * @var mixed
         */
        $tb  = [];
        if ($inf->brank_def) {
            foreach ($inf->brank_def as $k => $t) {
                $tb[] = sprintf('{%s:%s}', $k, implode(' ', $t));
            }
            array_unshift($def, ...$tb);
        }
        $def = implode(' ', array_map(function ($o, $k) {
            if (is_numeric($k)) {
                return $o;
            }
            return implode(':', [$k, $o]) . ';';
        }, $def, array_keys($def)));
        return $def;
    }
    /**
     * 
     * @param mixed &$def 
     * @param mixed $inf 
     * @param mixed $value 
     * @return void 
     */
    private static function _BindPropertyValue(&$def, $inf, $value)
    {
        if ($inf->property) {
            $v = $value; 
            if (CssUtils::IsAllowedPropertyValue($v)) {
                $def[$inf->property] = $v;
            } else {
                unset($def[$inf->property]);
            }
        }
    }
    /**
     * define support rule
     * @param string $rule rule expression
     * @return HtmlDocTheme 
     * @example _ $def->supports('backdrop-filter: blur(2px)') 
     */
    public function supports(string $rule)
    {
        $key = "@supports (" . $rule . ")";
        $trule = &$this->_[self::DECLARED_RULE];
        if (isset($trule[$key])) {
            if (($trule[$key] instanceof CssSupport)) {
                return $trule[$key];
            }
        }
        $rule = new CssSupport($rule);
        $trule[$key] = $rule;
        return $rule;
    }
    /**
     * Returns Properties.
     */
    public function getProperties()
    {
        return igk_getv($this->_, self::PROPERTIES);
    }
    /**
     * .destructor
     * @param mixed $n
     */
    public function __get($n)
    {
        igk_die(__METHOD__ . " not allowed [{$n}] : ");
    }
    /**
     * destructor
     * @param mixed $n
     * @param mixed $v
     */
    public function __set($n, $v)
    {
        igk_die(__METHOD__ . " not allowed [{$n}]");
    }
    /**
     * get string presentation.
     */
    public function __toString()
    {
        return get_class($this) . "#items[count(" . count($this->_) . ")]";
    }
    /**
     * return a copy array presentation of this style
     * @return array 
     */
    public function to_array(): ?array
    {
        // + |------------------------------------------------------
        // + | copy array  
        $o = [];
        foreach ($this->_ as $k => $v) {
            $o[$k] = $v;
        }
        return $o;
    }
    /**
     * Loads data.
     * @param array $data
     */
    public function load_data(array $data)
    {
        $this->_ = [];
        foreach (
            [
                self::COLORS_RULE,
                self::DECLARED_RULE,
                self::FILES_BIND_TEMP_RULE,
                self::FILES_RULE,
                self::FLAG_RULE,
                self::FONT_RULE,
                self::PARAMS_RULE,
                self::PROPERTIES,
                self::SYMBOLS_RULE,
                self::TEMP_FILES_RULE,
                self::SET_FLAG,
            ] as $t
        ) {
            if (isset($data[$t]) && is_array($g = $data[$t])) {
                $this->_[$t] = $g;
            }
        }
    }
    /**
     * Adds Rule.
     * @param mixed $name
     * @param mixed $expression
     */
    public function addRule($name, $expression)
    {
        $rule = &$this->_[self::DECLARED_RULE];
        $rule[$name] = $expression;
    }
    /**
     * Clears.
     */
    public function clear()
    {
        if ($this->_) {
            $_state = igk_getv($this->_, self::SET_FLAG);
            $keys = array_keys($this->_);
            $to_reset = [
                self::COLORS_RULE => null
            ];
            foreach ($keys as $k) {
                if (key_exists($k, $to_reset)) {
                    $to_reset[$k] = &$this->_[$k];
                    $to_reset[$k] = [];
                    $this->_[$k] = &$to_reset[$k];
                } else {
                    unset($this->_[$k]);
                }
            }
            if ($_state) {
                // + | restore state flag
                $this->_[self::SET_FLAG] = $_state;
            }
        }
    }
    /**
     * Clears Files.
     */
    public function clearFiles()
    {
        unset($this->_[self::FILES_RULE]);
    }
    /**
     * Returns Attributes.
     */
    public function getAttributes()
    {
        return igk_getv($this->_, self::PROPERTIES);
    }
    /**
     * auto generate doc.
     * @return mixed
     */
    public function getdef()
    {
        return $this->getAttributes();
    }
    /**
     * retrieve binded temp file 
     * @param bool $clear clear the temp binding files
     */
    public function getBindTempFiles($clear = 0)
    {
        $r = igk_getv($this->_, self::FILES_BIND_TEMP_RULE);
        if ($r && $clear) {
            unset($this->_[self::FILES_BIND_TEMP_RULE]);
        }
        return $r;
    }
    /**
     * Returns Cl.
     */
    public function &getCl()
    {
        $g = &$this->prepareStorage(self::COLORS_RULE);
        return $g;
    }
    /**
     * clear color rules
     */
    public function clearColors()
    {
        unset($this->_[self::COLORS_RULE]);
    }
    /**
     * get files to load
     * @return mixed 
     * @throws IGKException 
     */
    public function getFiles()
    {
        return igk_getv($this->_, self::FILES_RULE);
    }
    /**
     * Returns Font.
     */
    public function &getFont()
    {
        $g = &$this->prepareStorage(self::FONT_RULE);
        return $g;
    }
    /**
     * Returns Has Rules.
     */
    public function getHasRules()
    {
        $tab = igk_getv($this->_, self::DECLARED_RULE);
        return $tab && (igk_count($tab) > 0);
    }
    /**
     * Returns Params.
     */
    public function &getParams()
    {
        $g = &$this->prepareStorage(self::PARAMS_RULE);
        return $g;
    }
    /**
     * Returns Rules.
     */
    public function &getRules()
    {
        $g = null;
        if (isset($this->_[self::FLAG_RULE])) {
            $g = &$this->_[self::FLAG_RULE];
        } else {
            $g = array();
            $this->_[self::FLAG_RULE] = &$g;
        }
        return $g;
    }
    /**
     * Returns Rules String.
     * @param null|mixed $lineseparator
     * @param mixed $themeexport
     * @param null|mixed $systheme
     */
    public function getRulesString($lineseparator = null, $themeexport = false, $systheme = null)
    {
        $o = "";
        $tab = igk_getv($this->_, self::DECLARED_RULE);
        foreach ($tab as $k => $v) {
            if (is_string($v)) {
                $v = igk_css_treat($v, $themeexport, $this, $systheme);
                if ($v) {
                    $o .= $k . "{" . $v . "}" . $lineseparator;
                }
            } elseif ($v instanceof CssSupport) {
                $b = $v->getCssDef($this, $systheme);
                $o .= $k . "{" . $b . "}";
            }
        }
        return $o;
    }
    /**
     * Returns Symbols.
     */
    public function getSymbols()
    {
        return igk_getv($this->_, self::SYMBOLS_RULE);
    }
    /**
     * auto generate doc.
     * @param bool $clear
     * @return mixed
     */
    public function &getTempFiles($clear = false)
    {
        $g = &$this->prepareStorage(self::TEMP_FILES_RULE);
        if ($clear) {
            $gcp = $g;
            $g = [];
            $g = &$gcp;
            unset($this->_[self::TEMP_FILES_RULE]);
        }
        return $g;
    }
    /**
     * auto generate doc.
     * @param mixed $id
     * @return mixed
     */
    private function &prepareStorage($id)
    {
        $g = null;
        if (isset($this->_[$id]))
            $g = &$this->_[$id];
        else {
            $g = array();
            $this->_[$id] = &$g;
        }
        return $g;
    }
    /**
     * Reg symbol.
     * @param mixed $file
     */
    public function regSymbol($file)
    {
        $tab = igk_getv($this->_, self::SYMBOLS_RULE) ?? array();
        $tab[$file] = $file;
        $this->_[self::SYMBOLS_RULE] = $tab;
    }
    /**
     * Resets Params.
     */
    public function resetParams()
    {
        unset($this->_[self::PARAMS_RULE]);
    }
    /**
     * Rm rule.
     * @param mixed $name
     */
    public function rmRule($name)
    {
        $rule = &$this->_[self::DECLARED_RULE];
        unset($rule[$name]);
    }
    /**
     * Sets Bind Temp Files.
     * @param mixed $files
     */
    public function setBindTempFiles($files)
    {
        if (($files == null) || !is_string($files)) {
            unset($this->_[self::FILES_BIND_TEMP_RULE]);
        } else {
            $this->_[self::FILES_BIND_TEMP_RULE] = igk_io_collapse_path($files);
        }
    }
    /**
     * Sets Cl.
     * @param mixed $n
     * @param mixed $v
     */
    public function setCl($n, $v)
    {
        $g = &$this->prepareStorage(self::COLORS_RULE);
        $g[$n] = $v;
    }
    /**
     * Sets Files.
     * @param mixed $files
     */
    public function setFiles($files)
    {
        if (($files == null) || !is_string($files)) {
            unset($this->_[self::FILES_RULE]);
        } else {
            $rf = igk_io_collapse_path($files);
            $this->_[self::FILES_RULE] = $rf;
        }
    }
    /**
     * auto generate doc.
     * @return bool
     */
    public function reverseDefinitionProperties()
    {
        if (isset($this->_[self::PROPERTIES])) {
            $p = $this->_[self::PROPERTIES];
            $p = array_reverse($p, true);
            $this->_[self::PROPERTIES] = $p;
            return true;
        }
        return false;
    }
}
