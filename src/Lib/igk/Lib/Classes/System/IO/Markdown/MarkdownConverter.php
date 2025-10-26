<?php
// @author: C.A.D. BONDJE DOUE
// @file: MarkdownConverter.php
// @date: 20241105 09:15:11
namespace IGK\System\IO\Markdown;
use Exception;
use IGK\Helper\StringUtility;
use IGK\System\Console\App;
use IGK\System\Console\Logger;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlUtils;
use IGK\System\Text\Formatters\FormatterBase;
use IGK\System\Text\IRegexMatchPatternOutpuTreatmentListener;
use IGK\System\Text\IRegexMatchPatternStateListener;
use IGK\System\Text\RegexMatcherCapture;
use IGK\System\Text\RegexMatcherContainer;
use IGKException;
use ReflectionException;
/**
 * helper use to convert markdown text to html
 * @package IGK\System\IO\Markdown
 * @author C.A.D. BONDJE DOUE
 */
class MarkdownConverter implements IRegexMatchPatternStateListener, IRegexMatchPatternOutpuTreatmentListener
{
    /**
     * use code formatter service
     * @var ?bool
     */
    var $useCodeFormatter;
    /**
     * mention base url 
     * @var ?string
     */
    var $mentionBaseURL;

    /**
     * set default line tag
     * @var string
     */
    var $tag = 'p';

    /**
     * store document link
     * @var ?array
     */
    var $documentLinks;
    /**
     * store emojies
     * @var emojies
     */
    private $m_emojies;
    /**
     * 
     * @var RegexMatcherContainer
     */
    private $m_container;
    /**
     * 
     * @var int
     */
    private $m_lpos;
    /**
     * 
     * @var ?string
     */
    private $m_output;
    /**
     * ul/ol root node
     * @var ?HtmlNode
     */
    private $m_ul;
    /**
     * 
     * @var IGK\System\IO\Markdown\m_useTag
     */
    private $m_useTag;
    /**
     * 
     * @var array<keyOf<'a' | 'quote' >, array<['class']>>
     */
    private $m_classStyles;
    private $m_li_item;
    private $m_li_depth;
    protected $m_table;
    /**
     * treatment state 
     * @var 'quote' | 'table' | 'list' | 'sublist' 
     */
    protected $m_state;
    protected $m_context;
    protected $m_rows;
    protected $m_haveHeader;
    /**
     * stored node for extra items state 
     * @var mixed
     */
    protected $m_node;
    protected $m_stores;
    const TREAT_METHOD = 'treat';

    /**
     * allow document link
     * @var ?bool
     */
    var $allowLinkDocument; 
    public function setEmojie(?array $emojies){
        $this->m_emojies = $emojies;
    }
    /**
     * flag: glue ltrim  output 
     */
    protected $m_ltrim = false;
    public function __construct()
    {
        $this->m_container = $this->_initialize();
        $this->m_classStyles = [];
        $this->m_stores = [];
    }
    /**
     * retrieve current output
     * @return null|string 
     */
    public function getOutput(): ?string
    {
        return $this->m_output;
    }
    public function saveState()
    {
        $this->m_stores[] = [
            'pos' => $this->m_lpos,
            'output' => $this->m_output,
            'render_state' => [
                $this->m_table,
                $this->m_rows,
                $this->m_ul,
                $this->m_li_depth,
                $this->m_li_item,
                $this->m_node,
                $this->m_state
            ]
        ];
        $this->m_output = '';
        $this->m_lpos = 0;
        $this->m_state = null;
        $this->m_node = null;
        $this->m_table = null;
        $this->m_rows = null;
        $this->m_li_item = null;
        $this->m_li_depth = null;
    }
    public function restoreState()
    {
        if ($p = array_pop($this->m_stores)) {
            $this->m_lpos = $p['pos'];
            $this->m_output = $p['output'];
            $render = $p['render_state'];
            $this->m_table = $render[0];
            $this->m_rows = $render[1];
            $this->m_ul = $render[2];
            $this->m_li_depth = $render[3];
            $this->m_li_item = $render[4];
            $this->m_node =  $render[5];
            $this->m_state =  $render[6];
        }
    }
    /**
     * setclass style 
     * @param 'ol'|'list'|'quote'|'bold'|'italic'|'table'|'ordered-list' $style 
     * @param mixed $def 
     * @return void 
     */
    public function setClassStyle(string $style, $def)
    {
        $this->m_classStyles[$style] = $def;
    }
    protected function _initialize()
    {
        $m = new RegexMatcherContainer;
        // + | --------------------------------------------------------------------
        // + | priority order 
        // + - table
        // + - header
        // + - list
        // + - orderd list
        // + | 
        // + |
        // $m->match("\\n","end-line");
        $m->match('(?:-+(?: )*\|){1,}(?:(?: )*-+)?(?=\\n)?', 'table-segment');
        $table_entry = $m->match('(?:(?:[^\\n\|]+)\|){1,}(?:[^\\n\|]+)?(?=\\n)?', 'table-entry')->last();
        $header = $m->match('^#{1,6}(?: (?P<title>.+))?', "text-header")->last();
        $quote = $m->match("^> .+", "text-quote")->last();
        $escaped_string = $m->referenceOnly()->match('\\\\.', "escaped");
        $mention_string = $m->match("@\b\w+\b", "at-mention")->last();
        $litteral_string = $m->begin("(\")", "\\1", "text-litteral-string")->last();
        $sub_list_item = $m->match('^\t+- .+(?=\n)?', "sub-list-item")->last();
        $sub_ordered_list_item = $m->match('(^\t+\*)[^\n]+', "sub-ordered-list-item")->last();
        $sub_ordered_list_item->captures = [
            1 => function ($v, $sourceValue, $captureIndex, $handle) {
                preg_match('/^\t+/', $v, $tab);
                $s = $tab[0];
                $count = strlen($s);
                $handle->depth = $count;
                return '';
            }
        ];
        //         $g = preg_match(RegexMatcherContainer::REGEX_START_LINE, $s);
        //         $s = preg_match('/(?<=^[\t])\t*\*.+/', "\t\t*rivc a info", $tab,  PREG_OFFSET_CAPTURE);
        //         print_r($tab);
        // igk_wln_e("data", $s, $tab);
        $ordered_list_item = $m->match('^[0-9]\. .+(?=\n)?', "order-list-item")->last();
        $fence_code = $m->begin("```(?P<name>\w+\b)?", "```", "fence-code")->last();
        $bold = $m->begin("(\\*\\*|__)", "\\1", "text-bold")->last();
        $task_list_item = $m->match("^- \[(?P<type> |x|-)\]\s+(?P<value>.+)(?=\\n)?", "task-list-item")->last();
        $list_item = $m->match("^- .+", "list-item")->last();
        $hr_item = $m->match("^---.*$", "hr")->last();
        $emphasis = $m->begin("(\\*|_)", "\\1", "text-italic")->last();
        $code_block = $m->begin('`', '`', 'code-block')->last();
        $empty_block = $m->appendEmptyLineDetection('empty-line')->last();
        $emoji_block = $m->match(":(\+\d|\b\w+\b):", "emoji")->last();
        $table_entry->patterns = $quote->patterns = [
            $litteral_string,
            $code_block,
            $bold,
            $emphasis,
            $emoji_block,
            $mention_string,
            $escaped_string
        ];
        $image_block = $m->match("!\[(?P<text>[^\\n]+)\]\((?P<uri>[^\(\)\\n]+)\)", "image")->last();
        $uri_block = $m->match("\[(?P<text>[^\\n]+)\]\((?P<uri>[^\(\)\\n]+)\)", "text-uri-block")->last();
        $emphasis->patterns = [
            $bold,
            $code_block,
            $litteral_string,
            $escaped_string
        ];
        $list_item->patterns =
            $task_list_item->patterns =
            $sub_list_item->patterns =
            $ordered_list_item->patterns =
            $sub_ordered_list_item->patterns = [
                $escaped_string,
                $bold,
                $emphasis,
                $code_block,
                $emoji_block,
                $litteral_string,
                $escaped_string,
                $uri_block
            ];
        $bold->patterns = [
            $emphasis,
            $code_block,
            $emoji_block,
            $litteral_string,
            $escaped_string
        ];
        $context = [
            'text-italic' => '_treat_italic',
            'text-bold' => '_treat_bold',
            'code-block' => '_treat_codeblock'
        ];
        $this->m_context = $context;
        return $m;
    }
    /**
     * 
     * @param string $html 
     * @return string 
     */
    public function transformToHtml(string $html): string
    {
        $this->m_lpos = 0;
        $this->m_output = '';
        //  $this->m_container->host = $this;
        $this->m_container->matchPatternStateListener = $this;
        $this->m_container->ouputTreatmentListener = $this;
        $this->m_container->treat($html, [$this, self::TREAT_METHOD]);
        if ($this->m_state) {
            // depending on document state  
            $this->m_output .= $this->endStateState();// . $this->m_output;
            $this->m_state = null;
        }
        // + | last entry segment
        if ($str = substr($html, $this->m_lpos)) {
            $str = $this->default($str);
            $this->m_output .= $str;
        }
        return ltrim($this->m_output);
    }
    private function default(string $str, $force=false){
        if ($this->m_useTag || $force){
            $l = igk_create_node_arg($this->tag);
            $l->content = igk_str_rm_start($str, "\n", 1);
            return $l->render();
        }
        return $str;

    }
    protected function bindRow($table, $rows, $header = false)
    {
        $tr = $table->tr();
        foreach ($rows as $r) {
            $m = $header ? $tr->th() : $tr->td();
            $m->Content = $r;
        }
    }
    /**
     * local string to allow html content by skipping extra white space 
     * @param mixed $g 
     * @param mixed $next_pos 
     * @param mixed $data 
     * @param mixed &$pos 
     * @param mixed &$ch 
     * @return void 
     */
    public static function Skip($g, $next_pos, $data, &$pos, &$ch)
    {
        $tch = substr($data, $pos, $g->from - $pos);
        $tch = self::_StreatContent($tch);
        $ch .= $tch;
        $pos = $next_pos;
    }
    /**
     * 
     * @return void 
     */
    public function treat(RegexMatcherCapture $g, int &$next_pos, string &$data)
    {
        $fc = null; 
        $v_debug = igk_is_debug();
        $tid = $g->tokenID;
        if ($tid)
            $fc = igk_getv($this->m_context, $tid) ?? '_treat_' . ltrim(StringUtility::FuncName($tid), '_');
        if ($g->getisRootCaptured()) {
             $v_debug && Logger::info('captured : ' . $next_pos . ':' . $tid . ' : ' . App::Gets(App::BLUE_I, $g->value));
            switch ($tid) {
                default:
                    if ($fc && method_exists($this, $fc)) {
                        $tc = '';
                        self::Skip($g, $next_pos, $data, $this->m_lpos, $tc);
                        $not_empty = (strlen(trim($tc)) > 0);
                        if ($not_empty && $this->m_ltrim) {
                            $tc = ltrim($tc);
                            $this->m_ltrim = false;
                            $this->_rtrimOutput();
                        }
                        $this->m_useTag = $this->m_useTag || !in_array($tid,['emoji']);
                        $s = call_user_func_array([$this, $fc], [$g->value, $g]);
                        if (($this->m_ltrim) && !$not_empty){
                            $tc='';
                        }
                        $this->m_output .= $tc . $s;
                    }
                    break;
            }
        } else {
            if ($fc && method_exists($this, $fc)) {
                $v_debug && Logger::print('tokenID:: ' . $g->tokenID . sprintf(" value : [%s]" , $g->value));
                $s = call_user_func_array([$this, $fc], [$g->value, $g]);
                if (!empty($s)) {
                    $data = substr($data, 0, $g->from) . $s . substr($data, $g->to);
                    $next_pos = $g->from + strlen($s);
                }
            }
        }
    }
    /**
     * treat bold
     * @param mixed $v 
     * @return null|string 
     * @throws Exception 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _treat_bold($v)
    {
        $s  = igk_getv($this->m_classStyles, 'bold');
        $v = substr(substr($v, 2), 0, -2);
        if (empty(trim($v))) return '';
        if ($s) {
            $n = igk_create_node('b')->setAttributes($s);
            $n->setContent($v);
            return $n->render();
        }
        return sprintf('<b>%s</b>', $v);
    }
    /**
     * litteral string
     * @param mixed $v 
     * @return null|string 
     * @throws Exception 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _treat_text_litteral_string($v)
    {
        $n = new HtmlNode('span');
        $n['class'] = 's';
        $g = $v; // igk_str_remove_quote($v);
        $n->text($g);
        $this->_bindNodeAttributes($n, 'string');
        return $n->render();
    }
    protected function _bindNodeAttributes($n, string $name)
    {
        $attr = igk_getv($this->m_classStyles, $name);
        $attr && $n->setAttributes($attr);
    }
    protected function _treat_italic($v)
    {
        $s  = igk_getv($this->m_classStyles, 'italic');
        $v = substr(substr($v, 1), 0, -1);
        if (empty(trim($v))) return '';
        if ($s) {
            $n = igk_create_node('i')->setAttributes($s);
            $n->setContent($v);
            return $n->render();
        }
        return sprintf('<i>%s</i>', $v);
    }
    protected function _treat_codeblock($v)
    {
        $s  = igk_getv($this->m_classStyles, 'code');
        $v = substr(substr($v, 1), 0, -1);
        if (empty(trim($v))) return '';
        if ($s) {
            $n = igk_create_node('code')->setAttributes($s);
            $n->text($v);
            return $n->render();
        }
        return sprintf('<code>%s</code>', $v);
    }
    protected function _treat_at_mention($v)
    {
        $n = new HtmlNode('a');
        $n['href'] = $this->mentionBaseURL . '/' . $v;
        $n['title'] = '';
        $n->span()->setClass('mention')->text($v);
        return $n->render();
    }
    protected function _treat_table_entry($v)
    {
        $tab = array_map('trim', explode("|", $v));
        $sb = '';
        if ($this->m_state && ($this->m_state != 'table')) {
            $sb .= $this->endStateState();
        }
        if (is_null($this->m_table)) {
            // init table
            $s  = igk_getv($this->m_classStyles, 'tables');
            $this->m_haveHeader = false;
            $this->m_table = igk_create_node('table');
            $this->m_table->setAttributes($s);
        }
        $this->m_rows = $tab;
        if ($this->m_haveHeader) {
            HtmlUtils::BindRow($this->m_table, $this->m_rows, false);
            $this->m_rows = null;
        }
        $this->m_state = 'table';
        return $sb;
    }
    public function _treat_table_segment($v)
    {
        if ($this->m_table &&  $this->m_rows && !$this->m_haveHeader) {
            HtmlUtils::BindRow($this->m_table, $this->m_rows, true);
            $this->m_haveHeader = true;
            $this->m_rows = null;
        }
    }
    public function _treat_empty_line()
    {
        if ($this->m_state)
            return $this->endStateState();
    }
    protected function endStateState()
    {
        $sb = '';
        switch ($this->m_state) {
            case 'table':
                if ($this->m_rows) {
                    HtmlUtils::BindRow($this->m_table, $this->m_rows, false);
                }
                $sb = $this->m_table->render();
                break;
            case 'task-list':
            case 'list':
            case 'olist':
            case 'sublist':
                $sb =  $this->m_ul->render();
                break;
            case 'quote':
            default:
                if ($this->m_node) {
                    $sb = $this->m_node->render();
                }
                break;
        }
        $this->_rtrimOutput();
        $this->m_table = null;
        $this->m_rows = null;
        $this->m_haveHeader = null;
        $this->m_state = null;
        $this->m_ul = null;
        $this->m_li_item = null;
        $this->m_li_depth = null;
        return $sb;
    }
    protected function _treat_list_item(string $v)
    {
        $sb = '';
        $v = ltrim($v, '- ');
        if ($this->m_state && ($this->m_state != 'list')) {
            $sb .= $this->endStateState();
        }
        $this->m_state = 'list';
        if (is_null($this->m_ul)) {
            $attr = igk_getv($this->m_classStyles, 'list');
            $this->m_ul = igk_create_node('ul')->setClass('list');
            $attr && $this->m_ul->setAttributes($attr);
        }
        $this->m_ul->li()->setClass('i')->text($v);
    }
    protected function _treat_order_list_item(string $v)
    {
        $sb = '';
        $v = ltrim(preg_replace("/^\\d+. /", "", $v), ' ');
        if ($this->m_state && ($this->m_state != 'olist')) {
            $sb .= $this->endStateState();
        }
        $this->m_state = 'olist';
        if (is_null($this->m_ul)) {
            $tag = 'ol';
            $attr = igk_getv($this->m_classStyles, $tag);
            $this->m_ul = igk_create_node($tag);
            $attr && $this->m_ul->setAttributes($attr);
        }
        $this->m_li_item = $this->m_ul->li()->setClass('i');
        $this->m_li_item->text($v);
        return $sb;
    }
    private static function _ChainSubList($ul, $depth, $tag = 'ul')
    {
        /**
         * @var mixed
         */
        $li = null;
        while ($depth > 0) {
            $ci = null;
            if ($li == null)
                $ci = $ul->li();
            else
                $ci = $li->li();
            $li = $ci->add($tag)
                ->setClass('sub-' . $depth);
            $depth--;
        }
        return $li;
    }
    public function _treat_hr(){
        $n = igk_create_node('hr');
        $n['class'] = 'hrule';
        return $n->render();
    }
    protected function _treat_sub_list_item(string $v)
    {
        $sb = '';
        preg_match("/^(?:\t)+/", $v, $ct);
        $depth = strlen($ct[0]);
        $v = ltrim($v, "- \t");
        if ($this->m_state == 'list') {
            // + | chain subitem by default the depth is 1:
            // + |
            $this->m_li_item = static::_ChainSubList($this->m_ul, $depth);
        } else {
            if ($this->m_state && ($this->m_state != 'sublist')) {
                $sb .= $this->endStateState();
            }
            if (is_null($this->m_ul)) {
                $this->m_ul = igk_create_node('ul');
                $this->m_li_item = static::_ChainSubList($this->m_ul, $depth);
            } else {
                if (!is_null($this->m_li_depth)) {
                    if ($this->m_li_depth != $depth) {
                        // sub list update reference 
                        if ($depth > $this->m_li_depth) {
                            $this->m_li_item = static::_ChainSubList($this->m_li_item, $depth - $this->m_li_depth);
                        } else {
                            $j = $this->m_li_depth - $depth;
                            while ($j > 0) {
                                $this->m_li_item = $this->m_li_item->getParentNode()->getParentNode();
                                $j--;
                            }
                        }
                    }
                }
            }
        }
        $this->m_state = 'sublist';
        $this->m_li_item->li()->text($v);
        $this->m_li_depth = $depth;
    }
    protected function _treat_sub_ordered_list_item(string $v, $captures = null)
    {
        $sb = '';
        $depth = ($captures ? $captures->option->depth : null) ??  0;
        $tag = 'ol';
        if (!$depth) {
            $c = preg_match("/^(?:\t)+/", $v, $ct);
            if (!$c) {
                return;
            }
            $depth = strlen($ct[0]);
            $v = ltrim($v, "- \t");
        }
        if ($this->m_state == 'olist') {
            // chain subitem by default the depth is 1:
            // if 
            $this->m_li_item = static::_ChainSubList($this->m_li_item->add($tag), $depth - 1, $tag);
        } else {
            if ($this->m_state && ($this->m_state != 'sublist')) {
                $sb .= $this->endStateState();
            }
            if (is_null($this->m_ul)) {
                $this->m_ul = igk_create_node($tag);
                $this->m_li_item = static::_ChainSubList($this->m_ul, $depth, $tag);
            } else {
                if (!is_null($this->m_li_depth)) {
                    if ($this->m_li_depth != $depth) {
                        // sub list update reference 
                        if ($depth > $this->m_li_depth) {
                            $this->m_li_item = static::_ChainSubList($this->m_li_item, $depth - $this->m_li_depth, $tag);
                        } else {
                            $j = $this->m_li_depth - $depth;
                            while ($j > 0) {
                                $this->m_li_item = $this->m_li_item->getParentNode();
                                $j--;
                            }
                        }
                    }
                }
            }
        }
        $this->m_state = 'sublist';
        $this->m_li_item->li()->text($v);
        $this->m_li_depth = $depth;
    }
    /**
     * remove output trailing white space
     * @return void 
     */
    private function _rtrimOutput()
    {
        $this->m_output = rtrim($this->m_output);
    }
    private function _closeState(){
        if ($this->m_state){
            $this->m_output .= $this->endStateState(); 
        }
    }
    /**
     * treat text header 
     * @param string $v 
     * @return mixed|void 
     * @throws Exception 
     */
    protected function _treat_text_header(string $v)
    {
        if (empty($c = ltrim($v, '# '))){
            return '';
        }
        $this->_closeState();
     
        $regex = "/^#{1,6} /";
        preg_match($regex, $v, $tab);
        $count = strlen(trim($tab[0]));
        $v = ltrim(preg_replace($regex, '', $v));

        // header convert to local document action 
        $v_slug = $this->_slugify($v);

        if (!empty($v)) {
            $v = self::_StreatContent($v);
            $attr = igk_getv($this->m_classStyles, 'header');
            $n = igk_create_node('h' . $count)->setContent($v);
            if ($attr) {
                $n->setAttributes($attr);
            }
            if ($v_slug && $this->allowLinkDocument){
                if (!isset($this->documentLinks[$v_slug])){
                    $n->setAttribute('id', $v_slug);
                    $this->documentLinks[$v_slug]=1;
                }
            }
            $this->_rtrimOutput();
            $this->m_ltrim = true;
            return $n->render();
        }
    }
    protected function removeAccents($text){
        return StringUtility::RemoveAccents($text);
       
    }
    protected function _slugify(string $v){
        $text =  $v; // trim(strtolower(preg_replace('/\\s+/', '-', $v)));

        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = $this->removeAccents($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
        return $text;
    }
    protected function _treat_text_quote(string $v)
    {
        $v = ltrim($v, '> ');
        $sb = '';
        if (!empty($v)) {
            $n = $this->m_state;
            if ($n == 'quote') {
                $this->m_node->bind(igk_create_node('br') ?? ' ', igk_html_node_text($v));
            } else {
                if ($n) {
                    $sb = $this->endStateState();
                }
                $tag = 'blockquote';
                $attr = igk_getv($this->m_classStyles, $tag);
                $n = igk_create_node($tag)->setContent($v);
                if ($attr) {
                    $n->setAttributes($attr);
                }
                $this->m_node = $n;
                $this->m_state = 'quote';
            }
            return $sb;
        }
    }
    protected function _treat_text_uri_block($v, $captures = null)
    {
        $tag = 'a';
        $attr = igk_getv($this->m_classStyles, $tag);
        $a = igk_create_node($tag);
        $attr && $a->setAttributes($attr);
        if ($captures) {
            $c = $captures->beginCaptures['text'][0];
            $u = $captures->beginCaptures['uri'][0];
            $a["href"] = $u;
            $a->text($c);
        }
        return $a->render();
    }
    /**
     * treat image content
     * @param mixed $v 
     * @param mixed $captures 
     * @return null|string 
     * @throws Exception 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _treat_image($v, $captures = null)
    {
        $tag = 'img';
        $attr = igk_getv($this->m_classStyles, $tag);
        $a = new HtmlNode($tag);
        $attr && $a->setAttributes($attr);
        if ($captures) {
            $c = $captures->beginCaptures['text'][0];
            $u = $captures->beginCaptures['uri'][0];
            $a["src"] = $u;
            $a['alt'] = $c;
        }
        return $a->render();
    }
    public function _treat_escaped($v)
    {
        return stripslashes($v);
    }
    /**
     * treat content.
     * @param string $v 
     * @return string|string[]|null 
     */
    public static function _StreatContent(string $v)
    {
        return preg_replace("/\\s+/", " ", $v);
    }
    /**
     * treat block code 
     * @param mixed $v 
     * @param mixed $captures 
     * @return null|string|void 
     * @throws Exception 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _treat_fence_code($v, $captures = null)
    {
        $tname = $captures ? igk_getv($captures->beginCaptures, 'name') : null;
        $name = $tname ? $tname[0] : '';
        $v = trim(igk_str_rm_start(trim($v, '```'), $name));

        if ($name){
            if ($this->useCodeFormatter){
                
            }
        }


        if (!empty($v)) {
            $n = new HtmlNode('code');
            $attr_class = igk_getv($this->m_classStyles, 'code-fence') ?? "igk-code -code-php " . ($name ? 'code-' . $name : '');            
            $n->setClass($attr_class);
            $n->text($v);
            $this->m_ltrim = true;
            return $n->render();
        }
    }
    /**
     * treat task list item capture
     * @param string $v 
     * @param RegexMatcherCapture $captures 
     * @return void 
     */
    protected function _treat_task_list_item(string $v, $match = null)
    {
        $type = !$match ? '' : $match->captures['type'][0];
        $v_v = !$match ? '' : $match->captures['value'][0];;
        if (empty(trim($v_v))) {
            return;
        }
        $type = igk_getv(['-' => 'progress', 'x' => 'complete', ' ' => 'start'], $type);
        $sb = '';
        if ($this->m_state && $this->m_state != 'task-list') {
            $sb = $this->endStateState();
        }
        $this->m_state = 'task-list';
        if (is_null($this->m_ul)) {
            $this->m_ul = igk_create_node('ul');
            $this->m_ul->setClass('igk-task-list');
            $attr = igk_getv($this->m_classStyles, 'task-list');
            $attr && $this->m_ul->setAttributes($attr);
        }
        $attr = igk_getv($this->m_classStyles, 'task-list-item');
        $li = $this->m_ul->li();
        if ($type) {
            $li->setClass('type-' . $type);
        }
        $attr && $li->setAttributes($attr);
        $li->text($v_v);
        $this->m_li_item = $li;
        return $sb;
    }
    /**
     * 
     * @param string $v 
     * @param mixed $match 
     * @return void 
     */
    protected function _treat_emoji(string $v, $match = null)
    {
        $s = igk_getv(array_merge([
            ':cup:' => '☕️',
            ':cmrflag' => "🇨🇲",
            ':+1:' => '👍',
            ':metal:' => '🤟',
            ':disc:' => '📀',
            ':octocat:' => '-',
            ':drink:' => '🥤',
            ':birthday-cake:' => '🎂',
            ':joy:'=>'😀'
        ], $this->m_emojies ?? []), $v);
        return $s;
    }
}