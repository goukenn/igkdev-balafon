<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlContext;
use IGK\System\Html\HtmlEventProperty;
use IGK\System\Html\HtmlExpressionAttribute;
use IGK\System\Html\HtmlStyleValueAttribute;
use IGKException;
use function igk_resources_gets as __;

/**
 * 
 * @package IGK\System\Html\Dom
 * @method HtmlNode div() add div
 * @method HtmlNode container() add a div container
 * @method HtmlFormNode form() add a div form
 * @method HtmlFormNode table() add a div form
 * @method HtmlNode br() add break node
 * @method HtmlNode p() add paragraph node 
 * @method self a() create a html 'a' node 
 * @method self abbr() create a html 'abbr' node 
 * @method self acronym() create a html 'acronym' node 
 * @method self address() create a html 'address' node 
 * @method self applet() create a html 'applet' node 
 * @method self area() create a html 'area' node 
 * @method self article() create a html 'article' node 
 * @method self aside() create a html 'aside' node 
 * @method self audio() create a html 'audio' node 
 * @method self b() create a html 'b' node 
 * @method self base() create a html 'base' node 
 * @method self basefont() create a html 'basefont' node 
 * @method self bdi() create a html 'bdi' node 
 * @method self bdo() create a html 'bdo' node 
 * @method self big() create a html 'big' node 
 * @method self blockquote() create a html 'blockquote' node 
 * @method self body() create a html 'body' node 
 * @method self br() create a html 'br' node 
 * @method self button() create a html 'button' node 
 * @method self canvas() create a html 'canvas' node 
 * @method self caption() create a html 'caption' node 
 * @method self center() create a html 'center' node 
 * @method self cite() create a html 'cite' node 
 * @method self code() create a html 'code' node 
 * @method self col() create a html 'col' node 
 * @method self colgroup() create a html 'colgroup' node 
 * @method self data() create a html 'data' node 
 * @method self datalist() create a html 'datalist' node 
 * @method self dd() create a html 'dd' node 
 * @method self del() create a html 'del' node 
 * @method self details() create a html 'details' node 
 * @method self dfn() create a html 'dfn' node 
 * @method self dialog() create a html 'dialog' node 
 * @method self dir() create a html 'dir' node 
 * @method self div() create a html 'div' node 
 * @method self dl() create a html 'dl' node 
 * @method self dt() create a html 'dt' node 
 * @method self em() create a html 'em' node 
 * @method self embed() create a html 'embed' node 
 * @method self fieldset() create a html 'fieldset' node 
 * @method self figcaption() create a html 'figcaption' node 
 * @method self figure() create a html 'figure' node 
 * @method self font() create a html 'font' node 
 * @method self footer() create a html 'footer' node 
 * @method self form() create a html 'form' node 
 * @method self frame() create a html 'frame' node 
 * @method self frameset() create a html 'frameset' node 
 * @method self head() create a html 'head' node 
 * @method self header() create a html 'header' node 
 * @method self hgroup() create a html 'hgroup' node 
 * @method self h1() create a html 'h1' node 
 * @method self h2() create a html 'h2' node 
 * @method self h3() create a html 'h3' node 
 * @method self h4() create a html 'h4' node 
 * @method self h5() create a html 'h5' node 
 * @method self h6() create a html 'h6' node 
 * @method self hr() create a html 'hr' node 
 * @method self html() create a html 'html' node 
 * @method self i() create a html 'i' node 
 * @method self iframe() create a html 'iframe' node 
 * @method self img() create a html 'img' node 
 * @method self input() create a html 'input' node 
 * @method self ins() create a html 'ins' node 
 * @method self kbd() create a html 'kbd' node 
 * @method self keygen() create a html 'keygen' node 
 * @method self label() create a html 'label' node 
 * @method self legend() create a html 'legend' node 
 * @method self li() create a html 'li' node 
 * @method self link() create a html 'link' node 
 * @method self main() create a html 'main' node 
 * @method self map() create a html 'map' node 
 * @method self mark() create a html 'mark' node 
 * @method self menu() create a html 'menu' node 
 * @method self menuitem() create a html 'menuitem' node 
 * @method self meta() create a html 'meta' node 
 * @method self meter() create a html 'meter' node 
 * @method self nav() create a html 'nav' node 
 * @method self noframes() create a html 'noframes' node 
 * @method self noscript() create a html 'noscript' node 
 * @method self object() create a html 'object' node 
 * @method self ol() create a html 'ol' node 
 * @method self optgroup() create a html 'optgroup' node 
 * @method self option() create a html 'option' node 
 * @method self output() create a html 'output' node 
 * @method self p() create a html 'p' node 
 * @method self param() create a html 'param' node 
 * @method self picture() create a html 'picture' node 
 * @method self pre() create a html 'pre' node 
 * @method self progress() create a html 'progress' node 
 * @method self q() create a html 'q' node 
 * @method self rp() create a html 'rp' node 
 * @method self rt() create a html 'rt' node 
 * @method self ruby() create a html 'ruby' node 
 * @method self s() create a html 's' node 
 * @method self samp() create a html 'samp' node 
 * @method self script() create a html 'script' node 
 * @method self section() create a html 'section' node 
 * @method self select() create a html 'select' node 
 * @method self small() create a html 'small' node 
 * @method self source() create a html 'source' node 
 * @method self span() create a html 'span' node 
 * @method self strike() create a html 'strike' node 
 * @method self strong() create a html 'strong' node 
 * @method self style() create a html 'style' node 
 * @method self sub() create a html 'sub' node 
 * @method self summary() create a html 'summary' node 
 * @method self sup() create a html 'sup' node 
 * @method self svg() create a html 'svg' node 
 * @method self table() create a html 'table' node 
 * @method self tbody() create a html 'tbody' node 
 * @method self td() create a html 'td' node 
 * @method self template() create a html 'template' node 
 * @method self textarea() create a html 'textarea' node 
 * @method self tfoot() create a html 'tfoot' node 
 * @method self th() create a html 'th' node 
 * @method self thead() create a html 'thead' node 
 * @method self time() create a html 'time' node 
 * @method self title() create a html 'title' node 
 * @method self tr() create a html 'tr' node 
 * @method self track() create a html 'track' node 
 * @method self tt() create a html 'tt' node 
 * @method self u() create a html 'u' node 
 * @method self ul() create a html 'ul' node 
 * @method self var() create a html 'var' node 
 * @method self video() create a html 'video' node 
 * @method self wbr() create a html 'wbr' node 
 */
class HtmlNode extends HtmlItemBase
{
    const HTML_NAMESPACE = "http://schemas.igkdev.com/balafon/html";
    static $AutoTagNameClass = false;
    const NODE_LIST = "a|abbr|acronym|address|applet|area|article|aside|audio|b|base|basefont|bdi|bdo|big|blockquote|body|br|button|canvas|caption|center|cite|code|col|colgroup|data|datalist|dd|del|details|dfn|dialog|dir|div|dl|dt|em|embed|fieldset|figcaption|figure|font|footer|form|frame|frameset|head|header|hgroup|h1|h2|h3|h4|h5|h6|hr|html|i|iframe|img|input|ins|kbd|keygen|label|legend|li|link|main|map|mark|menu|menuitem|meta|meter|nav|noframes|noscript|object|ol|optgroup|option|output|p|param|picture|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|source|span|strike|strong|style|sub|summary|sup|svg|table|tbody|td|template|textarea|tfoot|th|thead|time|title|tr|track|tt|u|ul|var|video|wbr";
    const ARIA_LIST = "autocomplete|checked|disabled|expanded|haspopup|hidden|invalid|label|level|multiline|multiselectable|orientation|pressed|readonly|required|selected|sort|valuemax|valuemin|valuenow|valuetext  |live|relevant|atomic|busy|dropeffect|dragged|activedescendant|controls|describedby|flowto|labelledby|owns|posinset|setsize";
    ///<summary></summary>
    ///<param name="eventObj"></param>
    ///<return refout="true"></return>
    /**
     * bind event property.
     * ->on(string $type) : return HtmlEventProperty\
     * ->on(string $type, mixed $value): return chain HtmlNode 
     * @param mixed $eventObj event name
     * @return HtmlNode|HtmlEvenProperty depend of number of argument. 
     */
    public function on($eventObj)
    {
        $c = $this->getFlag(self::EVENTS) ?? array();
        if (isset($c[$eventObj])) {
            $b = $c[$eventObj];
            return $b;
        }
        $b = HtmlEventProperty::CreateEventProperty($eventObj);
        $c[$eventObj] = $b;
        $this->setFlag(self::EVENTS, $c);
        if (func_num_args() > 1) {
            $b->content = func_get_args()[1];
            return $this;
        }
        return $b;
    }
    public function addNode($name)
    {
        if ($this->getCanAddChilds()) {
            $p = new HtmlNode($name);
            return $this->add($p);
        }
    }

    /**
     * set aria attribute
     * @param string $type aria types
     * @param mixed $value 
     * @return static 
     */
    public function setAria(string $type, $value){
        static $arias = null;
        if ($arias === null ){
            $arias = explode("|", self::ARIA_LIST);
        }
        if (!in_array($type, $arias)){
            throw new IGKException(__("Aria not found in aria list"));
        }
        $this->setAttribute("aria-".$type, $value);
        return $this;
    }
    public function addRange(array $childs)
    {
        if ($this->getCanAddChilds()) {
            array_map([$this, "add"], $childs);
        }
        return $this;
    }
    /**
     * force text content
     * @param string $text 
     * @return $this 
     */
    public function setTextContent(string $text){
        $this->content = $text;
        return $this;
    }
    ///<summary>set the class combination of this item</summary>
    /**
     * set the class combination of this item
     */
    public function setClass($value)
    {
        $this["class"] = $value;
        return $this;
    }
    /**
     * clear class
     * @return $this 
     */
    public function clearClass()
    {

        $this["class"] = null;
        return $this;
    }

    public function clear()
    {
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * set or append style to 
     * @param string|array $value
     */
    public function setStyle($value)
    {
        if (empty($value))
            return $this;
        if (is_array($value)){
            $value = implode(";", array_filter(array_map(function($a,$k){
                if (is_numeric($k)) return null;
                return implode(":", [$k, $a]);
            }, $value, array_keys($value))));
        }

        if (0 === strpos($value, '+/')) {
            $s = $this["style"]."";
            $value = implode(';', array_filter([rtrim($s, ";"), substr($value, 2)])); 
        }
        $this["style"] = new HtmlCssValueAttribute($value);
        return $this;
    } 
    public function __call($n, $arguments)
    {
        if (in_array(strtolower($n), ["address"])) {
            if ($this->getCanAddChilds()) {
                $tab = array(strtolower($n), null, $arguments);
                return call_user_func_array([$this, IGK_ADD_PREFIX], $tab);
            }
        }
        return parent::__call($n, $arguments);
    }
    /*
    address have a special meaning
    public function address(){
        if ($this->getCanAddChilds()){
            $c = new HtmlNode("address");
            $this->add($c);
            return $c;
        }
    }
    */

    ///<summary></summary>
    /**
     * 
     */
    public function getChildCount()
    {
        return $this->getChilds()->count();
    }
    /**
     * assert class
     * @param mixed $condition 
     * @param mixed $value 
     * @return $this 
     */
    public function setAssertClass($condition, $value)
    {
        if ($condition) {
            $this->setClass($value);
        }
        return $this;
    }
    ///<summary>set the id of this item</summary>
    /**
     * set the id of this item
     */
    public function setId($id)
    {
        $this["id"] = $this["name"] = $id;
        return $this;
    }
    public function __construct($tagname = null)
    {
        parent::__construct();
        if ($tagname !== null)
            $this->tagname = $tagname;
        $this->initialize();
    }
    /**
     * initialize this node
     * @return void 
     */
    protected function initialize()
    {
    }
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    ///<param name="context" default="null"></param>
    /**
     * 
     * @param mixed $key
     * @param mixed $value
     * @param mixed $context the default value is null
     */
    public function setSysAttribute($key, $value, $context = null)
    {
        $eval = false;
        if (($context !== null) && ($value !== null) && (is_string($value))) {
            $tb = array();
            if ((preg_match_all("%\[eval:(?P<value>[^\]]*)]%i", $value, $tb)) > 0) {
                $e = igk_str_read_in_brancket($value, "[", "]");
                $script = substr($e[0], strpos($e[0], ":") + 1);
                if (!empty($script))
                    $value = igk_html_eval_value_in_context($script, $context);
                $eval = true;
            }
        }
        $t = array("style" => "class");
        $m = igk_getv($t, strtolower($key));
        if ($m) {
            $this[$m] = strtolower("+igk-" . $this->getItemType() . "-" . $value);
        } else {
            $k = "set" . $key;
            if (method_exists($this, $k)) {
                $this->$k($value);
            } else {
                if ($eval){
                    $this[$key] = $value;             
                    return $this;
                }
                // igk_wln_e("set ".$key, $value);
                // $cond = igk_server_is_local() && (($context !== null) && ($context !== 'Load'));               
                // igk_assert_die($cond, "/!\\ Method not define [". $key. "] :::".$value. " :::".get_class($this). "::::Context[".$context."]");
                return false;
            }
        }
        return $this;
    }

    public function setProperty($name, $value)
    {
        $n = igk_getv($this, $name);
        $this->$name = $value;
        if ($n != $value) {
            // + | ------------------------------------------
            // + | dom property changed
            // + | 
            igk_hook(\IGKEvents::HOOK_DOM_PROPERTY_CHANGED, [
                "node" => $this,
                "name" => $name,
                "new" => $value,
                "old" => $n
            ]);
        }
        return $this;
    }
    public function __set($n, $v)
    {
        parent::__set($n, $v);
    }
    protected function _access_offsetExists($n)
    {
        return isset($this->m_attributes[$n]);
    }

    protected function _access_OffsetSet($k, $v)
    {
        if ($v === null) {
            unset($this->m_attributes[$k]);
        } else {
            switch ($k) {
                case "class":
                    if ($v === null) {
                        unset($this->m_attributes[$k]);
                    } else {
                        if (!($cl = igk_getv($this->m_attributes, $k))) {
                            $cl = new HtmlCssClassValueAttribute();
                            $this->m_attributes[$k] = $cl;
                        }
                        $cl->add($v);
                    }
                    break;
                case "style":
                    if (!($cl = igk_getv($this->m_attributes, $k))) {
                        $cl = new HtmlStyleValueAttribute($this);
                    }
                    $cl->setValue($v);
                    $this->m_attributes[$k] = $cl;
                    break;
                default:
                    if (strpos($k, 'igk:') === 0) {
                        $ck = substr($k, 4);

                        if (!HtmlOptions::IsAllowedAttribute($ck)) {
                            return;
                        }
                        if (!$this->setSysAttribute($ck, $v, $this->getLoadingContext())) {
                            $this->offsetSetExpression($k, $v);
                        }
                    } else {
                        $this->m_attributes[$k] = $v;
                    }
                    break;
            }
        }
        return $this;
    }

    ///<summary></summary>
    ///<param name="key">the key of expression to set</param>
    ///<param name="value">value to evaluate</param>
    ///<remark >every expression key must start with '@igk:expression' name or value will be set to default </summary>
    /**
     * 
     * @param mixed $key
     * @param mixed $value
     */
    function offsetSetExpression($key, $value)
    {
        if (preg_match("/^@igk:expression/", $key)) {
            if ((($g = $this->getAttributes()) !== null) || (($g = $this->_initattribs()) !== null)) {
                if ($value === null)
                    unset($g[$key]);
                else
                    $g[$key] = new HtmlExpressionAttribute($value);
                $this->_f->updateFlag(self::ATTRIBS, $g);
            }
            return $this;
        }
        return $this->Set($key, $value);
    }
    public function Set($key, $value)
    {
        $this->m_attributes[$key] = $value;
        return $this;
    }

    public function getCanRenderTag()
    {
       
        if ($this->iscallback(__FUNCTION__)) {
            $this->evalCallback(__FUNCTION__, $output);
            return $output;
        }
        return parent::getCanRenderTag(func_get_arg(0));
    }

    public function activate($n)
    {
        $this->m_attributes->activate($n);
        return $this;
    }
    public function deactivate($n)
    {
        $this->m_attributes->deactivate($n);
        return $this;
    }

    /**
     * @return bool get if close tag
     */
    public function closeTag()
    {
        $closeTags = HtmlContext::getCloseTagArray();
        $n = $this->tagname;
        return in_array($this->tagname, $closeTags) || !in_array($n, $this->_nodeList());
    }
    /**
     * get html5 node list
     * @return string[]|false 
     */
    private function _nodeList()
    {
        static $nlist;
        if ($nlist === null) {
            $nlist = explode('|', self::NODE_LIST);
        }
        return $nlist;
    }
}
