<?php
// @file: HtmlReader.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;

require_once IGK_LIB_DIR . "/igk_html_utils.php";
require_once IGK_LIB_CLASSES_DIR . '/System/Html/Dom/HtmlProcessInstructionNode.php';


use Exception;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\XML\XMLExpressionAttribute;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Html\Dom\HtmlCommentNode;
use IGK\System\Html\Dom\HtmlDoctype;
use IGK\System\Html\Dom\HtmlProcessInstructionNode;
use IGK\System\Html\HtmlTemplateReaderDataBinding;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\XML\XmlNode;
use IGK\System\IO\StringBuilder;
use IGK\XML\XMLNodeType;
use IGKException;
use IGKObject;
use function igk_resources_gets as __;


final class HtmlReader extends IGKObject
{
    const EXPRESSION_ARGS = "[[:@raw]], [[:@ctrl]]";
    const ARGS_ATTRIBUTE = "igk:args";
    const READ_XML =  "XML";
    const READ_HTML = "HTML";
    const LOAD_EXPRESSION = "LoadExpression";
    private $m_attribs, $m_context, $m_contextLevel, $m_hasAttrib, $m_hfile, $m_isEmpty, $m_mmodel, $m_name, $m_nodes, $m_nodetype, $m_offset, $m_procTagClose, $m_resolvKeys, $m_resolvValues, $m_text, $m_v;
    private static $sm_ItemCreatorListener, $sm_openertype = [];
    private $m_length;
    static $ss;

    /**
     * return the reader open type
     * @return mixed 
     */
    public static function GetOpenerContext()
    {
        $c = count(self::$sm_openertype);
        return $c > 0 ? self::$sm_openertype[$c - 1] : null;
    }

    ///<summary>bind template object</summary>
    private static function _BindTemplate($reader, &$cnode, $template)
    {
        $engine = "";
        if (is_array($template)) {
            $src = $template["content"];
            $data = $template["context-data"];
            $ctrl = isset($reader->m_context->ctrl) ? $reader->m_context->ctrl : null;
            $n_context = ["scope" => 0, "contextlevel" => 1, "fname" => "__memory__", "data" => null];

            if ($template["operation"] == "loop") {
                $n_options = (object)["Indent" => 0, "Depth" => 0, "Context" => "html", "RContext" => $n_context, "ctrl" => $ctrl];
                igk_set_env("sys:://expression_context", $n_options);
                if ($cnode->getIsVisible()) {
                    if ($data) {
                        $v_bind = new HtmlTemplateReaderDataBinding($cnode, $src, $ctrl, $data);
                        $v_ts = $v_bind->treat(); 
                        if ($reader->m_context->transformToEval )
                        {
                            $sb = new StringBuilder;
                            $sb->appendLine('<?php');
                            $sb->append("foreach(\$context_raw as \$index=>\$raw){");
                            $sb->append($v_ts);
                            $sb->appendLine("<php } ?>");
                            $v_ts = $sb;


                        } 
                        $engine .= $v_ts;
                        
                        //igk_debug_wln_e("binding data ", $data, $n_context, $src, $engine);
                    }
                }
                igk_set_env("sys:://expression_context", null);
            } else {
                igk_die(__("Operation not handle : {0}", $template["operation"]));
            }
        }
        $gnode = $cnode->getParentNode();
        if ($gnode && !empty($engine)) {
            $cnode->remove();
            // $gnode->remove($cnode);
            $v = igk_create_notagnode();
            $v->addText($engine);
            $gnode->add($v);
            $cnode = $v;
        }
        return 1;
    }
    ///<summary></summary>
    ///<param name="text"></param>
    private function __construct($text)
    {
        $this->m_text = $text;
        $this->m_offset = 0;
        $this->m_contextLevel = 0;
        $this->m_nodetype =  XMLNodeType::NONE;
        $this->m_resolvKeys = array();
        $this->m_resolvValues = array();
        $this->m_attribs = null;
        $this->m_nodes = array();
        $this->m_length = strlen($text);   
    }
    ///<summary></summary>
    ///<param name="text"></param>
    ///<param name="offset" ref="true"></param>
    ///<param name="start" default="'['"></param>
    ///<param name="end" default="']'"></param>
    private static function __readBracket($text, &$offset, $start = '[', $end = ']')
    {
        $c = 0;
        $m = "";
        $ct = strlen($text);
        while (($offset < $ct)) {
            $ch = $text[$offset];
            $m .= $ch;
            $offset++;
            if ($ch == $end) {
                if ($c == 0) {
                    break;
                }
                $c--;
            } else if ($ch == $start) {
                $c++;
            }
        }
        return $m;
    }
    ///<summary></summary>
    ///<param name="reader"></param>
    ///<param name="text"></param>
    ///<param name="offset" ref="true"></param>
    ///<param name="tag"></param>
    private static function __readSkipContent($reader, $text, &$offset, $tag)
    {
        $ln = strlen($text);
        $o = "";
        $v = "";
        $level = 0;
        $end = 1;
        $tpos = 0;
        $intag = 0;
        $replace_expression = 1;
        $read_name = function ($text, $ln, &$offset) {
            $name = "";
            while ($offset < $ln) {
                $ch = $text[$offset];
                if (strpos(IGK_IDENTIFIER_TAG_CHARS, $ch) === false) {
                    $offset--;
                    break;
                }
                $name .= $ch;
                $offset++;
            }
            return $name;
        };
        $tnames = [$tag];
        while ($end && ($offset < $ln)) {
            $ch = $text[$offset];
            switch ($ch) {
                case ">":
                    $v .= $ch;
                    if (($end == 2) && ($level == 0)) {
                        $end = 0;
                    }
                    $intag = 0;
                    break;
                case "/":
                    $v .= $ch;
                    if (($offset + 1 < $ln) && (($tch = $text[$offset + 1]) == ">")) {
                        $v .= $tch;
                        $offset++;
                        $level--;
                        $intag = 0;
                        array_pop($tnames);
                    }
                    break;
                case "<":
                    if ($intag) {
                        igk_die("xml reading : enter tag not valid: " . $offset);
                    }
                    $intag = 1;
                    $tpos = strlen($v);
                    $v .= $ch;
                    $tch = null;
                    ($offset + 1 < $ln) && (($tch = $text[$offset + 1]));
                    switch ($tch) {
                        case "/": {
                                $v .= $tch;
                                $offset += 2;
                                $name = $read_name($text, $ln, $offset);
                                if (empty($name) || (($tmix = array_pop($tnames)) != $name)) {
                                    igk_die("xml reading not valid : " . $tmix . " # " . $name . " level " . $level);
                                }
                                $v .= $name;
                                if (($level == 0) && ($name == $tag)) {
                                    $end = 2;
                                } else {
                                    $level--;
                                }
                            }
                            break;
                        case "!":
                            $start = $offset;
                            //+ Skip comment

                            if (($pp = strpos($text, "-->", $offset)) !== null) {
                                $offset = $pp + 3;
                            }
                            $intag = 0;
                            break;
                        default:
                            $offset++;
                            $name = $read_name($text, $ln, $offset);
                            if (empty($name)) {
                                igk_wln_e(__FILE__ . ":" . __LINE__, "start tag is not a valid start tag", igk_html_wtag("textarea", $v . substr($text, $offset, 10) . "...\n-----------\n" . $text), "name is empty : offset : " . $offset . " tag  : " . $tag . " level: " . $level);
                            }
                            array_push($tnames, $name);
                            $level++;
                            $v .= $name;
                            break;
                    }
                    break;
                case "'":
                case '"':
                    $v .=  igk_str_read_brank($text, $offset, $ch, $ch, null, 1);
                    break;
                case '{':
                case '@':
                    if (!$intag) {
                        if ($reader->GetStringContext() == HtmlContext::Html) {
                            if (self::__replaceDetectedExpression($reader, $text, $v, $offset, $replace_expression, 1)) {
                                $offset--;
                                break;
                            }
                        }
                    }
                    $v .= $ch;
                    break;
                default:
                    $v .= $ch;
                    break;
            }
            $offset++;
        }
        $v = substr($v, 0, $tpos);
        if (($intag) || (count($tnames) > 0)) {
            igk_die("failed to read data");
        }
        return $v;
    }
    ///<summary>read text content</summary>
    /**
     * read text  
     * @param string $prefix 
     * @return bool 
     * @throws IGKException 
     */
    private function __readTextValue(string $prefix = "")
    {
        $_pre = ($this->m_name == 'pre') && ($this->m_nodetype == 1);
        $_cread = 1;
        $replace_expression = 1;
        $this->m_name = null;
        $ch = null;
        $v = $prefix;
        $space = 0;
        while ($_cread && $this->CanRead()) {
            $ch = $this->m_text[$this->m_offset];
            if (!$_pre && $ch == ' ') {
                if ($space) {
                    $this->m_offset++;
                    continue;
                } else {
                    $space = 1;
                }
            } else {
                $space = 0;
            }
            switch ($ch) {
                case '<':
                    $_cread = 0;
                    break 2;
                case '@':
                case '{':
                case IGK_EXPRESSION_ESCAPE_MARKER:
                    if ($this->GetStringContext() == HtmlContext::Html) {
                        if (self::__replaceDetectedExpression($this, $this->m_text, $v, $this->m_offset, $replace_expression, 0)) {
                            break 2;
                        }
                    }
                    break;
            }
            $this->m_offset++;
            $v .= $ch;
        }
        if (($v == '0') || !empty($v)) {
            $this->_setText($v);
            return true;
        }
        return false;
    }
    /**
     * get the string context
     * @throws Exception not a valid context
     */
    public function GetStringContext()
    {
        if (!$this->m_context) {
            return HtmlContext::Html;
        }
        if (is_string($this->m_context)) {
            return $this->m_context;
        }
        return igk_getv($this->m_context, "Context", HtmlContext::Html);
    }
    ///<summary>replace data binding expression</summary>
    ///<param name="reader"></param>
    ///<param name="text"></param>
    ///<param name="v" ref="true"></param>
    ///<param name="offset" ref="true"></param>
    ///<param name="replace_expression" default="1"></param>
    ///<param name="skip" default="1"></param>
    private static function __replaceDetectedExpression($reader, $text, &$v, &$offset, $replace_expression = 1, $skip = 1)
    {
        // if (!is_object($reader->m_context) || !igk_getv($reader->m_context, "load_expression")){
        if (!is_object($reader->m_context)) { //  || !igk_getv($reader->m_context, "load_expression")){
            return false;
        }
        if ($c = preg_match(IGK_TEMPLATE_EXPRESSION_REGEX, $text, $tab, PREG_OFFSET_CAPTURE, $offset)) {
            if ($offset == $tab[0][1]) {
                if ($replace_expression) {
                    $sdata = "";
                    if ($skip) {
                        $sdata = \igk_html_wtag(IGK_ENGINE_EXPRESSION_NODE, "", ["expression" => str_replace("\"", "\\\"", htmlentities($tab[0][0])), self::ARGS_ATTRIBUTE => self::EXPRESSION_ARGS], 1);
                        // igk_wln_e(__FILE__.":".__LINE__, "is skip");
                    } else {
                        $n_context = $reader->m_context;
                        $_e = $tab[0][0];
                        $_b = 0;
                        while ($_e[0] == "@") {
                            $_e = substr($_e, 1);
                            $_b = 1;
                        }
                        if (!isset($n_context->raw)) {
                            igk_die("raw not defined in reading context");
                        }
                        
                        $sdata = igk_html_databinding_treatresponse($_e, $n_context->ctrl, 
                            igk_get_attrib_raw_context($n_context), null, $_b, 
                            $n_context->transformToEval
                        );
                    }
                    $v .= $sdata;
                } else {
                    $v .= $tab[0][0];
                }
                $offset += strlen($tab[0][0]);
                return true;
            }
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="cnode"></param>
    ///<param name="n"></param>
    ///<param name="tab"></param>
    ///<param name="args"></param>
    private function _addNode($cnode, $n, $tab, $args)
    {
        $g = explode(':', $n);
        if (igk_count($g) == 1) {
            $v = self::CreateNode($n, $args);
            if (!$cnode->add($v) && !self::_AddToParent($tab, $cnode, $v)) {
                $b = $tab->add($v);
            } else {
                $b = $v;
            }
            return $b;
        }
        if (($k = call_user_func_array(array($cnode, IGK_ADD_PREFIX . $g[1]), $args != null ? $args : array())) && ($k !== $cnode))
            $this->_appendResolvNode($n, $k, $cnode);
        return $k;
    }
    ///<summary></summary>
    ///<param name="topnode"></param>
    ///<param name="cnode"></param>
    ///<param name="node"></param>
    private static function _AddToParent($topnode, $cnode, $node)
    {
        $p = $cnode->ParentNode;
        while ($p && ($p !== $topnode)) {
            if ($p->add($node) !== null) {
                return 1;
            }
            $p = $p->ParentNode;
        }
        return 0;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="k"></param>
    ///<param name="cnode"></param>
    private function _appendResolvNode($n, $k, $cnode)
    {
        if ($k) {
            $this->m_resolvKeys[] = $n;
            $this->m_resolvValues[] = $k;
            $this->m_nodes[] = $cnode;
        }
    }
    ///<summary></summary>
    ///<param name="reader"></param>
    ///<param name="cnode"></param>
    ///<param name="name"></param>
    ///<param name="tab_doc"></param>
    ///<param name="pargs"></param>
    private static function _BuildNode(HtmlReader $reader, $cnode, $name, $tab_doc, $pargs)
    {
        if ((self::GetOpenerContext() == self::READ_XML) || (isset($reader->context) && ($reader->context == HtmlContext::XML))) {
            // $n = self::CreateNode($name, $pargs);
            igk_debug_wln_e("create expression node : ", "igk:expression-node");
            $n = new XmlNode($name);
            if ($pargs) {
                $n->setAttributes($pargs);
            }
            if ($cnode) {
                $cnode->add($n);
            }
            return $n;
        }
        return ($cnode !== null) ? $reader->_addNode($cnode, $name, $tab_doc, $pargs) : self::CreateNode($name, $pargs);
    }
    ///<summary></summary>
    ///<param name="cnode">node to close</param>
    ///<param name="tag">tag name that referrer to </param>
    private function _LoadComplete($cnode, $tag, $peekData = null)
    {
        $m = (strtolower($cnode->tagName) == strtolower($tag));
        if ($m) {
            $p = $cnode->getParentNode();
            $cnode->loadingComplete();
            return $p;
        }
        $c = count($this->m_resolvKeys);
        if ($peekData && ($c > 0)) {
            $n = count($this->m_resolvKeys) > 0 ? $this->m_resolvKeys[$c - 1] : null;
            $d = count($this->m_resolvValues) > 0 ? $this->m_resolvValues[$c - 1] : null;
            if (!$d && (strtolower($n) != strtolower($tag))) {
                igk_wln_e($c, "failed to relov : " . $tag . " VS :::" . $n . " === " . $cnode->tagName);
            }
            $cnode->loadingComplete();
            $pnode = $cnode->getParentNode();
            if ($pnode === $d) {
                $pnode->loadingComplete();
                $pnode = $d->getParentNode();;
            } else {
                while ($pnode && ($pnode !== $d)) {
                    $pnode->loadingComplete();
                    $pnode = $pnode->getParentNode();
                }
                if ($pnode === $d) {
                    $pnode = $d->getParentNode();
                }
            }
            array_pop($this->m_resolvKeys);
            array_pop($this->m_resolvValues);
            $peekData->closing = true;
            return $pnode;
        }
        $n = count($this->m_resolvKeys) > 0 ? $this->m_resolvKeys[count($this->m_resolvKeys) - 1] : null;
        $d = count($this->m_resolvValues) > 0 ? $this->m_resolvValues[count($this->m_resolvValues) - 1] : null;

        // if (($n==null) || ($tag==null)){
        //     igk_wln_e("lower no d : ", compact("n", "tag", "d", "cnode"));
        // }

        if ($n && $d && (strtolower($n) == strtolower($tag)) && ($d === $cnode)) {
            array_pop($this->m_resolvKeys);
            array_pop($this->m_resolvValues);
        }
        $cnode->loadingComplete();
        $b = array_pop($this->m_nodes);
        $pnode = $cnode->getParentNode();
        if (($b !== null) && ($pnode !== $b)) {
            $pnode = $b;
        }
        $cnode = $pnode;
        return $cnode;
    }
    ///<summary>read attribute</summary>
    ///<param name="reader"></param>
    ///<param name="v" ref="true"></param>
    ///<param name="attribs" default="[[]" ref="true"></param>
    ///<param name="callback" default="null"></param>
    /**
     * 
     * @param HtmlReader $reader 
     * @param mixed $v 
     * @param array $attribs 
     * @param mixed $callback 
     * @return bool 
     * @throws IGKException 
     */
    private static function _ReadAttributes(self $reader, &$v, &$attribs = [], $callback = null)
    {
        // start reading attribute name
        $mode = 0;
        $v_n = "";
        $v_v = "";
        $end = false;
        $v_skip = false;
        $protag = 0;
        $v_sv = "";
        $escape = false;
        $pro_expr = "";
        $expr_attrib = false;
        while (!$end && $reader->CanRead()) {
            $v_ch = $reader->m_text[$reader->m_offset];
            $reader->m_offset++;
            $v .= $v_ch;
            if ($protag == 2) {
                switch ($v_ch) {
                    case ">":
                        if (substr($v_v, -1) == "?") {
                            $protag = 0;
                        }
                        break;
                }
                if ($mode == 2) {
                    $v_v .= $v_ch;
                } else {
                    die("syntax error mode protag");
                }
                continue;
            } else if ($protag == 1) {
                if ($v_ch == '?') {
                    if ($mode == 2) {
                        $v_v .= $v_ch;
                        $expr_attrib = true;
                    } else {
                        $pro_expr .= $v_ch;
                    }
                    $protag = 2;
                    continue;
                }
                $protag = 0;
            }
            switch ($v_ch) {
                case '\\':
                    if ($mode == 2) {
                        $v_v .= $v_ch;
                        $escape = true;
                    }
                    break;
                case "'":
                case "\"":
                    if ($mode == 0) {
                        igk_wln($attribs);
                        die("not valid attribute read not vaid: [$v] - {$v_n} - $mode");
                    }
                    // if (igk_is_debug()){
                    //     echo __FILE__.":".__LINE__. "debug ing";
                    // }
                    $start = $reader->m_offset;
                    $escaped = false;
                    while ($c_pos = strpos($reader->m_text, $v_ch, $start)) {
                        $start = $c_pos;
                        if ($reader->m_text[$c_pos - 1] == "\\") {
                            // is escaped
                            $escaped = true;
                            $start++;
                            continue;
                        }
                        break;
                    }
                    if ($c_pos === false) {
                        die("close bracket not found");
                    }
                    $v_v =  substr($reader->m_text, $reader->m_offset, -$reader->m_offset + $c_pos);
                    if ($escaped) {
                        $v_v = stripslashes($v_v);
                    }


                    $reader->m_offset = $start + 1;
                    $attribs[$v_n] = $v_v;
                    if ($callback) {
                        if ($expr_attrib) {
                            $v_v = new XMLExpressionAttribute($v_v);
                        }
                        $callback($v_n, $v_v);
                    }
                    $v_n = "";
                    $v_v = "";
                    $v_sv = "";
                    $mode = 1;
                    break;
                case "<":
                    if ($protag != 0)
                        die("protag not valid");
                    $protag = 1;
                    if ($mode == 2) {
                        $v_v .= $v_ch;
                    } else {
                        igk_trace();
                        die("syntax error: expression tag not allowed in attribute definition: $mode " . $v);
                    }
                    break;
                case "=":
                    if (($mode == 0) || ($mode == 1)) {
                        $mode = 2;
                        $v_v = "";
                    }
                    break;
                case ">":
                    //+ attempt to close tag 
                    $end = true;
                    $v = substr($v, 0, -1);
                    if (substr($v, -1) == "/") {
                        $v = substr($v, 0, -1);
                        $reader->m_isEmpty = true;
                    } else {
                        if ($reader->m_context == HtmlContext::Html) {
                            // special closing tag
                            if (in_array($reader->m_name, HtmlContext::GetEmptyTagArray())) {
                                $reader->m_isEmpty = true;
                            }
                        }
                    }

                    break;
                default:
                    if ($mode == 0) {
                        if (!empty(trim($v_ch))) {
                            $v_n .= $v_ch;
                        } else {
                            if (!empty($v_n)) {
                                $attribs[$v_n] = true;
                                if ($callback) {
                                    $callback($v_n, true);
                                }
                                $v_n = "";
                            } else {
                                $mode = 1;
                            }
                        }
                    } else if ($mode == 1) {
                        if (!empty(trim($v_ch))) {
                            if (!empty($v_n)) {
                                $attribs[$v_n] = true;
                                if ($callback) {
                                    $callback($v_n, true);
                                }
                            }
                            $mode = 0;
                            $v_n = $v_ch;
                        }
                    } else if ($mode == 2) {
                        $v_v .= $v_ch;
                    }
                    break;
            }
        }
        return $end;
    }
    ///<summary>read the model</summary>
    ///<param name="context">name of the function that call the read model</param>
    /**
     * 
     * @param HtmlReader $reader 
     * @param mixed $tab_doc 
     * @param string $caller_context from Load|LoadExpression
     * @return void 
     * @throws IGKException 
     */
    private static function _ReadModel(self $reader, $tab_doc, ?string $caller_context = null)
    {
        /**
         * @var ?HtmlNode $cnode
         */
        $cnode = null;
        $pnode = null;
        self::_PushContext(($reader->m_context != null) ? $reader->m_context : self::READ_XML);

        // + | }
        $v_tags = array();
        $_shift_setting = function ($n, $cnode, &$v_tags, &$krsv) {
            if (igk_count($v_tags) <= 0)
                return;
            $s = array_shift($v_tags);
            if ($s->clName == $n) {
                if ($cnode === $s->item) {
                    $krsv = false;
                } else
                    array_unshift($v_tags, $s);
            }
        };


        while ($reader->Read()) {

            switch ($reader->NodeType) {
                case XMLNodeType::ELEMENT:
                    $name = $reader->Name();
                    if (empty($name)) {
                        break;
                    }
                    $cattr = $reader->Attribs();

                    if ($caller_context == self::LOAD_EXPRESSION) {
                        $v_tn = new HtmlNode($name);
                        $v_tn->startLoading(__CLASS__, $caller_context);
                        if ($reader->HasAttrib()) {
                            foreach ($cattr as $k => $c) {
                                $v_tn->offsetSetExpression($k, $c);
                            }
                        }
                        if ($cnode == null) {
                            $tab_doc->add($v_tn);
                        } else {
                            $cnode->add($v_tn);
                            if ($v_tn->ParentNode !== $cnode) {
                                $ht = $cnode->ParentNode;
                                while ($ht) {
                                    if ($ht->add($v_tn)->ParentNode === $ht)
                                        break;
                                    $ht = $ht->ParentNode;
                                }
                                if ($ht === null) {
                                    $tab_doc->add($v_tn);
                                } else
                                    igk_die("failed " . ($ht === null));
                            }
                        }
                        $cnode = $v_tn;
                        if ($reader->IsEmpty()) {
                            $cnode = $reader->_LoadComplete($cnode, $name);
                            if ($cnode == $tab_doc) {
                                $cnode = null;
                                $reader->m_nodes = array();
                            }
                        }
                    } else {
                        // + | loading context
                        $template = igk_getv($cattr, IGK_ENGINE_ATTR_TEMPLATE_CONTENT);
                        if ($template) {
                            $cattr[IGK_ENGINE_ATTR_TEMPLATE_CONTENT] = null;
                        }
                        $pargs = igk_engine_get_attr_arg(igk_getv($cattr, self::ARGS_ATTRIBUTE), $reader->m_context);
                        $v_tn = self::_BuildNode($reader, $cnode, $name, $tab_doc, $pargs);

                        if ($v_tn) {
                            // igk_debug_wln("\n data : ".get_class($v_tn) , "\nEmptyTag:". $v_tn->isEmptyTag(). " : ". $v_tn->tagName ." vs " .$name. "? ".$reader->IsEmpty()."\n");
                            if ($v_tn->tagName && ($v_tn->tagName != $name) && !$reader->IsEmpty()) {
                                array_unshift($v_tags, (object)array(IGK_FD_NAME => $name, "item" => $v_tn));
                                if ($cnode == null)
                                    $reader->_appendResolvNode($name, $v_tn, $cnode);
                            }
                            $v_tn->startLoading(__CLASS__, $caller_context);
                            if ($reader->HasAttrib()) {
                                foreach ($cattr as $k => $c) {
                                    if ($k == self::ARGS_ATTRIBUTE)
                                        continue;
                                    if (self::GetOpenerContext() == self::READ_XML) {
                                        $v_tn->setAttribute($k, $c);
                                    } else
                                        $v_tn[$k] = $c;
                                }
                            }
                            if ($cnode == null) {
                                $tab_doc->add($v_tn);
                            }
                            $cnode = $v_tn;
                            if ($template) {
                                self::_BindTemplate($reader, $cnode, $template, $caller_context);
                                $gc = $reader->_LoadComplete($cnode, $cnode->tagName);
                                $cnode = $gc;
                                break;
                            }
                            if ($reader->IsEmpty() && $cnode) {

                                $cnode = $reader->_LoadComplete($cnode, $name);
                                if ($cnode === $tab_doc) {
                                    $cnode = null;
                                    $reader->m_nodes = array();
                                }
                            }
                        } else {
                            $reader->Skip();
                        }
                    }
                    break;
                case XMLNodeType::TEXT:
                    $v_sr = $reader->getValue() . "";
                    if (strlen($v_sr) > 0) {
                        $v_sr = preg_replace("/\s+/", " ", $v_sr);
                        if ($cnode) {
                            if ($cnode->isEmptyTag()) {
                                $txt = new HtmlTextNode($v_sr);
                                $cnode->parentNode->add($txt);
                                $cnode = $cnode->parentNode;
                            } else {
                                if ($cnode->getTempFlag("replaceContentLoading") || (($cnode->Content == "") && !$cnode->HasChilds))
                                    $cnode->Content = $v_sr;
                                else {
                                    $txt = new HtmlTextNode($v_sr);
                                    $cnode->add($txt);
                                }
                            }
                        } else {
                            $v = new HtmlTextNode($v_sr);
                            $tab_doc->add($v);
                        }
                    }
                    break;
                case XMLNodeType::INNER_TEXT:
                    // must set as inner text
                    if (!$cnode){
                        igk_trace();
                        echo $reader->m_text;
                        die("can't set inner text on non detected node");
                    }
                    if (!empty($g = $reader->getValue())){
                        $cnode->setTextContent($g);
                    }
                    $reader->NodeType = XMLNodeType::ENDELEMENT;
                    $cnode = $cnode->getParentNode();
                    break;
                case XMLNodeType::COMMENT:
                    $v_v = $reader->getValue();
                    $v = new HtmlCommentNode($v_v);
                    if (!$cnode) {
                        $tab_doc->add($v);
                    } else {
                        $cnode->add($v);
                    }
                    break;
                case XMLNodeType::CDATA:
                case XMLNodeType::DOCTYPE;
                    $v = self::CreateElement($reader->NodeType);
                    $v->Content = $reader->getValue();
                    if (!$cnode) {
                        $tab_doc->add($v);
                    } else {
                        $cnode->add($v);
                    }
                    break;
                case XMLNodeType::ENDELEMENT:
                    $n = $reader->Name();
                    // $tnode = $cnode;
                    if ($cnode) {
                        $t = $cnode->TagName;
                        if ($caller_context == self::LOAD_EXPRESSION) {
                            if ($n == $t) {
                                $cnode = $reader->_LoadComplete($cnode, $n);
                            } else {
                                $rsv = false;
                                $krsv = true;
                                $_shift_setting($n, $cnode, $v_tags, $krsv);
                                while ($cnode && ($cnode->TagName != $n)) {
                                    $cnode = $reader->_LoadComplete($cnode, $n);
                                    $rsv = true;
                                }
                                if (!$krsv && $cnode) {
                                    $cnode = $reader->_LoadComplete($cnode, $n);
                                } else {
                                    igk_die("[Bad Html structure] can't get parent, cnode is null, name : $n  , tagName : $t  ?  <br/>\n" . "Line : " . __LINE__ . "<br /><br />" . IGK_LF . "<br /><div >Context:" . $caller_context . "</div>" . " : " . get_class($cnode));
                                }
                            }
                            if ($cnode == $tab_doc) {
                                $cnode = null;
                                $reader->m_nodes = array();
                            }
                        } else {
                            if (($n == $t) || $cnode->isCloseTag($n) || $reader->IsResolved($cnode, $n)) {
                                $cnode = $reader->_LoadComplete($cnode, $n);
                            } else {
                                $rsv = false;
                                $krsv = true;
                                $pnode = $cnode;
                                $kclosing = true;
                                $peek = null;
                                while ($kclosing && $pnode) {
                                    if (igk_count($v_tags) > 0) {
                                        $peek = $v_tags[0];
                                        if (($peek->clName == $n) && ($peek->item === $pnode)) {
                                            // igk_dev_wln_e(__FILE__ . ':' . __LINE__, 
                                            //     "html reader error: closing found. but not match parent : ".$n);
                                            array_shift($v_tags);
                                            $kclosing = 0;
                                        } else {
                                            if (!$pnode->isEmptyTag() && ($pnode->TagName == $n)) {
                                                $kclosing = 0;
                                            }
                                        }
                                    }
                                    $pnode = $reader->_LoadComplete($pnode, $n, $peek);
                                    if ($peek && ($pnode === $peek->item)) {
                                        array_shift($v_tags);
                                        $kclosing = 0;
                                    }
                                }
                                $cnode = $pnode;
                            }
                            if ($cnode == $tab_doc) {
                                $cnode = null;
                                $reader->m_nodes = array();
                            }
                        }
                    }
                    break;
                case XMLNodeType::PROCESSOR: {
                        $v = $reader->getValue();
                        $v_cnode = new HtmlProcessInstructionNode($v, $reader->procTagClose());
                        if ($cnode == null) {
                            $tab_doc->add($v_cnode);
                        } else {
                            $cnode->add($v_cnode);
                        }
                    }
                    break;
            }
        }
        self::_PopContext();
    }
    private static function _ShifSetting($n, $cnode, &$v_tags, &$krsv)
    {
        if (igk_count($v_tags) <= 0)
            return;
        $s = array_shift($v_tags);
        if ($s->clName == $n) {
            if ($cnode === $s->item) {
                $krsv = false;
            } else
                array_unshift($v_tags, $s);
        }
    }
    ///<summary>return iterable definition</summary>
    /**
     * return iterable definition
     * @return null|Iterable 
     */
    public function Attribs()
    {
        return $this->m_attribs;
    }
    ///<summary></summary>
    private function CanRead()
    {
        return (($this->m_offset >= 0) && ($this->m_offset < $this->m_length));
    }
    ///<summary></summary>
    public function Close()
    {
        if ($this->hfile)
            fclose($this->hfile);
        $this->m_text = null;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    public static function Create($file)
    {
        if (is_file($file)) {
            $f = fopen($file, "r");
            if ($f) {
                $c = filesize($file);
                $str = fread($f, $c);
                $reader = new static($str);
                $reader->m_hfile = $f;
                return $reader;
            }
        }
        return null;
    }
    ///<summary>create Element</summary>
    public static function CreateElement($nodetype, $value = IGK_STR_EMPTY)
    {
        $v = null;
        switch ($nodetype) {
            case XMLNodeType::CDATA:
                $v = (new XmlNode("!CDATA"));
                $v->setContent($value);
                break;
            case XMLNodeType::DOCTYPE:
                $v = new HtmlDoctype($value);
                break;
        }
        return $v;
    }
    ///<summary>createnode </summary>
    public static function CreateNode($name, $args = null)
    {
        if (is_callable(self::$sm_ItemCreatorListener)) {
            $fc = self::$sm_ItemCreatorListener;
            return $fc($name, $args);
        }
        if (self::GetOpenerContext() == "XML") {
            return new XmlNode($name);
        }
        return HtmlNode::CreateWebNode($name, $args);
    }
    ///<summary>Represente GetAttributeRegex function</summary>
    public static function GetAttributeRegex()
    {
        $machv = "(?P<value>";
        $machv .= "([\"](([^\"]*(')?(\"\")?)+)[\"])";
        $machv .= "|([\'](([^']*(\")?('')?)+)[\'])";
        $machv .= "|(([^\s]*)+)";
        $machv .= ")";
        $tagRegexLoad = "(?P<name>(" . "([\(])" . IGK_TAGNAME_CHAR_REGEX . "+([\)])" . "|([\\[])" . IGK_TAGNAME_CHAR_REGEX . "+([\\]])" . '|(@|\*(\*)?)?' . IGK_TAGNAME_CHAR_REGEX . '+' . "))";
        return "/" . $tagRegexLoad . "[\s]*=[\s]*(" . $machv . ")/im";
    }
    ///<summary>Create Binding information</summary>
    /**
     * get binding info
     * @return HtmlReaderBindingInfo 
     */
    protected function getBindingInfo()
    {
        $bindinfo = new HtmlReaderBindingInfo($this, function ($k, $v) {
            $this->m_attribs[$k] = $v;
            return $this;
        });
        return $bindinfo;
    }
    ///<summary>Represente getContext function</summary>
    /**
     * get reader loading context.
     * @return mixed 
     */
    public function getContext()
    {
        return $this->m_context;
    }
    ///<summary></summary>
    public function getNodeType()
    {
        return $this->m_nodetype;
    }
    ///<summary></summary>
    public function getSource()
    {
        return $this->m_text;
    }
    ///<summary></summary>
    public function getValue()
    {
        return $this->m_v;
    }
    ///<summary></summary>
    public function HasAttrib()
    {
        return $this->m_hasAttrib;
    }
    ///<summary>get if the current reading node is empty</summary>
    /**
     * get if the current reading node is empty
     * @return mixed 
     */
    public function IsEmpty()
    {
        return $this->m_isEmpty;
    }
    ///<summary></summary>
    ///<param name="node" ref="true"></param>
    ///<param name="tagName"></param>
    private function IsResolved(&$node, string $tagName)
    {
        if (!$node)
            return false;
        $n = count($this->m_resolvKeys) > 0 ? $this->m_resolvKeys[count($this->m_resolvKeys) - 1] : "";
        $d = count($this->m_resolvValues) > 0 ? $this->m_resolvValues[count($this->m_resolvValues) - 1] : null;

        if ((strtolower($n) == strtolower($tagName)) && ($d === $node)) {
            return true;
        }
        if (0 === strpos($tagName, "igk:")) {
            $f = IGKString::Format(IGK_HTML_CLASS_NODE_FORMAT, substr($tagName, 4));
            if (strtolower($f) == strtolower(get_class($node))) {
                return class_exists($f) && !igk_reflection_class_isabstract($f) && igk_reflection_class_extends($f, 'HtmlNode');
            } else {
                if (($h_host = $node->getParentHost()) != null) {
                    $node = $h_host;
                    return $this->IsResolved($node, $tagName);
                }
            }
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="text"></param>
    ///<param name="context" default="null"></param>
    ///<param name="listener" default="null"></param>
    /**
     * 
     */
    public static function Load(string $text, $context = null, $listener = null)
    {
        $tab_doc = null;
        $b_context = false;
        if ((self::GetOpenerContext() === null) && ($context !== null)) {
            self::_PushContext($context);
            $b_context = true;
        }
        self::SetCreator($listener);

        $tab_doc = new HtmlReaderDocument();
        $reader = new static($text);
        $reader->setContext($context);
        self::_ReadModel($reader, $tab_doc, __FUNCTION__);
        if ($b_context) self::_PopContext();
        return $tab_doc;
    }
    ///<summary></summary>
    ///<param name="text"></param>
    ///<param name="context" default="null"></param>
    public static function LoadExpression($text, $context = null)
    {
        $tab_doc = null;
        if (is_string($text)) {
            $tab_doc = new HtmlReaderDocument();
            $reader = new static($text);
            $reader->setContext($context);
            self::_ReadModel($reader, $tab_doc, __FUNCTION__);
        }
        return $tab_doc;
    }
    ///<summary>load the html file</summary>
    public static function LoadFile($file)
    {
        if (is_file($file)) {
            $size = @filesize($file);
            if ($size > 0) {
                try {
                    $hfile = fopen($file, "r");
                } catch (Exception $ex) {
                    // +| failed to open for some reason 
                    igk_ilog($ex->getMessage());
                }
                if ($hfile) {
                    $text = fread($hfile, $size);
                    fclose($hfile);
                    return self::Load($text, null);
                }
            }
            return null;
        }
        igk_die("file : " . $file . " doesn't exist");
    }
    ///<summary> load in xml opening context</summary>
    public static function LoadXML($content)
    {
        self::_PushContext(self::READ_XML);
        $d = self::Load($content);
        self::_PopContext();
        return $d;
    }
    private static function _PushContext($context)
    {
        array_push(self::$sm_openertype, $context);
    }
    private static function _PopContext()
    {
        return array_pop(self::$sm_openertype);
    }
    ///<summary></summary>
    ///<param name="file"></param>
    public static function LoadXMLFile($file)
    {
        self::_PushContext(self::READ_XML);
        $d = self::LoadFile($file);
        self::_PopContext();
        return $d;
    }
    ///<summary></summary>
    public function Name()
    {
        return $this->m_name;
    }
    ///<summary>loading context</summary>
    public function procTagClose()
    {
        return $this->m_procTagClose;
    }
    private function _getAttribuReaderCallback($binfo,  $fc_attrib, &$v_expressions, $context=null){
        $v_context = $context ?? $this->m_context; 
        $v_fc = null;
        $v_self = $this; 

        if ($v_context != HtmlContext::XML) {
            $v_fc = function ($k, $_v) use ($v_self, $binfo, $v_context, $fc_attrib, &$v_expressions) {
                if (preg_match("/^@igk:expression/", $k)) {
                    $v_self->m_attribs[$k] = $v_expressions[HtmlUtils::GetAttributeValue($_v, $v_context)];
                } else {
                    // igk_debug_wln_e(__FILE__.":".__LINE__, $k, $_v);
                    if (!igk_engine_temp_bind_attribute($binfo, $k, $_v, $v_context, $fc_attrib)) {
                        if ((strlen($k) > 2) && preg_match("/^\*\*[^\*]/i", $k)) {
                            //+ |  match double attribute. **test
                            $v_self->m_attribs["[" . substr($k, 2) . "]"] = HtmlUtils::GetAttributeValue($_v, $v_context, true);
                        } else {
                            $v_self->m_attribs[$k] = $_v;
                        }
                    }
                }
            };
        } else {
            $v_fc = function ($k, $v) use ($v_self) {
                $v_self->m_attribs[$k] = $v;
            };
        }
        return $v_fc;
    }
    ///<summary></summary>
    public function Read()
    {
        static $_tagRegexValueRgx = null;
        if (!$this->CanRead()) {
            $this->m_nodetype = XMLNodeType::NONE;
            $this->m_v = null;
            $this->m_name = null;
            $this->m_isEmpty = true;
            $this->m_hasAttrib = false;
            $this->m_attribs = null;
            return false;
        }
        if (($this->m_nodetype == XMLNodeType::ELEMENT) && ($this->m_isEmpty)) {
            $this->m_nodetype = 0;
            $this->m_isEmpty = false;
            $this->m_name = "";
        }
        $v_enter = false;
        $this->m_isEmpty = false;
        $this->m_hasAttrib = false;
        $this->m_attribs = array();
        $v = IGK_STR_EMPTY;
        $v_c = strlen($this->m_text);
        $fc_attrib = function ($k, $v) {
            $this->m_attribs[$k] = $v;
        };
        if ($_tagRegexValueRgx === null) {
            $_tagRegexValueRgx = self::GetAttributeRegex();
        }


        while ($this->CanRead()) {
            $c = $this->m_text[$this->m_offset];
            switch ($c) {
                case "<":
                    $v_enter = true;
                    break;
                case "?":
                    if ($v_enter) {
                        $this->m_offset++;
                        if (self::ReadProcessText($this)) {
                            return true;
                        }
                    } else {
                        return $this->__readTextValue();
                    }
                    return false;
                case "!":
                    if ($v_enter) {
                        if (substr($this->m_text, $this->m_offset + 1, 2) == "--") {
                            $this->m_offset += 3;
                            $v = IGK_STR_EMPTY;
                            while ($this->CanRead()) {
                                $v .= $this->m_text[$this->m_offset];
                                $this->m_offset++;
                                if (substr($v, -3, 3) == "-->") {
                                    $v = substr($v, 0, strlen($v) - 3);
                                    $this->m_name = null;
                                    $this->m_v = $v;
                                    $this->m_nodetype = XMLNodeType::COMMENT;
                                    return true;
                                }
                            }
                        } else if (strtoupper(substr($this->m_text, $this->m_offset + 1, 7)) == "[CDATA[") {
                            $this->m_offset += 8;
                            $v = IGK_STR_EMPTY;
                            while ($this->CanRead()) {
                                $v .= $this->m_text[$this->m_offset];
                                $this->m_offset++;
                                if (substr($v, -3, 3) == "]]>") {
                                    $v = substr($v, 0, strlen($v) - 3);
                                    $this->m_name = null;
                                    $this->m_v = $v;
                                    $this->m_nodetype = XMLNodeType::CDATA;
                                    return true;
                                }
                            }
                        } else if (strtoupper(substr($this->m_text, $this->m_offset + 1, 7)) == "DOCTYPE") {
                            $this->m_offset += 8;
                            $v = IGK_STR_EMPTY;
                            while ($this->CanRead()) {
                                $v .= $this->m_text[$this->m_offset];
                                $this->m_offset++;
                                if (substr($v, -1, 1) == ">") {
                                    $v = substr($v, 0, strlen($v) - 1);
                                    $this->m_name = null;
                                    $this->m_v = $v;
                                    $this->m_nodetype = XMLNodeType::DOCTYPE;
                                    return true;
                                }
                            }
                        }
                        return false;
                    }
                    break;
                case "/":
                    if ($v_enter) {
                        $this->m_offset += 1;
                        $this->m_nodetype = XMLNodeType::ENDELEMENT;
                        $this->m_name = $this->ReadName();
                        $this->m_v = null;
                        $v_enter = false;
                        while (($v_c > $this->m_offset) && ($this->m_text[$this->m_offset] !== '>')) {
                            $this->m_offset++;
                        }
                        return true;
                    }
                    $v .= $c;
                    break;
                default:
                    if (!$v_enter) {
                        if ($this->m_nodetype == XMLNodeType::ELEMENT) {                      
                            $tag = strtolower($this->m_name);
                            switch ($tag) {
                                case "script":
                                case "style": 
                                     $v = self::__readSkipContent($this, $this->m_text, $this->m_offset, $tag); 
                                    $this->m_name = $tag;
                                    $this->m_v = $v;
                                    $this->m_nodetype = XMLNodeType::INNER_TEXT;                                
                                    return true; 
                                default:
                                    $c = $this->__readTextValue($v); 
                                    return $c;
                            }
                        } else {
                            $v = IGK_STR_EMPTY;
                            if ($this->m_nodetype == XMLNodeType::ENDELEMENT) {
                                if ($c == ">")
                                    $this->m_offset++;
                                $c_txt = $this->__readTextValue();
                                if (!$c_txt) {
                                    $this->m_offset--;
                                    break;
                                }
                                return $c_txt;
                            }
                            return $this->__readTextValue();
                        }
                    } else {
                        $this->m_name = $this->ReadName();
                        $this->m_v = null;
                        $this->m_nodetype = XMLNodeType::ELEMENT;
                        $this->m_isEmpty = false;
                        $this->m_hasAttrib = false;
                        // $v_end = false;
                        $v = IGK_STR_EMPTY;
                        // $v_readname = false;
                        // $v_readvalue = false;
                        // $v_attribname = null;
                        // $v_ch = null;
                        // $v_startattribvalue = false;
                        // $v_attribmatch = IGK_STR_EMPTY;
                        // $v_bracketstart = false;
                        // $v_bracketch = "";
                        $v_expressions = array();
                        $v_tattribs = [];
                        $binfo = $this->getBindingInfo();
                        $v_fc = $this->_getAttribuReaderCallback($binfo, $fc_attrib, $v_expressions);
                        if (self::_ReadAttributes($this, $v, $v_tattribs, $v_fc) && !empty($v)) {
                            $this->m_hasAttrib = true;
                            $skip_visible = (array_key_exists("igk:isvisible", $this->m_attribs) && ($this->m_attribs["igk:isvisible"] == false));
                            if ($skip_visible || ($binfo->skipcontent && !$this->m_isEmpty)) {
                                $content = self::__readSkipContent($this, $this->m_text, $this->m_offset, $this->m_name);
                                $this->m_attribs[IGK_ENGINE_ATTR_TEMPLATE_CONTENT] = $skip_visible ? null : array_merge(["content" => $content], $binfo->getInfoArray());
                                $this->m_isEmpty = true;
                                
                            }
                        }
                        return true;
                    }
            }
            $this->m_offset += 1;
        }
        if (!$v_enter && !empty($v)) {
            $this->m_name = null;
            $this->_setText($v);
            return true;
        }
        return false;
    }
    private function _setText(?string $value = "")
    {
        $this->m_v = $value;
        $this->m_nodetype = XMLNodeType::TEXT; 
    }
    ///<summary>Represente ReadAttributes function</summary>
    ///<param name="value"></param>
    public static function ReadAttributes($value)
    {
        die("not implement" . __METHOD__);
    }
    ///read tag name
    public function ReadName()
    {
        $v = IGK_STR_EMPTY;
        while ($this->CanRead() && preg_match("/" . IGK_TAGNAME_CHAR_REGEX . "/i", $this->m_text[$this->m_offset])) {
            $v .= $this->m_text[$this->m_offset];
            $this->m_offset++;
        }
        return $v;
    }
    ///<summary>Represente ReadProcessText function</summary>
    ///<param name="reader"></param>
    private static function ReadProcessText($reader)
    {
        $v = IGK_STR_EMPTY;
        $bind = false;
        $reader->m_procTagClose = false;
        $phptag = false;
        $detectHeader = false;
        $tag = null;
        $scomment = 0;
        while ($reader->CanRead()) {
            $ch = $reader->m_text[$reader->m_offset];
            $v .= $ch;
            $reader->m_offset++;
            if (!$detectHeader) {
                if (!$phptag) {
                    $phptag = preg_match("/^(php|=)/", $v);
                    $detectHeader = true;
                }
                if ($ch == " ") {
                    $tag = trim($v);
                    $detectHeader = true;
                }
                continue;
            }

            if (substr($v, -2, 2) == "/*") {
                // read till end end comment
                $lpos = strpos($reader->m_text, "*/", $reader->m_offset);
                if ($lpos > 0) {
                    $v .= substr($reader->m_text, $reader->m_offset, $lpos - $reader->m_offset + 2);
                    $reader->m_offset = $lpos + 2;
                    continue;
                } else {
                    die("end multiline comment not found");
                }
            }
            switch ($ch) {
                case '"':
                case "'":
                    // + | -------------------------------------------------------------------------                     
                    // + | read propcessor string content                
                    if ($reader->m_text[$reader->m_offset] == $ch) {
                        // empty string
                        $text = $ch;
                    } else {
                        $text = igk_str_read_brank($reader->m_text, $reader->m_offset, $ch, $ch, null, 1);
                    }
                    $v .= $text;
                    $reader->m_offset++;
                    break;
                case '/':
                    if (!$scomment) {
                        $scomment = 1;
                    } else if ($scomment) {
                        $c_pos = strpos($reader->m_text, "\n",  $reader->m_offset);
                        if ($c_pos === false) {
                            // read to end 
                            $v .= substr($reader->m_text, $reader->m_offset);
                            $reader->m_offset = strlen($reader->m_text) + 1;
                        } else {
                            $v .= substr($reader->m_text, $reader->m_offset, $c_pos - $reader->m_offset);
                            $reader->m_offset = $c_pos;
                        }
                        $scomment = 0;
                    }
                    break;
                case '<':
                    // detect HEREDOC and now doc
                    $pos = preg_match("#\<\<('|\")?(?P<name>[\w]+)(\\1)?$#im", $reader->m_text, $tab, PREG_OFFSET_CAPTURE,  $reader->m_offset);
                    if ($pos){
                        //igk_wln_e("detect:");
                        $name = $tab['name'][0];
                        $cois = $tab[0][1];
                        if ($cois == $reader->m_offset){
                            $ln = strlen($tab[0][0]); 
                            if (preg_match("/^".$name."($|;|,|\)|])/m", $reader->m_text, $tab, PREG_OFFSET_CAPTURE,  $cois + $ln)){
                                $xoff = $tab[0][1]+strlen($tab[0][0]);
                                $v .= substr($reader->m_text, $cois, $tab[0][1]+strlen($tab[0][0]) - $cois);
                                
                                $reader->m_offset = $xoff;
                                // continue 2; 
                            } else {
                                die("HEREDOC NOT CLOSED");
                            }  
                        }
                    } 
                    break;
            }

            if (substr($v, -2, 2) == "?>") {
                $v = substr($v, 0, strlen($v) - 2);
                $bind = true;
                break;
            }
        } 
        if ($bind || preg_match("/^(php|xml|=)/", $v)) {
            $reader->m_name = null;
            $reader->m_v = $v;
            $reader->m_nodetype = XMLNodeType::PROCESSOR;
            $reader->m_procTagClose = !$bind;
            return true;
        }
    }
    ///<summary>set loading  context</summary>
    private function setContext($context)
    {
        $this->m_context = $context;
    }
    ///<summary>set root node creator</summary>
    public static function SetCreator($listener)
    {
        self::$sm_ItemCreatorListener = $listener;
    }
    ///<summary></summary>
    public function Skip()
    {
        if ($this->m_nodetype == XMLNodeType::ELEMENT) {
            if (!$this->m_isEmpty) {
                $n = $this->Name();
                $depth = 0;
                $end = false;
                while (!$end && $this->Read()) {
                    switch ($this->m_nodetype) {
                        case XMLNodeType::ELEMENT:
                            $depth++;
                            break;
                        case XMLNodeType::ENDELEMENT:
                            if (($depth == 0) && (strtolower($this->Name()) == strtolower($n))) {
                                $end = true;
                            } else if ($depth > 0)
                                $depth--;
                            break;
                    }
                }
                return $end;
            }
        }
        return false;
    }
}
