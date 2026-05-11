<?php
// @file: HtmlCssClassValueAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use Exception;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlCssClassValueAttribute as DomHtmlCssClassValueAttribute;
use IGK\System\Html\HtmlAttributeExpression;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\IDomHtmlCssClassValueAttribute;
use IGK\System\Text\RegexMatcherContainer;
use IGKApp;
use IGKEvents;
use IGKException;
use ReflectionException;

/**
* Html css class value attribute.
* @package IGK\System\Html\Dom
*/
final class HtmlCssClassValueAttribute extends HtmlItemAttribute
{
    /**
    * Properties: classes, expressions.
    * @var mixed
    */
    private $m_classes, $m_expressions;
    /**
    * Listener: listener.
    * @var mixed
    */
    private $m_listener;
    /**
    * Property: reg class.
    * @var mixed
    */
    private static $sm_regClass = null;
    /**
    * Name of treat class name.
    * @var mixed
    */
    private $_treat_ClassName;
    /**
    * auto generate doc.
    * @param ?callable $listener
    * @return void
    */
    public function setListener($listener){
        $this->m_listener = $listener;
        return $this;
    }
    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_classes = array();
        $this->m_expressions = array();
    }
    /**
    * Returns serializable representation.
    */
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
    /**
    * Restores instance from serialized data.
    * @param mixed $data
    */
    public function __unserialize($data)
    {
        if (is_array($data)) {
            $data = $data[0];
        }
        $o = igk_unseri_data($data);
        $tab = explode(" ", $o->v);
        $this->m_classes = array_combine($tab, $tab);
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
    /**
    * auto generate doc.
    * @param mixed $v
    * @return mixed
    */
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
    /**
    * auto generate doc.
    * @return mixed
    */
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
    /**
    * auto generate doc.
    * @param mixed $App
    * @param mixed $name
    * @return mixed
    */
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
    /**
    * auto generate doc.
    * @param mixed $name
    * @return mixed
    */
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
    /**
    * auto generate doc.
    * @param mixed $name
    * @return mixed
    */
    private static function _UnRegClass($name)
    {
        $v = &self::_GetRegClass();
        if (isset($v[$name])) {
            unset($v[$name]);
        }
    }
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
                return true;
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
        } else{
            // + treat $class definition 
            if (strpos($class, '.')!== false){
                ($g = $this->_treat_ClassName)  || ($g = $this->_treat_ClassName  = self::ClassDefinitionTreatment());
                $class = $g($class);
            }
            $tab = explode(" ", $class);
        }
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
    /**
    * Class definition treatment.
    */
    static function ClassDefinitionTreatment(){
        $r = new RegexMatcherContainer;
        $r->match('(?i)\.([\\w+\\-\\\\]+)\\b', 'word');
        return function($c)use($r){
            $pos = 0;
            $o = [];
            $v = '';
            $offset = 0;
            while($g = $r->detect($c, $pos)){
                if ($e = $r->end($g, $c, $pos)){
                    $v = substr($c, $offset, $e->from-$offset);
                    if (!empty($v)){
                        $o[] = $v;
                    }
                    $offset=$e->to;
                    $o[] = $e->beginCaptures[1][0];
                }
            }
            if (!empty($v = trim(substr($c, $offset)))){
                $o[] = $v;
            }
            return implode(' ', $o);
        };
    }
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
    /**
    * Eval class style.
    */
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
    /**
    * Returns Keys.
    */
    public function getKeys()
    {
        return array_keys($this->m_classes);
    }
    /**
    * auto generate doc.
    * @param mixed $theme
    * @param mixed $v
    * @return mixed
    */
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
    /**
    * Returns Reg Class.
    */
    public static function GetRegClass()
    {
        return self::_GetRegClass();
    }
    /**
    * Returns Value.
    * @param null|mixed $options
    * @return string
    */
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
        }
        return empty($b) ? '' : $b;
    }
    /**
    * Returns true if Css Child.
    * @param mixed $v
    */
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
    /**
    * Removes.
    * @param mixed $class
    */
    public function remove($class)
    {
        if (empty($class))
            return;
        if (isset($this->m_classes[$class])) {
            unset($this->m_classes[$class]);
        }
    }
    /**
    * Sets Classes.
    * @param mixed $expression
    */
    public function setClasses($expression)
    {
        $tb = array_filter(explode(" ", $expression));
        foreach ($tb as $s) {
            $this->add($s);
        }
        return $s;
    }
    /**
    * Un reg class.
    * @param mixed $key
    */
    public static function UnRegClass($key)
    {
        self::_UnRegClass($key);
    }
    /**
    * Checks if a dynamic property is set.
    * @param mixed $name
    */
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