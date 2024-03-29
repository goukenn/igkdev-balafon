<?php
// @file: HtmlCssClassValueAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use Exception;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlCssClassValueAttribute as DomHtmlCssClassValueAttribute;
use IGK\System\Html\HtmlAttributeExpression;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\IDomHtmlCssClassValueAttribute;
use IGKApp;
use IGKEvents;
use IGKException;
use ReflectionException;

final class HtmlCssClassValueAttribute extends HtmlItemAttribute
{
    private $m_classes, $m_expressions;
    private $m_listener; 
    private static $sm_regClass = null;

    /**
     * 
     * @param ?callable $listener 
     * @return void 
     */
    public function setListener($listener){
        $this->m_listener = $listener;
        return $this;
    }   


    ///<summary></summary>
    public function __construct()
    {
        $this->m_classes = array();
        $this->m_expressions = array();
    }
    ///<summary></summary>
    public function __serialize()
    {
        if (igk_get_env("seri")) {
            igk_die(__CLASS__ . "::loop detected :::" . __METHOD__);
            igk_exit();
        }
        igk_set_env("seri", 1);
        $s = 'v:' . implode(" ", array_keys($this->m_classes)) . ';';
        igk_set_env("seri", null);
        return [$s];
    }
    ///<summary></summary>
    ///<param name="data"></param>
    public function __unserialize($data)
    {
        if (is_array($data)) {
            $data = $data[0];
        }
        $o = igk_unseri_data($data);
        $tab = explode(" ", $o->v);
        $this->m_classes = array_combine($tab, $tab);
        /// TODO : TEST UN SERIALISE
        $r = igk_getv($o, "r");
        if ($r) {
            $owner = igk_get_env("sys://serialize/owner");
            $v = 'O:8:"stdClass":1:{s:5:"value";r:1;}';
        }
    }
    /**
     * return value string 
     * @return string 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function __toString()
    {
        return $this->getValue();
    }
    ///<summary></summary>
    ///<param name="v"></param>
    private function _add($v)
    {
        if (is_array($v)) {
            igk_die("is array");
        }
        $v = trim($v);
        if (strlen($v) > 0) {
            switch ($v[0]) {
                case '-':
                    $v = substr($v, 1);
                    $this->remove($v);
                    break;
                case '+':
                    $v = substr($v, 1);
                    if (!isset($this->m_classes[$v])) {
                        $this->m_classes[$v] = $v;
                    }
                    self::_RegClass("." . $v);
                    break;
                case "[":
                case "{":
                    $this->m_expressions[] = $v;
                    break;
                default:
                    if (!isset($this->m_classes[$v])) {
                        $this->m_classes[$v] = $v;
                    }
                    self::_RegClass("." . $v);
                    break;
            }
        }
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    private static function &_GetRegClass()
    {
        if (self::$sm_regClass === null) {
            if (igk_app()->session->RegClasses !== null) {
                self::$sm_regClass = &igk_app()->session->RegClasses->regClass;
            } else {
                self::$sm_regClass = array();
            }
        }
        return self::$sm_regClass;
    }
    ///<summary></summary>
    ///<param name="App"></param>
    ///<param name="name"></param>
    private static function _initThemeDef($App, $name)
    {
        $tab = array();
        $c = preg_match_all("/^\.(?P<type>(bgcl|fcl|res|ft))\-(?P<name>(.)+)$/i", $name, $tab);
        if ($c > 0) {
            $def = $App->Doc->Theme->def;
            for ($i = 0; $i < $c; $i++) {
                $t = strtolower($tab['type'][$i]);
                $n = strtolower($tab['name'][$i]);
                $def[$name] = "[$t:$n]";
            }
        }
    }
    ///<summary></summary>
    ///<param name="name"></param>
    private static function _RegClass($name)
    {
        if (!IGKApp::IsInit() || (defined("IGK_NO_WEB") && (constant("IGK_NO_WEB") == 1))) {
            return;
        }
        $v = &self::_GetRegClass();
        if (!isset($v[$name])) {
            $v[$name] = $name;
            igk_hook(IGKEvents::HOOK_CSS_REG, [$name]);
        }
    }
    ///<summary></summary>
    ///<param name="name"></param>
    private static function _UnRegClass($name)
    {
        $v = &self::_GetRegClass();
        if (isset($v[$name])) {
            unset($v[$name]);
            // igk_invoke_session_event(IGKApp::$REG_CSS_CLASS_EVT, array(igk_app(), null));
        }
    }
    ///<summary>add css class value</summary>
    ///<param name="class">mixed string expression or array defenition</param>
    /**
     * add css class value
     * @param mixed|array|object $class
     */
    public function add($class)
    {
        if (empty($class))
            return false;
        if (is_object($class)) {
            if ($class instanceof HtmlAttributeExpression) {
                $this->m_expressions[] = $class; 
                return true;//$class = null;               
            } else if ($class instanceof DomHtmlCssClassValueAttribute){
                $keys = $class->getKeys();
                array_map([$this,'_add'], $keys); 
                return true;
            }
            $class = (array)$class; 
        }

        $tab = null;
        if (is_array($class)) {
            $cl = [];
            foreach ($class as $k => $v) {
                if (!is_string($v) && is_callable($v)) {
                    if ($v()) {
                        $cl[] = $k;
                    } else {
                        $cl[] = "-" . $k;
                    }
                } else if ($v) {
                    if (is_numeric($k)){
                        if ($v instanceof IDomHtmlCssClassValueAttribute){
                            $this->m_classes[] = $v;
                            continue;
                        }

                        $cl[] = $v;
                    } else {
                        $cl[] = $k;
                    }
                } else
                    $cl[] = "-" . $k;
            }
            $tab = $cl;
            $class = implode(" ", $cl);
        } else
            $tab = explode(" ", $class);
        if ($tab) {
            if (count($tab) == 1) {
                $this->_add($class);
            } else {
                foreach ($tab as $v) {
                    $this->_add($v);
                }
            }
        }
    }
    ///<summary>clear classes_name storage</summary>
    /**
     * clear classes_name storage
     * @return void 
     */
    public function Clear()
    {
        $this->m_expression = array();
        $this->m_classes = array();
    } 
    /**
     * get if this instance contain classe_name
     * @param mixed $name 
     * @return bool 
     */
    public function contain($name)
    {
        return isset($this->m_classes[$name]);
    }
    ///<summary></summary>
    public function evalClassStyle()
    {
        $out = IGK_STR_EMPTY;
        $i = 0;
        foreach ($this->m_classes as $v) {
            if ($i == 0)
                $i = 1;
            else
                $out .= " ";
            $out .= igk_css_get_style("." . $v);
        }
        return $out;
    }
    ///<summary></summary>
    public function getKeys()
    {
        return array_keys($this->m_classes);
    }
    ///<summary></summary>
    ///<param name="theme"></param>
    ///<param name="v"></param>
    private static function GetParentClass($theme, $v)
    {
        $s = $theme[$v];
        if (!empty($s)) {
            $t = array();
            if (preg_match_all(IGK_CSS_CHILD_EXPRESSION_REGEX, $s, $t)) {
                $vv = $t["name"][0];
                if (self::IsCssChild($vv)) {
                    return self::GetParentClass($theme, $vv);
                }
                return $vv;
            }
        }
        return $v;
    }
    ///<summary></summary>
    public static function GetRegClass()
    {
        return self::_GetRegClass();
    }
    ///<summary>get html css class presentation value</summary>
    public function getValue($options = null):string
    {
        $out = IGK_STR_EMPTY;
        $i = 0;
        if ($fc = $this->m_listener){
            if ($list = $fc()){
                $this->_add($list);
            }
        }

        foreach ($this->m_classes as $v) {
            if ($i == 0)
                $i = 1;
            else
                $out .= " ";
            if (self::IsCssChild($v)) {
                $out .= self::GetParentClass(igk_app()->getDoc()->Theme, $v);
            } else
                $out .= $v;
        }
        $b = HtmlUtils::GetValue($out);
        if ($this->m_expressions) {
            $i && $b .= ' ';

            foreach ($this->m_expressions as $k) {
                if (!is_string($k) && is_callable($k) ){
                    $k = $k();
                }
                $b .= '' . $k;
            }
            // treat operator 
            // $b = preg_replace("/(^|\s+)(+|-)/", "",$b);
        }
        return empty($b) ? '' : $b;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    public static function IsCssChild($v)
    {
        if (!IGKApp::IsInit()) {
            return false;
        }
        $c = igk_app();
        if ($c && $c->Doc) {
            $s = $c->Doc->Theme[$v];
            if (!empty($s)) {
                $r = preg_match(IGK_CSS_CHILD_EXPRESSION_REGEX, trim($s));
                return $r;
            }
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="class"></param>
    public function remove($class)
    {
        if (empty($class))
            return;
        if (isset($this->m_classes[$class])) {
            unset($this->m_classes[$class]);
        }
    }
    ///<summary>Represente setClasses function</summary>
    ///<param name="expression"></param>
    public function setClasses($expression)
    {
        $tb = array_filter(explode(" ", $expression));
        foreach ($tb as $s) {
            $this->add($s);
        }
        return $s;
    }
    ///<summary></summary>
    ///<param name="key"></param>
    public static function UnRegClass($key)
    {
        self::_UnRegClass($key);
    }    
    public function __isset($name)
    {
        return isset($this->m_classes[$name]);
    }
    /**
     * callback expression 
     * @param mixed $expression 
     * @return void 
     */
    public function addListener($expression){
        if (!is_null($expression)){
            $this->m_expressions[] = $expression;
        }
    }
    /**
     * get stored classes definition
     * @return array 
     */
    public function getClasses(){
        return $this->m_classes;
    }
   
}
