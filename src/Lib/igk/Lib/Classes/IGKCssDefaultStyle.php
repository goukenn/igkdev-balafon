<?php
// @file: IGKCssDefaultStyle.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Css\CssSupport;
use IGK\Css\ICssStyleContainer;
use IGK\Css\ICssSupport;
use IGK\System\Html\Css\CssUtils;
use IGK\System\IToArray;

/**
 * default style definition .
 * @package 
 */
final class IGKCssDefaultStyle implements ICssSupport, ArrayAccess, ICssStyleContainer, IToArray
{
    use \IGK\System\Polyfill\CSSDefaultArrayAccess;
    const COLORS_RULE = 5;
    const DECLARED_RULE = 1;
    const FILES_BIND_TEMP_RULE = 9;
    const FILES_RULE = 4;
    const FLAG_RULE = 2;
    const FONT_RULE = 7;
    const PARAMS_RULE = 6;
    const PROPERTIES = 0;
    const SYMBOLS_RULE = 3;
    const TEMP_FILES_RULE = 8;
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
     * 
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
                $this->_[self::SET_FLAG] = $s; // new array 
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
    ///<summary></summary>
    public function __construct(&$setting)
    {
        $uri = igk_io_request_uri();
        $this->_ = &$setting;
    }
    public function &getDeclaredRules()
    {
        $trule = &$this->_[self::DECLARED_RULE];;
        return $trule;
    }
    /**
     * request to append or not properties
     * @param mixed $g 
     * @param mixed $i 
     * @param mixed $v 
     * @return void 
     * @throws IGKException 
     */
    protected function _bindProperties(& $g, $i, $v){
        if (is_null($v)){
            unset($g[$i]);
            return;
        }
        $r = igk_getv($g, $i);
        $s = $this->noAppendDefinition || empty($r) ? $v :  CssUtils::MergeStyleDefinition($r, $v); 
        $g[$i]=$s; 
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

    public function getProperties()
    {
        return igk_getv($this->_, self::PROPERTIES);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __get($n)
    {

        igk_die(__METHOD__ . " not allowed [{$n}] : ");
    }
    // ///<summary></summary>
    // public function __serialize(){
    //     igk_ilog('serialize data'); 
    //     return [serialize(array_filter($this->_))];
    // }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v)
    {
        igk_die(__METHOD__ . " not allowed [{$n}]");
    }
    ///<summary>display value</summary>
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
    public function load_data(array $data)
    {
        $this->_ = [];
        foreach ([
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
        ] as $t) {
            if (isset($data[$t]) && is_array($g = $data[$t])) {
                $this->_[$t] = $g;
            }
        }
    }
    ///<summary></summary>
    ///<param name="seri"></param>
    // public function __unserialize($seri){
    //     igk_trace();
    //     igk_wln_e("unserie ....");
    //     if(is_array($seri)){
    //         $seri=$seri[0];
    //     }
    //     $this->_=unserialize($seri) ?? [];
    // }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="expression"></param>
    public function addRule($name, $expression)
    {
        $rule = &$this->_[self::DECLARED_RULE];
        $rule[$name] = $expression;
    }
    ///<summary></summary>
    public function clear()
    {

        if ($this->_) {
            // $_color = null;
            // if (isset($this->_[self::COLORS_RULE])){
            //     $_color = & $this->_[self::COLORS_RULE];
            // }
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
            // restore color rules 
            if ($_state) {
                // + | restore state flag
                $this->_[self::SET_FLAG] = $_state;
            }
        }
    }
    ///<summary></summary>
    public function clearFiles()
    {
        unset($this->_[self::FILES_RULE]);
    }
    ///<summary></summary>
    public function getAttributes()
    {
        return igk_getv($this->_, self::PROPERTIES);
    }
    /**
     * 
     * @return mixed 
     * @throws IGKException 
     */
    public function getdef()
    {
        return $this->getAttributes();
    }
    ///<summary>retrieve binded temp file </summary>
    /**
     * retrieve binded temp file 
     * @param bool $clear clear the temp binding files
     */
    public function getBindTempFiles($clear = 0)
    {
        $r = igk_getv($this->_, self::FILES_BIND_TEMP_RULE);
        if ($r && $clear) {
            unset($this->_[self::FILES_BIND_TEMP_RULE]); //=null;
        }
        return $r;
    }
    ///<summary>get reference to getCl</summary>
    public function &getCl()
    {
        $g = &$this->prepareStorage(self::COLORS_RULE);
        return $g;
    }
    ///<summary></summary>
    /**
     * get files to load
     * @return mixed 
     * @throws IGKException 
     */
    public function getFiles()
    {
        return igk_getv($this->_, self::FILES_RULE);
    }
    ///<summary>return registrated font</summary>
    ///<return refout="true"></return>
    public function &getFont()
    {
        $g = &$this->prepareStorage(self::FONT_RULE);
        return $g;
    }
    ///<summary></summary>
    public function getHasRules()
    {
        $tab = igk_getv($this->_, self::DECLARED_RULE);
        return $tab && (igk_count($tab) > 0);
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    public function &getParams()
    {
        $g = &$this->prepareStorage(self::PARAMS_RULE);
        return $g;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
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
    ///<summary></summary>
    ///<param name="lineseparator" default="null"></param>
    ///<param name="doc" default="null"></param>
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
    ///<summary></summary>
    public function getSymbols()
    {
        return igk_getv($this->_, self::SYMBOLS_RULE);
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
     * 
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
    ///<summary></summary>
    ///<param name="id"></param>
    ///<return refout="true"></return>
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
    ///<summary>register symbols package</summary>
    public function regSymbol($file)
    {
        $tab = igk_getv($this->_, self::SYMBOLS_RULE) ?? array();
        $tab[$file] = $file;
        $this->_[self::SYMBOLS_RULE] = $tab;
    }
    ///<summary></summary>
    public function resetParams()
    {
        unset($this->_[self::PARAMS_RULE]);
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public function rmRule($name)
    {
        $rule = &$this->_[self::DECLARED_RULE];
        unset($rule[$name]);
    }
    ///<summary>Bind css Temppory files</summary>
    ///<param name="files"></param>
    public function setBindTempFiles($files)
    {
        if (($files == null) || !is_string($files)) {
            unset($this->_[self::FILES_BIND_TEMP_RULE]);
        } else {
            $this->_[self::FILES_BIND_TEMP_RULE] = igk_io_collapse_path($files);
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function setCl($n, $v)
    {
        $g = &$this->prepareStorage(self::COLORS_RULE);
        $g[$n] = $v;
    }
    ///<summary></summary>
    ///<param name="files"></param>
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
     * 
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
