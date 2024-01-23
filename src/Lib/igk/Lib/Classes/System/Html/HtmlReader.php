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

use Closure;
use Exception;
use IGK\Helper\Activator;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlTextNode;
use IGK\System\XML\XMLExpressionAttribute;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlCommentNode;
use IGK\System\Html\Dom\HtmlDoctype;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Dom\HtmlProcessInstructionNode;
use IGK\System\Html\HtmlTemplateReaderDataBinding;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\Templates\BindingConstants;
use IGK\System\Html\XML\XmlNode;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\CompilerConstants;
use IGK\System\Templates\BindingExpressionReader;
use IGK\XML\XMLNodeType;
use IGKException;
use IGKObject;
use ReflectionException;

use function igk_resources_gets as __;


final class HtmlReader extends IGKObject
{
    const EXPRESSION_ARGS = "[[:@raw]], [[:@ctrl]]";
    const ARGS_ATTRIBUTE = "igk:args";
    const REF_ATTRIBUTE = "igk:ref-attribute";
    const READ_XML =  "XML";
    const READ_HTML = "HTML";
    const LOAD_EXPRESSION = "LoadExpression";
    private $m_attribs, $m_contextLevel, $m_hasAttrib, $m_hfile,
        $m_selfClose,
        $m_isEmpty, $m_mmodel, $m_name, $m_nodes, $m_nodetype, $m_offset, $m_procTagClose, $m_resolvKeys, $m_resolvValues, $m_text, $m_v;

    /**
     * last read for empty 
     * @var ?HtmlItemBase
     */
    private $m_last_read_node;
    /**
     * @var ?IHtmlReadContextOptions|mixed
     */
    private $m_context;
    // $m_self_close;
    private $m_self_closing_items;
    private $m_errors = [];
    private static $sm_ItemCreatorListener, $sm_openertype = [];
    private $m_length;
    /**
     * skip element data
     * @var ?bool
     */
    private $m_skipElement;
    /**
     * in skip content to evaluate
     * @var ?bool
     */
    private $m_skip_content_mode;
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
    /**
     * 
     * @param static $reader 
     * @param mixed $cnode current node
     * @param mixed $template template
     * @return int 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _BindTemplate($reader, &$cnode, $template)
    {
        $engine = "";
        if (is_array($template)) {
            $src = igk_getv($template, "content");
            $data = igk_getv($template, "context-data", []);
            $operation = igk_getv($template, 'operation');
            $ctrl = isset($reader->m_context->ctrl) ? $reader->m_context->ctrl : null;
            $n_context = ["scope" => 0, "contextlevel" => 1, "fname" => "__memory__", "data" => null];

            if ($operation == \IGK\System\Html\Templates\BindingConstants::OP_LOOP) {
                $n_options = (object)["Indent" => 0, "Depth" => 0, "Context" => "html", "RContext" => $n_context, "ctrl" => $ctrl];
                igk_set_env("sys:://expression_context", $n_options);
                if ($cnode->getIsVisible() && $data) {
                        $v_bcontext = $reader->m_context;
                        $v_bind = new HtmlTemplateReaderDataBinding($cnode, $src, $ctrl, $data, $v_bcontext);
                        $v_ts = $v_bind->treat();
                        if ($v_bcontext->transformToEval) {
                            $v_expression = $v_bcontext->hookExpression ?? CompilerConstants::BINDING_CONTEXT_VAR_NAME;
                            $v_comment = igk_is_debug() ? '/* ' . __METHOD__ . ' */ ' : "";
                            $sb = new StringBuilder;
                            $sb->appendLine('<?php');
                            $sb->append("if (isset($v_expression)) foreach($v_expression as \$key=>\$raw$v_comment):\n?>");
                            $sb->append($v_ts);
                            $sb->appendLine("<?php\nendforeach;\n?>");

                            if ($v_expression==CompilerConstants::BINDING_CONTEXT_VAR_NAME){
                                //+ | passing sub to function 
                                $sb = sprintf('<?php (function(%s){ %s ?>%s<?php })($raw); ?>', 
                                    $v_expression,
                                     $v_expression.' = '.str_replace("%s", $v_expression, 
                                        'isset(%s) && !is_array(%s)&& !is_object(%s) ? [%s] : %s;'), //.$v_expression.'];',
                                    $sb.'');
                            }
                            $v_ts = $sb;
                        }
                        $engine .= $v_ts;  
                }
                igk_set_env("sys:://expression_context", null);
            } else {
                igk_die(__("Only loop operation is allowed : {0}", $operation));
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
        $this->m_self_closing_items = [
            HtmlContext::Html => explode("|", HtmlContext::EmptyTags)
        ];
    }

    /**
     * read name
     * @param mixed $text 
     * @param mixed $length length to read
     * @param mixed $offset move position to the last read char
     * @param mixed $eval_context 
     * @param bool $expressionRead 
     * @return string 
     */
    private static function _ReadName($text, $length, &$offset, $eval_context, &$expressionRead = false)
    {
        $expressionRead = false;
        $name = "";
        $length = !$length ? strlen($text) : max($length, strlen($text));
        while ($offset < $length) {
            $ch = $text[$offset];
            if (strpos(IGK_IDENTIFIER_TAG_CHARS, $ch) === false) {
                if ($eval_context) {
                    if (strpos($text, "<?=", $offset) === $offset) {
                        if (($gpos = strpos($text, "?>", $offset)) > $offset) {
                            $v_txt = substr($text, $offset, ($gpos - $offset) + 2);
                            $name .= $v_txt;
                            $offset = $gpos + 2;
                            $expressionRead = true;
                            continue;
                        }
                    }
                }
                break;
            }
            $name .= $ch;
            $offset++;
        }
        return $name;
    }
  
    /**
     * 
     * @param static $reader 
     * @param string $text 
     * @param int $offset 
     * @param string $tag 
     * @param bool $replacement activate expression replacement
     * @param bool $replace_expression 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _SkipContent($reader,string $text, &$offset, string $tag, bool $replacement = false, bool $replace_expression=true)
    {
        $ln = strlen($text);
        $name = $ch = $o = "";
        $v = "";
        $level = 0;
        $end = 1;
        $tpos = 0;
        $intag = 0; 
        $eval_context = $reader->m_context->transformToEval ?? false;
        $expressionRead = false;
        $tnames = [$tag];
        $reader->m_skip_content_mode = true; 
        $v_is_multiline_comment_support = in_array($tag, ['script', 'style', 'code']);
        $v_support_litteral_string = in_array($tag, ["style", "script", "code"]);

        if (in_array($tag, ['script', 'textarea'])) {
            // + | read until end </script> found
            $stag = 0;
            $tpos = 0;
            $bpos = $offset;
            $skip_space = 0;
            while ($end && ($offset < $ln)) {
                $lch = $ch;

                $ch = $text[$offset];

                if ($v_is_multiline_comment_support && ($ch == '*') && '/' == $lch) {
                    // multi line comment
                    $next = strpos($text, "*/", $offset);
                    if ($next !== false) {
                        $v .= substr($text, $offset, ($next + 2) - $offset);
                        $offset = $next + 2;
                    } else {
                        $v .= substr($text, $offset);
                        $offset = $ln;
                    }
                    //$offset++;
                    continue;
                }
                switch ($ch) {
                    case '<':
                        $stag = 1;
                        $tpos = strlen($v); //$offset;
                        break;
                    case '>':
                        if ($stag == 2) {
                            $end =  0;
                        }
                        break;
                    case '/':
                        if ($stag) {
                            $v .= $ch;
                            $offset++;
                            $name = self::_ReadName($text, $ln, $offset, $eval_context, $expressionRead);
                            if ($name == $tag) {
                                $stag = 2;
                            }
                            $v .= $name;
                            $ch = '';
                            $offset--;
                        } else {
                            if ($lch == $ch) {
                                // single line comment dected
                                if (($pos = strpos($text, "\n", $offset)) !== false) {
                                    $v .= substr($text, $offset, $pos + 1 - $offset);
                                    $offset = $pos;
                                } else {
                                    $v .= substr($text, $offset) . "\n";
                                    $offset = $ln;
                                }
                                $ch = '';
                                $lch = '';
                                //igk_wln_e("single line ....", $v);
                            }
                        }
                        break;
                    case "'":
                    case '"':
                    case '`': // + | multiline litteral string
                        $v .=  igk_str_read_brank($text, $offset, $ch, $ch, null, 1);
                        $ch = '';
                        break;
                    case ' ':
                        if (!$skip_space) {
                            $v .= $ch;
                        }
                        $ch = '';
                        $skip_space = 1;
                        break;
                    case "\t":
                        if (!$skip_space) {
                            $v .= $ch;
                        }
                        $ch = '';
                        $skip_space = 1;
                        break;

                    default:
                        $stag = 0;

                        break;
                }
                $offset++;
                $v .= $ch;
                if ($skip_space && !empty($ch)) {
                    $skip_space = 0;
                }
            }
            if (!$end)
                $v = trim(substr($v, 0, $tpos));
            return $v;
        }

        $v_can_replace_detected = ($reader->GetStringContext() == HtmlContext::Html) && $replacement &&
            self::_CanReplaceDectedSkipModeExpression($reader, $tag);

        // + | read tag until matching end tag found script|code found
        // push buffer content 
        $v_contents = [];
        $v_end_tag_flag = false;
        $v_start_tag_flag = false;
        $single_quote = false;
        $single_end = false;
        while ($end && ($offset < $ln)) {
            $lch = $ch;
            $ch = $text[$offset];

            if ($v_is_multiline_comment_support && ($ch == '*') && ('/' == $lch)) {
                // multi line comment
                $next = strpos($text, "*/", $offset);
                if ($next !== false) {
                    $v .= substr($text, $offset, ($next + 2) - $offset);
                    $offset = $next + 2;
                } else {
                    $v .= substr($text, $offset);
                    $offset = $ln;
                }
                //$offset++;
                continue;
            }
            if ($single_quote){
                if ($ch == "\n"){
                    $single_quote = false;
                }
            }
            switch ($ch) {
                case ">":
                    $v .= $ch;
                    if (($end == 2) && ($level == 0)) {
                        $end = 0;
                    }
                    $intag = 0;
                    if ($v_start_tag_flag){
                        // start tag mark 
                        $v_contents[] = $v;
                        $v = '';
                        $v_start_tag_flag = false;
                    }
                    if ($v_end_tag_flag) {
                        if (($name == 'code') && ($v_can_replace_detected) && !$single_quote){
                            // remove last 
                            $dv = substr($v, 0, $endpos = strrpos($v, '</'));
                            $dv = self::_ReplaceLitteralExpression($reader, $dv, $replace_expression, false);
                            $v = $dv.substr($v, $endpos);
                        }

                        $v_contents[] = $v; // (object)['offset'=>$offset, 'sb'=>'', 'name'=>$name];
                        $v = '';
                        $v_end_tag_flag = false;
                        $single_quote = false;
                    }
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
                    if (igk_str_endwith($v, '//')) {
                       $single_quote = self::_SupportSingleQuote($tag);
                       if ($single_quote){
                           $single_end = $name;
                       }
                    }
                    break;
                case "<":
                    if ($intag) {
                        igk_die(
                            sprintf(
                                "[BLF] - xml reading : enter tag not valid.\nat %s",
                                substr($text, $offset - 10, 30)
                            )
                        );
                    }
                    $intag = 1;
                    $tpos = strlen($v);
                    $v .= $ch;
                    $tch = null;
                    ($offset + 1 < $ln) && (($tch = $text[$offset + 1]));
                    switch ($tch) {
                        case "/": {
                                // + | ------------------------------------------------
                                // + | detect end tag 
                                $v .= $tch;
                                $offset += 2;
                                $name = self::_ReadName($text, $ln, $offset, $eval_context, $expressionRead);
                                if (empty($name) || (($tmix = array_pop($tnames)) != $name)) {
                                    igk_dev_wln(
                                        'error :: not matching tag - ',
                                        $tmix,
                                        $name,
                                        substr($text, max(0, $offset - 30),  80),
                                        ''
                                    );
                                    igk_die("xml reading not valid : " . $tmix . " # [" . $name . "] level " . $level);
                                }
                                $v .= $name;
                                if (($level == 0) && ($name == $tag)) {
                                    $end = 2;
                                    $intag = 0;
                                } else {
                                    $level--;
                                }
                                $offset--;
                                $v_end_tag_flag = true;
                                if ($single_quote){
                                    if ($single_end == $tmix){
                                        $single_quote = false;
                                    }
                                }
                            }
                            break;
                        case "!":
                            // $start = $offset;
                            //+ Skip comment

                            if (($pp = strpos($text, "-->", $offset)) !== null) {
                                $offset = $pp + 3;
                            }
                            $intag = 0;
                            break;
                        default:
                            $offset++;
                            $name = self::_ReadName($text, $ln, $offset, $eval_context, $expressionRead);
                            // if ($v_is_multiline_comment_support && !empty($name)) {
                            //     // start operator -: 
                            //     $v .= $name;
                            //     $offset--;
                            //     $intag = 0;
                            //     // array_push($tnames, $name);
                            //     break;
                            // }

                            if (empty($name)) {
                                $v .= $tch;
                                $intag = false;
                                break;
                            }
                            if ($expressionRead) {
                                $name = new HtmlTagExpressionName($name);
                            }
                            array_push($tnames, $name);
                            $level++;
                            $v .= $name;
                            $offset--;
                            $v_start_tag_flag = true;
                            break;
                    }
                    break;
                case "'":
                case '"':
                    if ($intag || ($v_support_litteral_string && !$single_quote)) {
                        $v .=  igk_str_read_brank($text, $offset, $ch, $ch, null, 1);
                    } else {
                        $v .= $ch;
                    }
                    break;                    
                default:
                    $v .= $ch;
                    break;
            }
            $offset++;
        } 
        if ($v_can_replace_detected && !empty($v)) {
            $v = self::_ReplaceLitteralExpression($reader, $v, $replace_expression);           
        }
        $v = implode('', $v_contents).$v;
        //remove last tag....
        $v = substr($v, 0, $endpos = strrpos($v, '</'));

        if (($intag) || (count($tnames) > 0)) {
            if ($q = array_pop($tnames)) {
                // skip end with 
                if (preg_match("/\<\/" . $q . "\>$/", $v, $match)) {
                    // fix by removing the end matching tag
                    $v = igk_str_rm_last($v, $match[0]);
                    return $v;
                }
            }

            igk_die(sprintf(
                "Syntax error intag but failed read data. %s, offset: %s",
                '---',
                substr($reader->m_text, $reader->m_offset - 20, 40)
            ));
        } 
        return $v;
    }
    private static function _SupportSingleQuote(string $name){
        return in_array($name,['code','script']);
    }
    /**
     * replace litteral expression 
     * @param mixed $reader 
     * @param string $v 
     * @param bool $replace_expression 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _ReplaceLitteralExpression($reader, string $v, bool $replace_expression, $skip=true):string{
 
        if (self::_ReplaceDetectedExpression(
            $reader,
            $v,
            $rv, 
            $replace_expression,
            $skip
        )) { 
            $v = $rv;
        }
        return $v;
    }
    /**
     * detect depending on reader context or in context mode
     * @param mixed $reader reader 
     * @param string $name tagname 
     * @return bool 
     */
    private static function _CanReplaceDectedSkipModeExpression($reader, string $name): bool
    {
        if ($reader->m_context instanceof HtmlBindingContextOptions) {
            $reader->m_skip_content_mode = false;
            return true;
        }
        return !in_array($name, explode("|", "code|textarea|script"));
    }
    ///<summary>read text content</summary>
    /**
     * read text  
     * @param string $prefix 
     * @return bool 
     * @throws IGKException 
     */
    private function _readTextValue(string $prefix = "")
    {
        $_pre = ($this->m_name == 'pre') && ($this->m_nodetype == 1);
        $_cread = 1; 
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
            }
            $this->m_offset++;
            $v .= $ch;
        }
        if (($v == '0') || !empty($v)) {
            if ($this->GetStringContext() == HtmlContext::Html) {                                 
                if (is_object($this->m_context) && !property_exists($this->m_context, 'transformToEval')){
                    igk_wln_e("die..... missing");
                }
                $transformToEval = is_object($this->m_context) ? $this->m_context->transformToEval : false;                
                if (!empty(trim($v)) && self::_ReplaceDetectedExpression($this, $v, $cv, $transformToEval, 0)) {
                    $v = $cv;
                }
            }
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
    ///<param name="skip" default="1"></param>
    /**
     * 
     * @param static $reader 
     * @param mixed $text 
     * @param mixed $v 
     * @param bool $transformToEval transformEval expression
     * @param bool $skip skip detected expression : transform to 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _ReplaceDetectedExpression($reader, $text, &$v, bool $transformToEval = false, bool $skip = false)
    {
        /**
         * @var IHtmlReadContextOptions $n_context
         */ 
        if (!is_object($reader->m_context)) {  
            return false;
        }
        if ($reader->m_context->noInterpolation){
            $v = $text;
            return true;
        }
        $n_context = $reader->m_context;
        $data =  igk_get_attrib_raw_context($n_context);
        $exp_reader = new BindingExpressionReader;
        $exp_reader->transformToEval = $transformToEval;
        $exp_reader->skipMode = $skip;
        $exp_reader->expressionValueName = 'expression';
        $exp_reader->expressionArgs = [
            "expression" => "",
            self::ARGS_ATTRIBUTE => self::EXPRESSION_ARGS
        ];

        if (igk_getv($n_context, 'type') == BindingConstants::OP_LOOP) {
            $exp_reader->transformToEval = $n_context->transformToEval;            
            $sdata = $exp_reader->treatContent($text, $data );
            $exp_reader->transformToEval = $transformToEval;
        } else {
            $sdata = $exp_reader->treatContent($text, $data);
        }
        $v = $sdata;
        return true; 
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
            $v = self::CreateNode($n, $args, $cnode);
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
            $n = new XmlNode($name);
            if ($pargs) {
                $n->setAttributes($pargs);
            }
            if ($cnode) {
                $cnode->add($n);
            }
            return $n;
        }
        return ($cnode !== null) ? $reader->_addNode($cnode, $name, $tab_doc, $pargs) : self::CreateNode($name, $pargs, $cnode);
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
        //$v_skip = false;
        $protag = 0;
        // $v_sv = "";
        //$escape = false;
        $pro_expr = "";
        $expr_attrib = false;
        $reader->m_selfClose = false; // detect that the attribute list is self closed


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
                        igk_die(
                            implode("\n", [
                                "not valid attribute read not vaid: [$v] - {$v_n} - $mode " .
                                    "offset : " . $reader->m_offset
                            ])
                        );
                    }
                    // + | important move back before read brank
                    $reader->m_offset--;
                    $v_v = stripslashes(trim($_tv = igk_str_read_brank(
                        $reader->m_text,
                        $reader->m_offset,
                        $v_ch,
                        $v_ch,
                        null,
                        true,
                        true,
                        $v_ch == "'" ? '"' : null
                    ), $v_ch));

                    // if ($_tv == '"alert-"') {
                    //     igk_dev_wln_e("log: " . $_tv);
                    // }

                    $v .= $v_v . $v_ch;


                    $reader->m_offset++; //= $start + 1;
                    $attribs[$v_n] = $v_v;
                    if ($callback) {
                        if ($expr_attrib) {
                            $v_v = new XMLExpressionAttribute($v_v);
                        }
                        $callback($v_n, $v_v);
                    }
                    $v_n = "";
                    $v_v = "";
                    // $v_sv = "";
                    $mode = 1;
                    break;
                case "<":
                    if ($protag != 0)
                        die("protag not valid");
                    $protag = 1;
                    if ($mode == 2) {
                        $v_v .= $v_ch;
                    } else {
                        igk_ilog(sprintf(
                            "%s - name:%s",
                            sprintf(
                                "Syntax error: expression tag not allowed in attribute definition: %s, mode:%s, offset:%s\n%s",
                                $v,
                                $mode,
                                $reader->m_offset,
                                substr($reader->m_text, $reader->m_offset - 10, 20),
                            ),
                            $reader->m_name
                        ));
                        $reader->m_errors[] = 'Syntax error : try read tag on attribute';
                        $end = true;
                        $reader->m_offset - 1;
                        return true;
                    }
                    break;
                case "=":
                    if (($mode == 0) || ($mode == 1)) {
                        $mode = 2;
                        $v_v = "";
                    }
                    break;
                case ">":
                    // + | attempt to close tag 
                    $end = true;
                    $v = substr($v, 0, -1);
                    if (substr($v, -1) == "/") {
                        $v = substr($v, 0, -1);
                        $reader->m_isEmpty = true;
                        $reader->m_selfClose = true;
                    } else {
                        if ($reader->m_context == HtmlContext::Html) {
                            // special closing tag
                            if (in_array($reader->m_name, HtmlContext::GetEmptyTagArray())) {
                                $reader->m_isEmpty = true;
                            }
                        }
                    }
                    if ($v_n == "/") {
                        $v_n = null;
                    } 
                    if (!is_null($v_n))
                        $v_n = rtrim($v_n, '/ ');
                    break;
                default:
                    if ($mode == 0) {
                        if (is_numeric($v_ch) || !empty(trim($v_ch))) {
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
        if (!empty($v_n)) {
            $attribs[$v_n] = true;
            if ($callback) {
                $callback($v_n, true);
            }
            $v_n = null;
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
        $v_tags = array();
        self::_PushContext(($reader->m_context != null) ? $reader->m_context : self::READ_XML);
        // + | 

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
        // + | read model definition 

        while ($reader->read()) {
            switch ($reader->NodeType) {
                case XMLNodeType::ELEMENT:
                    if (!$reader->m_skipElement)
                        self::_ReadModelEndElement($reader, $v_tags, $cnode, $tab_doc, $caller_context);
                    // else{
                        // igk_wln_e(__FILE__.":".__LINE__, "element skiped");
                    // }

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
                    if (!$cnode) {
                        header("Content-Type: text/plain");
                        igk_die("can't set inner text on non detected node :\n " .
                            "offset: " . $reader->m_offset . "\n\n" .
                            substr(
                                $reader->m_text,
                                $reader->m_offset,
                                40
                            ) . "\n\n");
                    }
                    if (!empty($g = $reader->getValue())) {
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
                    $n = $reader->getName();
                    // igk_debug_wln("end element : ".$n);
                    // $tnode = $cnode;
                    if ($cnode) {
                        $t = $cnode->getTagName();
                        if ($caller_context == self::LOAD_EXPRESSION) {
                            if ($n == $t) {
                                $cnode = $reader->_LoadComplete($cnode, $n);
                            } else {
                                $krsv = true;
                                $_shift_setting($n, $cnode, $v_tags, $krsv);
                                while ($cnode && ($cnode->getTagName() != $n)) {
                                    $cnode = $reader->_LoadComplete($cnode, $n);
                                    $rsv = true;
                                }
                                if (!$krsv && $cnode) {
                                    $cnode = $reader->_LoadComplete($cnode, $n);
                                } else {
                                    igk_die(new \IGK\System\DieInfo(
                                        "[Bad Html structure] can't get parent, cnode is null",
                                        [
                                            'name' => $n,
                                            'tagName' => $t,
                                            'Line' => __LINE__,
                                            'Context' => $caller_context,
                                            'object_class' => is_object($cnode) ? get_class($cnode) : 'cnode missing'
                                        ]
                                    ));
                                }
                            }
                            if ($cnode == $tab_doc) {
                                $cnode = null;
                                $reader->m_nodes = array();
                            }
                        } else {
                            if (($n == $t) || $cnode->closeTag() || $cnode->isCloseTag($n) || $reader->IsResolved($cnode, $n)) {
                                if ($reader->m_last_read_node && 
                                ($reader->m_last_read_node->getTagName()==$n)){
                                    $reader->m_last_read_node = null;
                                    break;
                                }
                                if ($n != $t) {
                                    // + | detect error start with different closing tag expected $t but found -$n
                                    $peek = $v_tags ? $v_tags[0] : null;
                                    $v_shift = ($peek) && ($peek->item === $cnode);
                                    if ($peek && ($peek->item === $cnode) && ($peek->source_tagname == $n)) {
                                        // possible matching tag 
                                        // igk_debug_wln("match-close-tag : ".$n); 
                                        $cnode = $reader->_LoadComplete($cnode, $n);
                                        array_shift($v_tags);
                                        break;
                                    } else {
                                        $v_error = true;
                                        if (($v_tags) && ($v_tags[count($v_tags) - 1])) {
                                            $peek = $v_tags[0];
                                            if ($peek->clName == $n) {
                                                $v_error = false;
                                            }
                                        }
                                        if ($v_error) {
                                            $empty = $cnode->isEmpty();
                                            if ($cnode->closeTag()) {
                                                $reader->m_errors['warnings'][] = 'missing close tag for : ' . $t;
                                            }
                                            if (!$empty) {
                                                igk_die("missing close tag for [" . $t
                                                    . "] offset:" . $reader->m_offset .
                                                    " data: " . $n .
                                                    " info:" . json_encode($peek) . PHP_EOL .
                                                    " source: " . ($peek ? $peek->source_tagname : null));
                                            }
                                        }
                                        // detect possible node 
                                        $v_shift = ($peek) && ($peek->item === $cnode);
                                        $cnode = $reader->_LoadComplete($cnode, $t);
                                    }
                                    if ($peek) {
                                        array_shift($v_tags);
                                    }
                                    if ($v_shift && (($peek) && ($peek->item === $cnode))) {
                                        break;
                                    }
                                }
                                $cnode = $reader->_LoadComplete($cnode, $n);
                                array_shift($v_tags);
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
                                            array_shift($v_tags);
                                            $kclosing = 0;
                                        } else {
                                            if (!$pnode->isEmptyTag() && ($pnode->getTagName() == $n)) {
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
    protected static function _ReadModelEndElement($reader, &$v_tags, &$cnode, $tab_doc, $caller_context)
    {
        $v_n = $reader->getName();
        if (empty($v_n)) {
            igk_die("xmlreading: empty element not allowed");
        }
        // igk_debug_wln('reader : '.$v_n . ' '.$reader->m_offset);
        $cattr = $reader->Attribs();

        if ($caller_context == self::LOAD_EXPRESSION) {
            $v_tn = new HtmlNode($v_n);
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
                $cnode = $reader->_LoadComplete($cnode, $v_n);
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
            $v_tn = self::_BuildNode($reader, $cnode, $v_n, $tab_doc, $pargs);


            if ($v_tn) {
                if ($v_tn->tagName && !$reader->IsEmpty()) {
                    // + | --------------------------------------------------------------------
                    // + | prepend tag item to detect closing real closing tag - because some \
                    // + | item can be created natively be return another tag property
                    array_unshift($v_tags, (object)array(
                        IGK_FD_NAME => $v_n,
                        "item" => $v_tn,
                        "source_tagname" => $reader->m_name
                    ));
                    if ($cnode == null)
                        $reader->_appendResolvNode($v_n, $v_tn, $cnode);
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
                    return;
                }
                if ($reader->IsEmpty() && $cnode) {
                    // move to parent if node is empty 
                    if (!$reader->getSelfClosed()){
                        $reader->m_last_read_node = $cnode;
                    }
                    
                    $cnode = $reader->_LoadComplete($cnode, $v_n);
                    if ($cnode === $tab_doc) {
                        $cnode = null;
                        $reader->m_nodes = array();
                    }
                }
            } else {
                $reader->Skip();
            }
        }
    }
    /**
     * 
     * @param mixed $n 
     * @param mixed $cnode 
     * @param mixed $v_tags 
     * @param mixed $krsv 
     * @return void 
     */
    private static function _ShifSetting($n, $cnode, &$v_tags, &$krsv)
    {
        //+ | REMOVE SHIFT SETTING
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
    ///<summary>return attributes</summary>
    /**
     * return attributes
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
    ///<summary>close the reader</summary>
    /**
     * close the reader in case of file reading
     * @return void 
     */
    public function close()
    {
        if ($this->hfile)
            fclose($this->hfile);
        $this->m_text = null;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    public static function Create(string $file)
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
    public static function CreateNode($name, $args = null, ?HtmlItemBase $currentNode = null)
    {
        if (is_callable(self::$sm_ItemCreatorListener)) {
            $fc = self::$sm_ItemCreatorListener;
            $cdoc = $currentNode;
            if ($cdoc instanceof HtmlReaderDocument)
                $cdoc = null;
            return $fc($name, $args, $cdoc);
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
     * create a binding info 
     * @return HtmlReaderBindingInfo 
     */
    protected function createBindingInfo()
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
     * @param string $text content to load  
     * @param const $listener environment context HTML|XML
     * @param callable $listener creator to call 
     */
    public static function Load(string $text,  $context = null, callable $listener = null)
    {
        $tab_doc = null;
        $b_context = false;

        if ((is_object($context) || (is_array($context))) && !($context instanceof HtmlLoadingContextOptions)){
            // + | transform object to loading context - setting
            $context = Activator::CreateNewInstance(HtmlLoadingContextOptions::class, $context);
        } 
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
    ///<summary>get name</summary>
    public function getName()
    {
        return $this->m_name;
    }
    ///<summary>loading context</summary>
    public function procTagClose()
    {
        return $this->m_procTagClose;
    }
    /**
     * 
     * @param mixed $binfo binding info
     * @param callable $fc_attrib attribute setter 
     * @param mixed $v_expressions 
     * @param mixed $context 
     * @return Closure(mixed $k, mixed $_v): void|Closure(mixed $k, mixed $v): void 
     */
    private function _getAttributeReaderCallback($binfo,  $fc_attrib, &$v_expressions, $context = null)
    {
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
                            //  if (!isset($v_self->m_attribs[$k]) || is_null($_v) || ($_v instanceof HtmlAttributeExpression)){
                            $set = true;
                            if (isset($v_self->m_attribs[$k]) && $v_self->m_attribs[$k] instanceof HtmlAttributeExpression) {
                                // must be replace with an expression or null
                                $set = ($_v instanceof HtmlAttributeExpression) || is_null($_v);
                            }
                            if ($set) {
                                $v_self->m_attribs[$k] = $_v;
                            }
                            //  }
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

    /**
     * check if tag supported expression tag
     * @param string $tag 
     * @return bool 
     */
    public function isSupportedExpressionTag(string $tag):bool {
        return in_array($tag,explode('|', 'script|style|code|textarea'));
    }
    ///<summary>read content</summary>
    /**
     * read content
     * @return bool 
     * @throws IGKException 
     */
    public function read(): bool
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

        if ($_tagRegexValueRgx === null) {
            $_tagRegexValueRgx = self::GetAttributeRegex();
        }


        while ($this->CanRead()) {
            $c = $this->m_text[$this->m_offset];
            switch ($c) {
                case '<':
                    if ($v_enter) {
                        $this->_readElement();
                        return true;
                    }
                    $v_enter = true;
                    break;
                case "?":
                    if ($v_enter) {
                        $this->m_offset++;
                        if (self::_ReadProcessText($this)) {
                            return true;
                        }
                    } else {
                        return $this->_readTextValue();
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
                                    $this->m_isEmpty = true;
                                    $v_enter = false;
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
                                case 'script':
                                case 'style':
                                case 'textarea':
                                case 'code':
                                    $this->m_name = $tag;
                                    if (!empty($v)) {
                                        $this->m_offset -= strlen($v);
                                    }
                                    $v = self::_SkipContent($this, $this->m_text, $this->m_offset, $tag, $this->isSupportedExpressionTag($tag));
                                    $this->m_v = $v;
                                    $this->m_nodetype = XMLNodeType::INNER_TEXT;

                                    return true;
                                default:
                                    $c = $this->_readTextValue($v);
                                    return $c;
                            }
                        } else {
                            $v = IGK_STR_EMPTY;
                            if ($this->m_nodetype == XMLNodeType::ENDELEMENT) {
                                if ($c == ">")
                                    $this->m_offset++;
                                $c_txt = $this->_readTextValue();
                                if (!$c_txt) {
                                    $this->m_offset--;
                                    break;
                                }
                                return $c_txt;
                            }
                            return $this->_readTextValue();
                        }
                    } else {
                        $this->_readElement();
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
    ///<summary> read element content </summary>
    /**
     * read element content 
     * @return bool 
     * @throws IGKException 
     */
    private function _readElement(): bool
    {
        $fc_attrib = function ($k, $v) {
            // special meaning of class
            if ($k == 'class') {
                $s = igk_getv($this->m_attribs, $k);
                if ($s && ($v instanceof HtmlAttributeExpression)) {
                    $v->prepend($s . " ");
                }
            }

            $this->m_attribs[$k] = $v;
        };
        $this->m_name = $this->ReadName();
        if (empty($this->m_name)) {
            Logger::danger(implode("\n", [
                __FILE__ . ":" . __LINE__,  "empty name ",
                $this->m_context,
                substr($this->m_text, $this->m_offset - 10, 30)
            ]));
            return false;
        }
        //$this->m_offset++;
        // igk_wln_e(__FILE__.":".__LINE__,  "empty name ", $this->m_text, "last char:", $this->m_text[$this->m_offset]);

        $this->m_v = null;
        $this->m_nodetype = XMLNodeType::ELEMENT;
        $this->m_isEmpty = false;
        $this->m_hasAttrib = false;
        $this->m_skipElement = false;
        $this->m_attribs = [];
        $v = IGK_STR_EMPTY;
        $v_expressions = array();
        $v_tattribs = [];
        $binfo = $this->createBindingInfo();
        $v_fc = $this->_getAttributeReaderCallback($binfo, $fc_attrib, $v_expressions);
        $v_key_attrib = "igk:isvisible";
        if (!empty($this->m_name) && self::_ReadAttributes($this, $v, $v_tattribs, $v_fc) && !empty($v)) {
            $this->m_hasAttrib = true;
            $v_not_visible = (array_key_exists($v_key_attrib, $this->m_attribs) && ($this->m_attribs[$v_key_attrib] == false));
            
            if ($v_not_visible || ($binfo->skipcontent && !$this->m_isEmpty)) {
                $v_content = null;
                if ($binfo->skipcontent && !$this->m_isEmpty){ 
                    $v_content = self::_SkipContent($this, $this->m_text, $this->m_offset, $this->m_name, false);
                }

                $this->m_attribs[IGK_ENGINE_ATTR_TEMPLATE_CONTENT] = $v_not_visible ? null : array_merge(
                    ["content" => $v_content],
                    $binfo->getInfoArray()
                );
                $this->m_isEmpty = true;
                $this->m_skipElement = $binfo->skipcontent && ($binfo->operation == BindingConstants::OP_CONDITION);
            } else if ($this->m_isEmpty){
                $this->m_skipElement = $binfo->skipcontent && ($binfo->operation == BindingConstants::OP_CONDITION);
            }
        }
        if ($this->_isSelfClosedElement()) {
            $this->m_isEmpty = 1;
        }
        return true;
    }
    /**
     * check is self closed element
     * @return bool 
     * @throws IGKException 
     */
    private function _isSelfClosedElement(): bool
    {
        if (is_string($this->m_context)) {

            if ($tab = igk_getv($this->m_self_closing_items, $this->m_context)) {
                return in_array($this->m_name, $tab);
            }
        }
        return false;
    }
    private function _setText(?string $value = "")
    {
        if ((strlen($value)>0) && empty(trim($value)) && (strpos($value,"\n ")===0)){
            $value = '';
        }
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
        $v_evaltransform = is_object($this->m_context) ? igk_getv($this->m_context, "transformToEval", false) : false;
        $n = self::_ReadName($this->m_text, strlen($this->m_text), $this->m_offset, $v_evaltransform, $expressRead);

        return $n;
    }
    /**
     * get if element is self closed
     * @return ?bool 
     */
    public function getSelfClosed(){
        return $this->m_selfClose;
    }
    ///<summary>Represente ReadProcessText function</summary>
    ///<param name="reader"></param>
    private static function _ReadProcessText($reader)
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
                    if ($pos) {
                        //igk_wln_e("detect:");
                        $name = $tab['name'][0];
                        $cois = $tab[0][1];
                        if ($cois == $reader->m_offset) {
                            $ln = strlen($tab[0][0]);
                            if (preg_match("/^" . $name . "($|;|,|\)|])/m", $reader->m_text, $tab, PREG_OFFSET_CAPTURE,  $cois + $ln)) {
                                $xoff = $tab[0][1] + strlen($tab[0][0]);
                                $v .= substr($reader->m_text, $cois, $tab[0][1] + strlen($tab[0][0]) - $cois);

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
                $n = $this->getName();
                $depth = 0;
                $end = false;
                while (!$end && $this->read()) {
                    switch ($this->m_nodetype) {
                        case XMLNodeType::ELEMENT:
                            $depth++;
                            break;
                        case XMLNodeType::ENDELEMENT:
                            if (($depth == 0) && (strtolower($this->getName()) == strtolower($n))) {
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
