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
use IGK\System\Text\IRegexMatchPatternOutpuTreatmentListener;
use IGK\System\Text\IRegexMatchPatternStateListener;
use IGK\System\Text\RegexMatcherCapture;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use IGKException;
use IGKServices;
use ReflectionException;
 
/**
 * helper use to convert markdown text to html
 * @package IGK\System\IO\Markdown
 * @author C.A.D. BONDJE DOUE
 */
class MarkdownConverter implements IRegexMatchPatternStateListener, IRegexMatchPatternOutpuTreatmentListener
{

/**
 * INLINE ITEM BLOCK
 */
    const MARK_ITEM_INLINE = 0;
    /**
     * single item block
     */
    const MARK_ITEM_BLOCK = 1;

    /**
    * Constant: fence marker.
    * @var mixed
    */
    const FENCE_MARKER = '```';

    /**
    * Constant: br.
    * @var mixed
    */
    const BR = "<br/>";

    /**
    * Listener: set output treatment listener.
    * @var mixed
    */
    private $m_setOutputTreatmentListener;

    /**
    * auto generate doc.
    * @param mixed $listener
    * @return void
    */

    public function setOutputTreatmentListener($listener)
    {
        if ($this->m_setOutputTreatmentListener  && ($this->m_setOutputTreatmentListener !==$listener)){
            $this->m_setOutputTreatmentListener->appendOutputListener = null;
        }
        if ($this->m_setOutputTreatmentListener = $listener){
            $listener->appendOutputListener = function($s){
                $this->_appendToOutput($s);
            };
        }
    }

    /**
     * line feed flag 
     * @var ?bool
     */
    private $m_line_feed;
    /**
     * the id reference 
     * @var ?string
     */
    private $m_ref_id;

    /**
     * use code formatter service
     * @var ?bool
     */
    var $useCodeFormatter;

    /**
     * inner text in tag definition 
     * @var ?bool
     */
    var $encapsulateTextInTag;
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
     * use to add br tag if line break
     * @var mixed
     */
    var $allowBreakLine;

    /**
     * allow document link
     * @var ?bool
     */
    var $allowLinkDocument;


    /**
     * format codeblock on server 
     * @var ?bool
     */
    var $formatCodeBlock;
    /**
     * store document link
     * @var ?array
     */
    var $documentLinks;
    /**
     * store emojies
     * @var emojies[]
     */
    private $m_emojies;

    /**
    * auto generate doc.
    * @var RegexMatcherContainer
    */
    private $m_container;
    /**
     * line feed flags
     * @var mixed
     */
    private $m_lf;

    /**
    * auto generate doc.
    * @var int
    */
    private $m_lpos;

    /**
    * auto generate doc.
    * @var ?string
    */
    private $m_output;
    /**
     * ul/ol root node
     * @var ?HtmlNode
     */
    private $m_ul;

    /**
    * auto generate doc.
    * @var IGK\System\IO\Markdown\m_useTag
    */
    private $m_useTag;

    /**
     * code formatter engine
     * @var mixed
     */
    private $m_codeFormatter;

    /**
    * auto generate doc.
    * @var a
    */
    private $m_classStyles;

    /**
    * Property: li item.
    * @var mixed
    */
    private $m_li_item;

    /**
    * Property: li depth.
    * @var mixed
    */
    private $m_li_depth;
    /**
     * flag to set is in buffer 
     * @var ?bool
     */
    private $m_sub;
    /**
     * content of the buffer 
     * @var string
     */
    private $m_buffer;
    /**
     * single definition flag. use to mark element
     * @var mixed
     */
    private $m_is_single_definition;
    /**
     * table state definition 
     * @var mixed
     */
    protected $m_table;
    /**
     * number of column in table 
     * @var mixed
     */
    protected $m_table_col;
    /**
     * treatment state 
     * @var 'quote' | 'table' | 'list' | 'sublist' 
     */
    protected $m_state;

    /**
    * Property: context.
    * @var mixed
    */
    protected $m_context;

    /**
    * Property: rows.
    * @var mixed
    */
    protected $m_rows;

    /**
    * Property: have header.
    * @var mixed
    */
    protected $m_haveHeader;
    /**
     * stored node for extra items state 
     * @var mixed
     */
    protected $m_node;

    /**
    * Property: stores.
    * @var mixed
    */
    protected $m_stores;

    /**
    * Constant: treat method.
    * @var mixed
    */
    const TREAT_METHOD = 'treat';

    /**
    * Sets Emojie.
    * @param null|array $emojies
    */
    public function setEmojie(?array $emojies)
    {
        $this->m_emojies = $emojies;
    }
    /**
     * flag: glue with left trimmed output 
     */
    protected $m_ltrim = false;

    /**
    * .ctr
    */
    public function __construct()
    {
        $this->m_container = $this->_initialize();
        $this->m_classStyles = [];
        $this->m_stores = [];
        $this->m_buffer = '';
        $this->allowBreakLine = true;
        $this->formatCodeBlock = false;
    }
    /**
     * retrieve current output
     * @return null|string 
     */

    public function getOutput(): ?string
    {
        return $this->m_output;
    }

    /**
    * auto generate doc.
    * @return void
    */

    public function saveState()
    {
        $this->m_stores[] = [
            'pos' => $this->m_lpos,
            'output' => $this->m_output, // current output 
            'buffer' => $this->m_buffer, // save buffer 
            'lf' => $this->m_lf,
            'is_single_definition' => $this->m_is_single_definition,
            'render_state' => [
                $this->m_table,
                $this->m_rows,
                $this->m_ul,
                $this->m_li_depth,
                $this->m_li_item,
                $this->m_node,
                $this->m_state,
                $this->m_table_col,
            ]
        ];
        $this->m_output = '';
        $this->m_buffer = '';
        $this->m_lpos = 0;
        $this->m_state = null;
        $this->m_node = null;
        $this->m_table = null;
        $this->m_table_col = null;
        $this->m_rows = null;
        $this->m_li_item = null;
        $this->m_li_depth = null;
        $this->m_is_single_definition = true;
        $this->m_lf = false;
    }

    /**
    * auto generate doc.
    * @return bool
    */

    protected function getStateDepth(): bool
    {
        return count($this->m_stores) > 0;
    }
    /**
     * restore state
     * @return void 
     */

    public function restoreState()
    {
        if ($p = array_pop($this->m_stores)) {
            $this->m_lpos = $p['pos'];
            $this->m_output = $p['output'];
            $this->m_is_single_definition = $p['is_single_definition'];
            $this->m_buffer = $p['buffer'];
            $this->m_lf = $p['lf'];

            $render = $p['render_state'];
            $this->m_table = $render[0];
            $this->m_rows = $render[1];
            $this->m_ul = $render[2];
            $this->m_li_depth = $render[3];
            $this->m_li_item = $render[4];
            $this->m_node =  $render[5];
            $this->m_state =  $render[6];
            $this->m_table_col = $render[7];
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
    /**
     * TreatMarkdowSubItem list 
     * @param string $value 
     * @return ?object 
     */

    public static function TreatMarkdownSubItem(string $value): ?object{
        $depth = 0;
        if (preg_match("/^(?P<depth>( {4}|\\t)+)?- /", $value, $tab)) {
            if ($c = igk_getv($tab, 'depth')) {
                $depth = strlen($c) * ($c[0] == "\t" ? 1 : 1/4.0);
            }
            $value = substr($value, strlen($tab[0]));
        }else{
            if (preg_match("/^\\s*(-|\\d+\\.) /", $value, $tab)){
                $value = substr($value, strlen($tab[0]));
            }else{
                return null;
            }
        }
        return (object)compact('depth', 'value');
    }

    /**
    * Initialize.
    */
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
        $quote = $m->match("^> (.+)?", "text-quote")->last();
        $v_tag_defition = $m->match('(<|>|≤)', 'tag-definition')->last();
        $m->match('^(\|| *)?(?:-+(?: )*\|){1,}(?:(?: )*-+( )*)?(?=\\n)?', 'table-segment');
        $table_entry = $m->match('^(\|)?(?:(?:[^\\n\|]+)\|){1,}(?:[^\\n\|]+)?(?=\\n)?', 'table-entry')->last();
        $header = $m->match('^#{1,6}(?: (?P<title>.+))?', "text-header")->last();

       
        

        $escaped_string = $m->referenceOnly()->match('\\\\.', "escaped");
        $mention_string = $m->match("@\b\w+\b", "at-mention")->last();
        $litteral_string = $m->begin("(\")", "\\1", "text-litteral-string")->last();
        $sub_list_item = $m->match('^(?P<depth>( {4}|\\t)+)- .+(?=\n)?', "sub-list-item")->last();
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
        $ordered_list_item = $m->match('^[0-9]+\. .+(?=\n)?', "order-list-item")->last();
        $fence_code = $m->begin("```(?P<name>\w+\b)?", "```", "fence-code")->last();
        $bold = $m->begin("(\\*\\*|__)", "\\1", "text-bold")->last();
        $task_list_item = $m->match("^- \[(?P<type> |x|-)\]\s+(?P<value>.+)(?=\\n)?", "task-list-item")->last();
        $list_item = $m->match("^- .+", "list-item")->last();
        $hr_item = $m->match("^---.*$", "hr")->last();

        $v_word = $m->createPattern(['match' => "[\\w_\\$][a-zA-Z0-9_\\-\\$]*", "tokenID" => "word"]); // ->last();
        $emphasis = $m->begin("(\\*|_)", "\\1", "text-italic")->last();
        $code_block = $m->begin('`', '`', 'code-block')->last();
        $empty_block = $m->appendEmptyLineDetection('empty-line')->last();
        $m->match('\\n', 'line-feed')->last();
        $emoji_block = $m->match(":(\+\d|\b\w+\b):", "emoji")->last();
        $table_entry->patterns =  [
            $litteral_string,
            $code_block,
            $bold,
            $emphasis,
            $emoji_block,
            $mention_string,
            $escaped_string,
            $fence_code,
            $v_tag_defition
        ];
        $litteral_string->patterns = array_merge( $litteral_string->patterns ??[], [
            $bold,
            $emphasis,
        ]);

        $quote->patterns = [
            $litteral_string,
            $code_block,
            $bold,
            $emphasis,
            $emoji_block,
            $mention_string,
            $escaped_string,
            $fence_code,
        ];
        $image_block = $m->match("!\[(?P<text>[^\\n]+)\]\((?P<uri>[^\(\)\\n]+)\)", "image")->last();
        $uri_block = $m->match("\[(?P<text>[^\\n]+)\]\((?P<uri>[^\(\)\\n]+)\)", "text-uri-block")->last();


        $emphasis->patterns = [
            $bold,
            $code_block,
            $litteral_string,
            $escaped_string,
            $v_word,
            $v_tag_defition
        ];
        $list_item->patterns =
            $task_list_item->patterns =
            $sub_list_item->patterns =
            $ordered_list_item->patterns =
            $sub_ordered_list_item->patterns = [
                $escaped_string,
                $v_word,
                $bold,
                $emphasis,
                $code_block,
                $emoji_block,
                $litteral_string,
                $escaped_string,
                $uri_block,
                $image_block
            ];
        $bold->patterns = [
            $emphasis,
            $code_block,
            $emoji_block,
            $litteral_string,
            $escaped_string,
            $v_word
        ];
        $context = [
            'text-italic' => '_treat_italic',
            'text-bold' => '_treat_bold',
            'code-block' => '_treat_codeblock'
        ];
        $fence_code->patterns = [
            $v_tag_defition
        ];
        $code_block->patterns = [
            $v_tag_defition
        ];
        $cp = $m->begin('\[\\\\([a-zA-Z\\-]+)\](?=\\{)', '(?<=\\})', 'md-instruction-start')->last();
        $cp->patterns = [
            $m->createStringPattern(),
            $m->createPattern([
                'tokenID'=>'instruct-block',
                'begin'=>"\(",
                'end'=>"\)"
            ]),
        ];
        $m->match('\[\\\\([a-zA-Z\\-]+)\](?:\{([^\\}]+)\}|\(\))?', 'md-instruction');
        $m->match('(\\$)?[a-zA-Z_0-9]+', 'skip-word-match');

        $header->patterns = [
            $code_block,
            $m->createPattern([
               'begin' => '\{\\s*#',
               'end' => '\}',
               'tokenID' => 'header-ref-id'
           ])
       ];
        $header->patterns[] = $uri_block;

        
        $this->m_context = $context;
        return $m;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    * @return mixed
    */

    protected function _treat_word($v)
    {
        return $v;
    }
    /**
     * to debug append 
     * @param string $s 
     * @return void 
     */

    protected function _appendToOutput(string $s)
    {
        $this->m_output .= $s;

        // + | --------------------------------------------------------------------
        // + | RESET FLAGS
        // + | 
        $this->m_ltrim = false;
    }
    /**
     * transform markdown
     * @param string $markdown 
     * @param null|IRegexMatchPatternOutpuTreatmentListener $outputTreatment 
     * @param null|IRegexMatchPatternStateListener $matchPatternStateListener 
     * @return string 
     */

    public function transform(
        string $markdown,
        ?\IGK\System\Text\IRegexMatchPatternOutpuTreatmentListener $outputTreatment,
        ?\IGK\System\Text\IRegexMatchPatternStateListener $matchPatternStateListener
    ): string {
        $this->_prepare_transform();
        $this->m_container->matchPatternStateListener = $matchPatternStateListener ?? $this;
        $this->m_container->ouputTreatmentListener = $outputTreatment ?? $this;
        return $this->_treat_data($markdown);
    }
    private function _prepare_transform()
    {
        $this->m_lpos = 0;
        $this->m_output = '';
        $this->m_buffer = '';
        $this->m_is_single_definition = true;
    }
    /**
     * transform markdown to html
     * @param string $markdown mardown to transform to html 
     * @return string 
     */

    public function transformToHtml(string $markdown): string
    {
        $this->_prepare_transform();
        //  $this->m_container->host = $this;
        $this->m_container->matchPatternStateListener = $this;
        $this->m_container->ouputTreatmentListener = $this;
        return $this->_treat_data($markdown);
         
    }

    /**
    * Treat data.
    * @param mixed $markdown
    * @return string
    */
    protected function _treat_data($markdown): string
    {
        $this->m_container->treat($markdown, [$this, self::TREAT_METHOD]);
        $fc_handle_single = null;
        $fc_posttreat_output = null;
        // + | last entry segment
        $gs = [];
        if ($sb = $this->m_buffer) {
            // fix multi space at end of the document 
            $sb = preg_replace("/(\\n\\s*)+$/m", "\n", $sb);
            $gs[] = $sb;
            // clear remaining buffer 
            $this->m_buffer = '';
        }
        if ($l = $this->m_setOutputTreatmentListener){
            if ($s = $l->endState()){
                $gs[0] = $s.igk_getv($gs, 0, ''); 
            }
            $fc_handle_single = function(& $output)use($l){
                $l->didHandleOutput($this->m_is_single_definition, $output);
            };

            $fc_posttreat_output = (function(& $output){
                /**
                 * @var mixed $g
                 */
                $g = $this;
                $output = $g->postTreatOutput($output);
            })->bindTo($l);
        }
        if ($this->m_state) {
            // depending on document state  
            if ($gs) {
                $this->_appendToOutput($gs[0]);
                $gs = [];
            }
            $this->_appendToOutput($this->endStateState());
            $this->m_state = null;
        }
        if (($str = substr($markdown, $this->m_lpos)) || $gs){
            if ($fc_handle_single && !empty($str)){
                $str = $this->default($str, true);
            }
            if ($gs)
                $gs[0] .=  $str;
            else
                $gs[0] = $str;
            $str = $gs[0]; 
            $this->_rTrimOutput($this->m_output);
            $fc_handle_single && $fc_handle_single($this->m_output);
            if (!$this->getIsSingleDefinition()) {
                $str = $this->default($str);
            }
            $this->_appendToOutput($str);
        }
        if ($fc_posttreat_output){
            $fc_posttreat_output($this->m_output);
        }
        return ltrim($this->m_output);
    }
    /**
     * 
     * @param mixed $n 
     * @param mixed ...$args 
     * @return object|null 
     */
    private function _handle_outputstream($n, ...$args)
    {
        if ($l = $this->m_setOutputTreatmentListener) {
            $g = call_user_func_array([$l, $n],  $args);
            return (object)['output' => $g];
        }
    }
    /**
     * 
     * @param string $str 
     * @param bool $force 
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private function default(string $str, $force = false)
    {
        if ($this->m_useTag || $force) {
            $src = self::_StreatContent(igk_str_rm_start($str, "\n", 1));
            if ($c = $this->_handle_outputstream(__FUNCTION__, $src)) {
                return $c->output;
            }
            $l = igk_create_node_arg($this->tag);
            $l->content = $src;
            return $l->render();
        }
        return $str;
    }

    /**
    * auto generate doc.
    * @param bool $header
    * @return void
    */

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
    * auto generate doc.
    * @return void
    */

    public function treat(RegexMatcherCapture $g, int &$next_pos, string &$data)
    {
        $fc = null;
        $v_debug = igk_is_debug();
        $tid = $g->tokenID;
        $v_item_type = self::MARK_ITEM_INLINE;

        if ($tid) {
            $fc = igk_getv($this->m_context, $tid) ?? '_treat_' . ltrim(StringUtility::FuncName($tid), '_');
        }
        $is_sub = $this->getStateDepth();
        if ($g->getisRootCaptured()) {
            // + | --------------------------------------------------------------------
            // + | buffer manager  
            // + |
            $v_buffer = &$this->m_buffer;
            $v_is_linefeed = ($tid == 'line-feed');
            $v_close_buffer = false;
            $v_close_lf = false;
            $v_debug && Logger::info('root-captured: ' . $next_pos . ':' . $tid . ' : ' . App::Gets(App::BLUE_I, json_encode($g->value)));
            if ($this->m_line_feed) {
                if (!empty(trim($g->value))) {
                    $v_debug && Logger::warn("::> close line-feed");
                    $this->m_line_feed = false;
                    //$this->m_lf = false;
                    $v_close_lf = true;
                }
            }
            $l = $this->m_setOutputTreatmentListener;
            if ($fc && method_exists($this, $fc)) {
                $tc = '';
                self::Skip($g, $next_pos, $data, $this->m_lpos, $tc);
                // Logger::warn('MDConverter - skip : '. $this->m_lpos);
                $not_empty = (strlen(trim($tc)) > 0);
                if ($not_empty && $this->m_ltrim) {
                    $tc = ltrim($tc);
                    $this->m_ltrim = false;
                    $this->_rtrimOutput();
                }
                $this->m_useTag = $this->m_useTag || !in_array($tid, ['emoji']);
                // + | mark mode 
                $this->m_markmode = null;
                $s = $this->_treat_callback($fc, $g, true, $is_sub); //call_user_func_array([$this, $fc], [$g->value, $g]);
                $v_item_type = $this->m_markmode ?? $this->_getMarkMode($tid);
                $v_close_buffer = !$is_sub && ($v_item_type != self::MARK_ITEM_INLINE);
                $u_state = true;
                if ($this->m_ltrim && !$not_empty) {
                    $tc = '';
                    $u_state = $this->m_state && $this->continueStateAgainst($tid);
                } else {
                    // + | update to fix state 2025
                    $v_continue_state = ($this->m_state && $this->_continue_update($tid));
                    $u_state = !empty($tc) || (!$v_continue_state &&  ($v_close_buffer || $v_close_lf));
                }
                if ($u_state && $this->m_state) {
                    if (!empty($v_buffer)) {
                        $this->_appendToOutput($v_buffer);
                        $v_buffer = '';
                    }
                    $this->_appendToOutput($this->endStateState());
                }
                // + | prepend to buffer line 
                if (!empty($tc)) {
                    $this->_appendBufferLine($this->m_lf, $v_buffer);
                    $tc = $this->_prepareTextBeforeAppendToBuffer($tc);
                    $this->_appendToBuffer($tc);
                }
                if ($v_close_buffer) {

                    $tc = !empty(trim($v_buffer)) ? $this->default($v_buffer) : '';
                    $this->_clearBuffer(); 
                    $this->m_lf = false;
                    $this->_appendToOutput($tc . $s);
                    $this->setIsSingleDefinition(false);
                } else {
                    if ($is_sub) {
                        $this->_appendToOutput($tc . $s);
                    } else {
                        if ($v_is_linefeed) {
                            $this->_lineFeedToBuffer();
                            $this->m_lf = true; // mark for next line - 
                        } else {
                            if ($s) {
                                if (empty($v_buffer) && ($v_item_type == self::MARK_ITEM_BLOCK)) {
                                    $this->_appendToOutput($s);
                                } else {
                                    $this->_appendToBuffer($s); 
                                    $this->m_lf = false;
                                }
                            }
                        }
                    }
                }
            } else {
                $l && $l->beforeBufferLine($g, $this, $this->m_lf);
                $this->_appendBufferLine($this->m_lf, $v_buffer);
            }
        } else {
            if ($fc && method_exists($this, $fc)) {
                $v_debug && Logger::info('[mdc] tokenID: ' . $g->tokenID . sprintf(": value : [%s]", $g->value));
                $s = $this->_treat_callback($fc, $g, false, $is_sub); // call_user_func_array([$this, $fc], [$g->value, $g]);
                if (!empty($s)) { // change the definition 
                    $data = substr($data, 0, $g->from) . $s . substr($data, $g->to);
                    $next_pos = $g->from + strlen($s);
                }
            }
        }
    }

    /**
    * Prepare text before append to buffer.
    * @param mixed $tc
    */
    protected function _prepareTextBeforeAppendToBuffer($tc){
        if ($l = $this->m_setOutputTreatmentListener){
            $tc = $l->prepareTextBeforeAppendToBuffer($tc);
        }
        return $tc;
    }

    /**
    * Treat md instruction.
    */
    protected function _treat_md_instruction(){
        return null;
    }

    /**
    * Treat md instruction start.
    */
    protected function _treat_md_instruction_start(){
        return null;
    }

    /**
    * auto generate doc.
    * @return void
    */

    protected function _lineFeedToBuffer(){
        if ($l = $this->m_setOutputTreatmentListener){
            if ($s = $l->endLineFeedToBuffer($this->m_lf)){
                $this->_appendToBuffer($s);
            }
        }
    }
    /**
     * set output is single definition 
     * @param mixed $v 
     * @return void 
     */

    protected function setIsSingleDefinition($v){
        $this->m_is_single_definition = $v;
    }

    /**
    * Returns Is Single Definition.
    */
    protected function getIsSingleDefinition(){
        return $this->m_is_single_definition; 
    }
    private function _clearBuffer(){
        $this->m_buffer = '';
    }
    /**
     * append to current buffer 
     * @param string $s 
     * @return void 
     */
    private function _appendToBuffer(string $s){
        $this->m_buffer.= $s;
    }
    /**
     * 
     * @param string $fc 
     * @param mixed $g 
     * @param bool $isroot 
     * @return mixed|null 
     */
    private function _treat_callback(string $fc, $g, bool $isroot, bool $is_substate){
        $fc_call = function()use($fc, $g){
            return call_user_func_array([$this, $fc], [$g->value, $g]);
        };
        if ($l = $this->m_setOutputTreatmentListener){
            $options = [
                'buffer'=>& $this->m_buffer,
                'output'=>& $this->m_output,
                'isSubState'=>$is_substate
            ];
            if ($r = $l->filter($g->tokenID, $g->value, $isroot, $fc_call,  $g, $options)){
                if (!is_bool($r)){
                    return $r->output;
                }
                return;
            }
        }
        return $fc_call();  

    }
    /**
     * 
     * @return bool 
     */

    protected function _continue_update(string $tid): bool
    {
        $t = $this->m_state;
        if (method_exists($this, $fc = __FUNCTION__ . '_' . StringUtility::FuncName($t))) {
            return call_user_func_array([$this, $fc], [$tid]);
        }
        return false;
    }

    /**
    * Property: markmode.
    * @var mixed
    */
    private $m_markmode;

    /**
    * auto generate doc.
    * @param string $tid
    * @return 1|0
    */

    protected function _getMarkMode(string $tid)
    {
        // if (in_array($tid, explode('|', 'text-header|fence-code|table-entry|hr|list-item'))) {
        if (in_array($tid, explode('|', 'text-header|text-quote|fence-code|table-entry|hr|list-item'))) {
            return self::MARK_ITEM_BLOCK;
        }
        return self::MARK_ITEM_INLINE;
    }

    /**
    * auto generate doc.
    * @param string &$buffer
    * @return void
    */

    protected function _appendBufferLine(?bool &$lf, string &$buffer)
    {

        if ($this->allowBreakLine && $lf && !empty($buffer)) {
            $lf = (($l = $this->m_setOutputTreatmentListener) ? 
                    $l->lf : null) ?? self::BR;
            $buffer .= $lf;
        }
        $lf = false;
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
     * retrieve the setting to add 
     * @param string $key 
     * @param null|string $default 
     * @return null|string 
     */

    public function getSetting(string $key, ?string $default = null)
    {

        return $default;
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
        $g = htmlentities($v);
        $n->text($g);
        $this->_bindNodeAttributes($n, 'string');
        return $n->render();
    }

    /**
    * Bind node attributes.
    * @param mixed $n
    * @param string $name
    */
    protected function _bindNodeAttributes($n, string $name)
    {
        $attr = igk_getv($this->m_classStyles, $name);
        $attr && $n->setAttributes($attr);
    }

    /**
    * Treat italic.
    * @param mixed $v
    */
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

    /**
    * Treat codeblock.
    * @param mixed $v
    */
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

    /**
    * Treat at mention.
    * @param mixed $v
    */
    protected function _treat_at_mention($v)
    {
        $n = new HtmlNode('a');
        $n['href'] = $this->mentionBaseURL . '/' . $v;
        $n['title'] = '';
        $n->span()->setClass('mention')->text($v);
        return $n->render();
    }

    /**
    * Treat table entry.
    * @param mixed $v
    */
    protected function _treat_table_entry($v)
    {
        $v = trim($v);
        $v = igk_str_rm_start(igk_str_rm_last($v, '|', 1), '|', 1);
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
            $this->m_table_col = count($tab);
        }
        while ($this->m_table_col != count($tab)) {
            $tab[] = '';
        }
        $this->m_rows = $tab;
        if ($this->m_haveHeader) {
            HtmlUtils::BindRow($this->m_table, $this->m_rows, false);
            $this->m_rows = null;
        } else {
            HtmlUtils::BindRow($this->m_table, $this->m_rows, true);
            $this->m_haveHeader = true;
            $this->m_rows = null;
        }
        $this->m_state = 'table';
        return $sb;
    }

    /**
    * Treat table segment.
    * @param mixed $v
    */
    public function _treat_table_segment($v)
    {
        if ($this->m_table &&  $this->m_rows && !$this->m_haveHeader) {
            HtmlUtils::BindRow($this->m_table, $this->m_rows, true);
            $this->m_haveHeader = true;
            $this->m_rows = null;
        }
    }

    /**
    * auto generate doc.
    * @return mixed|void
    */

    public function _treat_empty_line(): string
    {
        return (($this->m_state) ? $this->endStateState() : null) ?? '';
    }

    /**
    * auto generate doc.
    * @return void
    */

    protected function checkOutputDefinition()
    {
        if ($this->getIsSingleDefinition()) {
            if (!empty(trim($this->m_output))) {
                $this->m_output = $this->default($this->m_output);
            }
            $this->setIsSingleDefinition(false);
        }
    }
    /**
     * end state
     * @return string 
     */

    protected function endStateState(): string
    {
        $this->checkOutputDefinition();
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

    /**
    * auto generate doc.
    * @param string $v
    * @return void
    */

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

    /**
    * Treat order list item.
    * @param string $v
    */
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
        * auto generate doc.
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

    public function _treat_hr()
    {
        $n = igk_create_node('hr');
        $n['class'] = 'hrule';
        return $n->render();
    }

    /**
    * Treat sub list item.
    * @param string $v
    */
    protected function _treat_sub_list_item(string $v)
    {
        $sb = '';
        $c = MarkdownConverter::TreatMarkdownSubItem($v) ?? igk_die('not a valid sub list item value');
        list($depth, $v) = igk_extract($c, 'depth|value');
        // $p = preg_match("/^(?:\t)+/", $v, $ct);
        // $depth = strlen($ct[0]);
        // $v = ltrim($v, "- \t");
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

    /**
    * Treat sub ordered list item.
    * @param string $v
    * @param null|mixed $captures
    */
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
    * Continue state against.
    * @param string $nstate
    * @return bool
    */
    function continueStateAgainst(string $nstate): bool
    {
        if (in_array($nstate, ['fence-code', 'hr'])) {
            return true;
        }
        return false;
    }
    /**
     * remove output trailing white space
     * @return void 
     */
    private function _rtrimOutput()
    {
        if ($this->m_setOutputTreatmentListener) {
            $this->m_output = $this->m_setOutputTreatmentListener->rtrimOutput($this->m_output);
            return;
        }
        $this->m_output = rtrim($this->m_output);
    }
    /**
     * 
     * @return void 
     */
    private function _closeState()
    {
        if ($this->m_state) {
            $this->m_output .= $this->endStateState();
        }
    }

    protected function _updatePreviousOutput()
    {
        if (!$this->m_state) {
            $this->checkOutputDefinition();         
        }
    }
    /**
     * retrieve the stored listener 
     * @return mixed 
     */

    public function getOutputTreatmentListener(){
        return $this->m_setOutputTreatmentListener;
    }
    /**
     * treat text header 
     * @param string $v 
     * @return mixed|void 
     * @throws Exception 
     */

    protected function _treat_text_header(string $v)
    {
        if (empty(ltrim($v, '# '))) {
            return '';
        }
        $v = preg_replace('/ +/', ' ', $v);
        $this->_updatePreviousOutput();
        $this->_closeState();

        $regex = "/^#{1,6} /";
        preg_match($regex, $v, $tab);
        $count = strlen(trim($tab[0]));
        $v = ltrim(preg_replace($regex, '', $v));

        // header convert to local document action 
        $v_slug = $this->m_ref_id ?? $this->_slugify($v);
        $this->m_ref_id = null;

        if (!empty($v)) {
            $v = self::_StreatContent($v);
            $attr = igk_getv($this->m_classStyles, 'header');
            if ($v_slug && $this->allowLinkDocument) {
                if (!isset($this->documentLinks[$v_slug])) {
                    $this->documentLinks[$v_slug] = 1;
                    $attr['id'] = $v_slug;
                }
            }
            $this->_rtrimOutput();
            $this->m_ltrim = true;
            $this->setIsSingleDefinition(false);

            if ($this->m_setOutputTreatmentListener) {
                // $this->m_ltrim = false;
                return $this->m_setOutputTreatmentListener->title($v, $count, $v_slug);
            }
            $n = igk_create_node('h' . $count)->setContent($v);
            if ($attr) {
                $n->setAttributes($attr);
            }
            return $n->render();
        }
    }
    /**
     * check for updating table multi-section 
     * @param string $tid 
     * @return bool 
     */

    protected function _continue_update_table(string $tid): bool
    {
        return ($this->m_state == 'table') && (in_array($tid,  ['table-segment', 'table-entry']));
    }

    /**
    * Continue update list.
    * @param string $tid
    * @return bool
    */
    protected function _continue_update_list(string $tid): bool
    {
        return ($this->m_state == 'list') && (in_array($tid,  ['list-item']));
    }

    /**
    * Continue update quote.
    * @param string $tid
    * @return bool
    */
    protected function _continue_update_quote(string $tid): bool
    {
        return ($this->m_state == 'quote') && (in_array($tid,  ['text-quote']));
    }
    /**
     * remove accent
     * @param mixed $text 
     * @return string 
     */

    protected function removeAccents($text)
    {
        return StringUtility::RemoveAccents($text);
    }

    /**
    * Slugify.
    * @param string $v
    * @return string
    */
    protected function _slugify(string $v): string
    {   
        if (preg_match("/^[\\d\.]+ /", $v , $tab)){
            $g = $tab[0];
            $m = str_replace('.','', $g);
            $m = str_replace(' ','---', $m);
            $v = $m.StringUtility::Slugify(substr($v, strlen($g)));
            return $v;
        }

        return StringUtility::Slugify($v);
    }

    /**
    * Treat text quote.
    * @param string $v
    */
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

    /**
    * Treat text uri block.
    * @param mixed $v
    * @param null|mixed $captures
    */
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

    /**
    * Treat escaped.
    * @param mixed $v
    */
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
        //replace multiple empty line with only one empty line 
        return preg_replace("/\\n{2,}/", "\n", $v);
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
        $v = trim(igk_str_rm_start(trim($v, self::FENCE_MARKER), $name));

        if (!empty($v)) {
            $this->_updatePreviousOutput();
            $this->m_ltrim = true;
            $this->setIsSingleDefinition(false);
            if ($this->formatCodeBlock) {
                $engine = $this->m_codeFormatter;
                if (is_null($engine)) {
                    if ($service = IGKServices::Get(sprintf('formatters.%s', $name))) {
                        return $service->format($v);
                    }
                    $heighlight = igk_app()->getService(IGKServices::CORE_CODE_HIGHLIGHT) ?? igk_die('core code highlight is missing');
                    $v = $this->_beforeFormatCode($v, $name);
                    $v = $heighlight->format($v, $name);
                    $n = new HtmlNode('div');
                    $n->setClass($this->getSetting('code-box-classes', 'code-box'));
                    $n->text($v);
                    return $n->render();
                }
                return $v;
            }
            $n = new HtmlNode('code');
            $attr_class = igk_getv($this->m_classStyles, 'code-fence') ?? "igk-code -code-php " . ($name ? 'code-' . $name : '');
            $n->setClass($attr_class);
            // $n->text(htmlentities($v));
            $n->text($v);
            return $n->render();
        }
    }

    /**
    * auto generate doc.
    * @param mixed $type
    * @return string
    */

    protected function _beforeFormatCode($v, $type)
    {
        //igk_getv($this->treatFormatter, $name);

        return strtr($v, ['&gt;' => '>', '&lt;' => '<']);
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
    * auto generate doc.
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
            ':joy:' => '😀'
        ], $this->m_emojies ?? []), $v);
        return $s;
    }

    /**
    * auto generate doc.
    * @param mixed $e
    * @return string
    */

    protected function _treat_tag_definition(string $v, $e)
    {
        $rp = $e->match->replaceWith ?? ['<' => '&lt;', '>' => '&gt;'];
        $v = RegexMatcherUtility::ReplaceWithOnly($v, $rp, $e);
        return $v;
    }

    /**
    * auto generate doc.
    * @param mixed $e
    * @return string
    */

    protected function _treat_line_feed(string $v, $e): string
    {
        $lf = &$this->m_line_feed;
        if ($lf) {
            $this->_appendToOutput($this->_treat_empty_line());
            $lf = false;
            return '';
        }
        $lf = true;
        if ($this->m_state || $this->m_ltrim) {
            return '';
        }
        return $v;
    }
    /**
     * capture the header reference id
     * @param string $v 
     * @param mixed $e 
     * @return string 
     */

    protected function _treat_header_ref_id(string $v, $e)
    {
        $v = trim($v, '{} ');
        if (igk_str_startwith($v, '#'))
            $v = substr($v, 1);
        $this->m_ref_id = $v;
        return '';
    }
}
