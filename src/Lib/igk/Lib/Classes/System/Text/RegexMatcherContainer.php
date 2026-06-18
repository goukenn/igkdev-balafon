<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainer.php
// @date: 20240913 10:19:11
namespace IGK\System\Text;
use Closure;
use Exception;
use IGK\Helper\Activator;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption; 
use IGK\System\IO\File\TmLanguage\Converters\RegexMatcherContainerTmLanguageConverter;
use IGK\System\Text\RegexMatcherPattern;
use IGK\System\Text\IRegexMatchPatternOutpuTreatmentListener;
use IGKException;
use IGKServices;
use stdClass;
// + | --------------------------------------------------------------------
// + | - priority to end regex
// + |

/**
 * extract definitio beetween begin/end definition 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherContainer implements IRegexMatcherContainer
{
    /**
     * Constant: regex option.
     * @var mixed
     */
    const REGEX_OPTION = '/^\(\?\b(?P<add>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\b(:\b(?P<remove>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\b)?\)/';
    /**
     * Property: loading formatters.
     * @var mixed
     */
    static $sm_LoadingFormatters = [];
    /**
     * detect contain start line 
     */
    const REGEX_START_LINE = '/(?<!\\\\|\w|\[)\^/';
    /**
     * Constant: regex continues empty line.
     * @var mixed
     */
    const REGEX_CONTINUES_EMPTY_LINE = '/^\\s*$/';
    /**
     * detect contain end line 
     */
    const REGEX_END_LINE = '/(?<!\\\)\\$/';
    /**
     * Constant: begin end type.
     * @var mixed
     */
    const BEGIN_END_TYPE = RegexMatcherPattern::BEGIN_END_TYPE;
    /**
     * Constant: begin while type.
     * @var mixed
     */
    const BEGIN_WHILE_TYPE = 'begin/while';
    /**
     * Constant: match type.
     * @var mixed
     */
    const MATCH_TYPE = 'match';
    /**
     * Constant: include.
     * @var mixed
     */
    const INCLUDE = 'include';
    /**
     * enable mark end of source
     * @var ?bool
     */
    var $markEndOfSource;
    /**
     * Property: last.
     * @var mixed
     */
    private $m_last;
    /**
     * Property: ignore scoped.
     * @var mixed
     */
    private $m_ignoreScoped;
    /**
     * initialia pattern
     * @var ?array
     */
    private $m_initialPatterns;
    /**
     * Type of type.
     * @var mixed
     */
    var $type;
    /**
     * table used to associate a key to pattern
     * @var mixed
     */
    var $refTables;
    /**
     * auto store created pattern
     * @var bool
     */
    var $autoStore = true;
    /**
     * when end detect on empty child skip to end of line 
     * @var bool
     */
    var $autoSkipEndCapture = true;
    /**
     * flag: to enable capture
     * @var bool
     */
    var $continueCapture = false;
    /**
     * flag to indicate detection will use multiple line regex
     * @var mixed
     */
    var $splittingDefinition;
    /**
     * auto generate doc.
     * @var ?IRegexMatchPatternStateListener
     */
    var $matchPatternStateListener;
    /**
     * auto generate doc.
     * @var ?IRegexMatchPatternOutpuTreatmentListener
     */
    var $ouputTreatmentListener;
    /**
     * engine pattern listener
     * @var ?callable
     */
    var $enginePatternListener;
    /**
     * capture pattern listener
     * @var ?callable
     */
    var $captureHandlerListener;
    /**
     * auto generate doc.
     * @var ?callable
     */
    var $captureTreatmentListener;
    /**
     * regex detect info parent
     * @var ?RegexDetectInfo 
     */
    private $m_parent;
    /**
     * last match
     * @var ?RegexMatcherPattern
     */
    private $m_last_match;
    /**
     * last match end info
     * @var ?RegexDetectInfo
     */
    private $m_last_detect_end_info;
    /**
     * to avoid infinite loop on match
     * @var ?int
     */
    private $m_last_offset;
    /**
     * last detecting
     * @var mixed 
     */
    private $m_last_detect_info;
    /**
     * store prepared parent info 
     * @var mixed
     */
    private $m_parentInfo;
    /**
     * use internally to detect the current compared info 
     * @var mixed
     */
    private $m_tag;
    /**
     * Property: ref only.
     * @var mixed
     */
    private $m_refOnly;
    /**
     * to dispatch for match calling 
     * @var  
     */
    private $m_engine_treatment_info;
    /**
     * show options
     * @var mixed
     */
    private $m_options;
    /**
     * line detection 
     * @var mixed
     */
    private $m_lineBuffer;
    /**
    * auto generate doc.
    * @var mixed
    */
    protected $m_lineLastDetectionInfo;
    /**
    * auto generate doc.
    * @var mixed
    */
    protected $m_lineMarkSingleEndOffset;
    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_skippedList;
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    private $m_skippedListInfo;
    /**
     * get/set injected pattern creator class
     * @var ?string
     */
    var $patternCreatorClass;
    /**
     * auto generate doc.
     * @return ?IRegexMatcherEngineInfo
     */
    public function getEngineInfo()
    {
        return $this->m_engine_treatment_info;
    }
    /**
    * auto generate doc.
    * @return mixed
    */
    protected function getLastDetectInfo()
    {
        return $this->m_last_detect_info;
    }
    /**
    * auto generate doc.
    * @param mixed $detect
    * @param mixed & $offset
    * @param null|mixed $parent
    * @return void
    */
    protected function autoSkipDefinition($detect,  &$offset, $parent = null)
    {
        $s = &$this->m_skippedList;
        $parent = $parent ?? $detect->parent;
        if ($detect->match->captureMode == RegexMatcherPattern::AUTO_RESET_CAPTURE_MODE) {
            if (is_null($s)) {
                $s = [];
            }
            $m = $detect->match;
            if (!in_array($m, $s)) {
                $index = count($s);
                $s[] = $m;
                $this->m_skippedListInfo[$index] = (object)[
                    'parent' => $parent,
                    'match' => $m
                ];
            } else {
                igk_die('already contain in list');
            }
        }
    }
    /**
    * auto generate doc.
    * @param mixed $match
    * @return void
    */
    protected function unsetSkipDefinition($match)
    {
        if (($idx = array_search($match, $this->m_skippedList)) !== false) {
            unset($this->m_skippedListInfo[$idx]);
            unset($this->m_skippedList[$idx]);
            $this->m_skippedListInfo = array_values($this->m_skippedListInfo);
            $this->m_skippedList = array_map(function ($a) {
                return $a->match;
            }, $this->m_skippedListInfo);
        }
    }
    /**
    * auto generate doc.
    * @param mixed $match
    * @param null|mixed $parent
    * @return bool
    */
    protected function checkSkipDefinition($match, $parent = null): bool
    {
        if ($this->m_skippedList && ($idx = array_search($match, $this->m_skippedList)) !== false) {
            $r = $this->m_skippedListInfo[$idx];
            if ($r->parent === $parent) {
                $this->unsetSkipDefinition($match);
                return true;
            }
        }
        return false;
    }
    /**
     * auto generate doc.
     * @param ?IRegexMatcherEngineInfo  $info
     * @return void
     */
    public function setEngineInfo(?IRegexMatcherEngineInfo $info)
    {
        $this->m_engine_treatment_info = $info;
    }
    /**
     * load from json data
     * @param string $file 
     * @param ?string $pattern_class_name
     * @return static
     */
    public static function LoadFromFile(string $file, ?string $pattern_class_name = null)
    {
        $c = new static;
        $c->patternCreatorClass = $pattern_class_name;
        if ($data = json_decode(file_get_contents($file))) {
            extract(igk_extract_var($data, 'repository|patterns'));
            $c->loadRepository($repository ?? []);
            foreach ($patterns as $p) {
                $rp = null;
                if ($include = igk_getv($p, 'include')) {
                    if ($include[0] == '#') {
                        $include = substr($include, 1);
                        $rp = $c->getMatcherByRefId($include);
                    }
                } else {
                    $rp = $c->createPattern((array)$p);
                }
                if ($rp) {
                    $rp = $c->_fix_loading($rp);
                    $c->append($rp);
                }
            }
        }
        return $c;
    }
    /**
     * use to fix loading properties patterns
     * @param mixed $rp 
     * @return mixed 
     * @throws Exception 
     */
    protected function _fix_loading($rp)
    {
        $tdb = [$rp];
        $root = null;
        $treats = [];
        while (count($tdb) > 0) {
            $rp = array_shift($tdb);
            if (is_null($root)) {
                $root = $rp;
            }
            if ($ctp = igk_getv($rp, 'patterns')) {
                $gp = [];
                foreach ($ctp as $p) {
                    if ($include = igk_getv($p, 'include')) {
                        if ($include[0] == '#') {
                            $include = substr($include, 1);
                            $tp = $this->getMatcherByRefId($include) ?? igk_die('missing repository');
                            $gp[] = $tp;
                            if (!isset($treats[$include])) {
                                $treats[$include] = $tp;
                                array_unshift($tdb, $tp);
                            }
                        } else {
                            $gp[] = (array)$p;
                        }
                    } else {
                        $cp = $this->createPattern((array)$p);
                        $gp[] = $cp;
                    }
                }
                $rp->patterns = $gp;
            }
            // + | replaceWidth
            if ($v_rpw = igk_getv($rp, $key = 'replaceWith')) {
                if (is_object($v_rpw)) {
                    $rp->{$key} = (array)$v_rpw;
                }
            }
        }
        return $root;
    }
    /**
     * get last inserted match information 
     * @return ?RegexMatcherPattern
     * @throws Exception 
     */
    public function last()
    {
        if (!is_null($this->m_last)) {
            return $this->m_last;
        }
        $c = count($this->m_matcher);
        return $c > 0 ? igk_getv($this->m_matcher, $c - 1) : null;
    }
    /**
     * array list of matcher
     * @return array 
     */
    public function getMatcher()
    {
        return $this->m_matcher;
    }
    /**
     * Sets Matcher.
     * @param ?array $patterns
     */
    public function setMatcher(?array $patterns)
    {
        $this->m_matcher = $patterns;
    }
    /**
     * auto generate doc.
     * @var array
     */
    private $m_matcher = [];
    /**
     * store matching references
     * @var array
     */
    private $m_references = [];
    /**
     * last position 
     * @var ?int
     */
    private $m_pos;
    /**
     * auto generate doc.
     * @param string $id
     * @return mixed
     */
    public function getMatcherByRefId(string $id)
    {
        return igk_getv($this->m_references, $id);
    }
    /**
     * clear all definitions
     * @return void 
     */
    public function clear()
    {
        $this->m_matcher = [];
        $this->m_references = [];
    }
    /**
     * reset detection
     * @return void 
     */
    public function resetTreatment()
    {
        $this->m_pos = 0;
        $this->m_last_detect_end_info = null;
        $this->m_parent = null;
        $this->m_last_offset = null;
        $this->m_last_detect_info = null;
    }
    /**
     * .ctr
     */
    public function __construct()
    {
        $this->markEndOfSource = true;
    }
    /**
    * auto generate doc.
    * @param null|int $lastoffset
    * @return void
    */
    protected function lastOffset(?int $lastoffset)
    {
        $this->m_last_offset = $lastoffset;
    }
    /**
    * auto generate doc.
    * @param mixed $e
    * @param mixed & $offset
    * @return void
    */
    protected function _updateSkipEndCaptureMode($e, &$offset)
    {
        if ($e->match->captureMode == RegexMatcherPattern::AUTO_RESET_CAPTURE_MODE) {
            $offset = $e->from + ($e->to - $e->from);
        }
    }
    /**
     * do end operation 
     * @param IRegexMatcherDetectInfo $info object info class 
     * @param string $source 
     * @param int &$offset must pass and offset to select the proper info 
     * @return object|RegexMatcherCapture|void 
     * @throws Exception 
     */
    public function end(IRegexMatcherDetectInfo $info, string $source, int &$offset)
    {
        if ($this->_skipLineBufferEnd($source, $offset)){
            return null;
        }
        $e = $this->_treatEnd($info, $source, $offset);
        if ($e) {
            $this->_updateSkipEndCaptureMode($e, $offset);
            // + | --------------------------------------------------------------------
            // + | upate last info and parent definition 
            // + | 
            $this->m_ignoreScoped = null;
            $this->m_last_detect_end_info = $info;
            $this->_setParent($e->parentInfo);
            if ($e->match instanceof stdClass) {
                igk_wln_e(__FILE__ . ":" . __LINE__, 'instance of stdClass not allowed');
            }
            if ($e->match->scopedBoundary) {
                $p = ($this->m_parent) ? $this->m_parent->match : null;
                if ($p && $p->scopedBoundary) {
                    $this->m_ignoreScoped = $p;
                }
            }
            if ($e->info) {
                $e->info->start = true;
            }
            if ($e->parentInfo)
                $e->parentInfo->start = true;
        }
        $e = $this->_treatEndSkipLineBuffer($e, $source, $offset);
        return $e;
    }
    /**
    * auto generate doc.
    * @param mixed $g
    * @param string $source
    * @param int $offset
    * @return null|mixed
    */
    protected function _treatEndSkipLineBuffer($g, string $source, int $offset){
        $v_ln = strlen($source);
         if ($g && !$this->markEndOfSource && ($offset >= $v_ln)) { 
            $this->m_lineLastDetectionInfo = $g;
            if ($g->getisEnd()) {
                $this->m_lineMarkSingleEndOffset = $g->from;
            }
            $this->_updateBuffer($source);
            return null;
        }
        return $g;
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    private function _updateBuffer(string $value)
    {
        $this->m_lineBuffer = $value;
        $this->lastOffset(null);
    }
    /**
     * save container state
     * @return mixed 
     */
    public function saveState()
    {
        ($f = $this->matchPatternStateListener) ? $f->saveState() : null;
        return [
            'pos' => $this->m_pos,
            'info' => $this->m_last_detect_end_info,
            'parent' => $this->m_parent,
            'lastOffset' => $this->m_last_offset,
            'patterns' => $this->m_matcher,
            'lastDetect' => $this->m_last_detect_info,
        ];
    }
    /**
    * restore container state
    * @param ?array $states
    * @return mixed
    */
    public function restoreState(?array $states = null)
    {
        if ($states) {
            extract(igk_extract_var($states, 'pos|info|parent|lastOffset|patterns|lastDetect'));
            $this->m_pos = $pos;
            $this->m_last_detect_end_info = $info;
            $this->m_parent = $parent;
            $this->m_last_offset = $lastOffset;
            $this->m_matcher = $patterns;
            $this->m_last_detect_info = $lastDetect;
        }
        return ($f = $this->matchPatternStateListener) ? $f->restoreState() : null;
    }
    /**
     * get read output
     * @return null|string|void 
     */
    public function getOuput()
    {
        if ($f = $this->ouputTreatmentListener)
            return $f->getOutput();
    }
    /**
    * auto generate doc.
    * @param mixed $info
    * @param string $source
    * @param int & $offset
    * @return ?RegexMatcherCapture
    */
    protected function _treatEnd($info, string $source, int &$offset)
    {
        $tabinfo = [$info];
        $skip = $this->m_parent === $info;
        $v_size = strlen($info->value);
        $v_nextline_offset = strpos($source, "\n", $offset);
        $v_sln = strlen($source);
        $v_end_of_source = $offset >= $v_sln;
        if (($v_size == 0) && (!$info->start)) { 
            if ($info->match === $this->m_last_match) {
                if ($this->m_parent == null) {
                    $i = $v_nextline_offset;
                    if ($i === false) {
                        $offset = strlen($source) + 1;
                    } else
                        $offset++;
                }
                if (!$info->match->patterns)
                    throw new IGKException('--end found: skip: infinite loop---');
            }
            $this->m_last_match = $info->match;
        } else {
            $this->m_last_match = null;
        }
        // + | --------------------------------------------------------------------
        // + | treat end type
        // + |
        $endType = igk_getv($info, 'endType');
        if ($endType) {
            switch ($endType) {
                case 'end':
                    // + | just end with non end capture flag the child stop it normally
                    $n = $offset;
                    $this->_setParent($info->parent);
                    $e =  $this->_endinfo($info, $source, $n);
                    // + | --------------------------------------------------------------------
                    // + | add skip end element detected using internal skip definition 
                    // + |
                    ($this->autoSkipEndCapture && !$e->match->noSkipToEnd) && self::_SkipToEndOfLine($offset, $v_sln, $v_nextline_offset);
                    return $e;
            }
        }
        if ($v_end_of_source) {
            return $this->_endinfo($info, $source, $offset, []);
        }
        $v_continue = false;
        $v_continueRead = false;
        $v_boffset = $offset;
        $v_size = 0;
        while (count($tabinfo) > 0) {
            $info = array_shift($tabinfo);
            $k = $info->match;
            if ($v_continueRead) { // + | fix update offset movement
                if ($v_boffset == $offset) {
                    $offset += 1;
                }
                $v_continueRead = false;
                $v_boffset = $offset;
                $v_size = 0;
            } else {
                $v_size = !$info->start && $offset == $info->pos ? strlen($info->value) : 0;
            }
            // + | update parent info - 
            $this->_setParent($info->parent);
            switch ($k->type) {
                case RegexMatcherPattern::BEGIN_END_TYPE:
                case RegexMatcherPattern::BEGIN_WHILE_TYPE:
                    if (!property_exists($info->match, 'patterns')) {
                        $info->match->patterns = null;
                    }
                    if (!isset($info->endTreat)) {
                        $b = igk_getv($k, 'end');
                        $o = '';
                        if ($b) {
                            // + | is begin 
                            $b = $b ? sprintf("/%s/%s", $b, $o) : null;
                        } else {
                            if (!is_null($b)) {
                                $b = '/.+$/';
                            }
                        }
                        if (!is_null($b)) {
                            // + | 
                            // + | determine compared position 
                            // + | end back reference
                            $b = preg_replace_callback("/\\\\(?P<id>\d+)/", function ($m) use ($info) {
                                $id = intval($m['id']);
                                $f = $info->captures[$id][0];
                                // + | escape repeatable items
                                $f = str_replace("*", "\\*", $f);
                                $f = str_replace("+", "\\+", $f);
                                return $f;
                            }, $b);
                            $info->endTreat = $b;
                        } else {
                            $b = $info->endTreat = false;
                        }
                    } else
                        $b = $info->endTreat;
                    $v_skipped =  $skip;
                    if (!$skip)
                        $offset += $v_size; 
                    else
                        $skip = false;
                    $cpos = $offset;
                    $v_cpatterns = $info->match->patterns;
                    $compared_end = ($cpos >= $offset) && $v_cpatterns  ? $this->_comparedPattern($info, $v_cpatterns, $source, $cpos) : null;
                    $start_line = false;
                    if ($this->m_ignoreScoped === $info->match) {
                        $b = false;
                    }
                    if (($b !== false) && ($tab = $this->_matchOffset($b, $source, $offset, $start_line))) {
                        $v_current_offset = $tab[0][1];
                        $v_ms = strlen($tab[0][0]);
                        $n = $v_current_offset + $v_ms;
                        // + | if empty and offset not change then update to next 
                        if (empty($tab[0][0]) && !$v_skipped && ($v_current_offset == $offset)) {
                            array_unshift($tabinfo, $info);
                            $skip = true;
                            continue 2;
                        }
                        if ($compared_end && ($compared_end->pos < $v_current_offset)) {
                            // + | handle compared match item 
                            $v_continue = false;
                            $r = $this->_handleComparedMatchItem($info, $compared_end, $offset, $v_continue);
                            if ($r) {
                                // + | update match pattern value 
                                $r->value = $r->sourceValue = substr($source, $r->from, $r->to - $r->from);
                                return $r;
                            }
                            if ($v_continue /*&& ($v_size==0)*/ && empty($compared_end->value)) {
                                $v_continueRead = 
                                    $compared_end->pos != $offset;
                                array_unshift($tabinfo, $compared_end);
                                continue 2;
                            }
                            $offset = $compared_end->pos;
                            array_unshift($tabinfo, $compared_end);
                            continue 2;
                        }
                        $offset = $n;
                        $v_tvalue = substr($source, $info->pos, $n - $info->pos);
                        $v_tcaptures = $this->_treatEndCaptures($info, $v_tvalue, $tab);
                        $info->endType = 'end';
                        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                            $this,
                            'tag' => '_treatEnd',
                            'match' => $info->match,
                            'tokenID' => igk_getv($k, 'tokenID'),
                            'from' => $info->pos,
                            'to' => $n,
                            'value' => $v_tcaptures,
                            'sourceValue' => $v_tvalue,
                            'beginCaptures' => $info->captures,
                            'endCaptures' => $tab,
                            'parentInfo' => $info->parent,
                            'emptyLine' => $info->emptyLine,
                            'info' => $info,
                        ]);
                    } else {
                        // + | no match end found but
                        if ($compared_end) {
                            // + | a compared end found - empty just close the parent with  this
                            $v_continue = false;
                            $r = $this->_handleComparedMatchItem($info, $compared_end, $offset, $v_continue);
                            if ($r) {
                                return $r;
                            }
                            if ($v_continue) {
                                $offset = $compared_end->pos;
                                array_unshift($tabinfo, $compared_end);
                                continue 2;
                            }
                        }
                        $l = $tln = strlen($source);
                        // + | move to next line 
                        if (($tln >= $offset) && (false !== ($l = strpos($source, "\n", $offset)))) {
                            // + | move forward to detect the real next end that match condition
                            $offset = $l + 1;
                            $nv = substr($source, $info->pos, $offset - $info->pos);
                            array_unshift($tabinfo, $info);
                            $info->value = $nv;
                            $skip = true;
                            continue 2;
                        } 
                        $offset = $tln;
                        $v_srcv = substr($source, $info->pos);
                        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                            'tag' => '__local__',
                            'match' => $info->match,
                            'tokenID' => $k['tokenID'],
                            'from' => $info->pos,
                            'to' => $offset,
                            'value' => $v_srcv,
                            'sourceValue' => $v_srcv,
                            'beginCaptures' => $info->captures,
                            'endCaptures' => null,
                            'captures' => $info->captures,
                            'parentInfo' => $info->parent,
                            'info' => $info,
                        ]);
                    }
                    break;
                case RegexMatcherPattern::MATCH_TYPE:
                    if ($g = igk_is_debug('css_parse')){
                        igk_wln_e('kdj');
                    }
                    $n = $info->pos + $v_size;
                    $offset = $n + ($info->moveToNextLine ? 1 : 0);
                    $bsrc = $treated = $src = substr($source, $info->pos, $n - $info->pos);
                    $option = null;
                    $captures = (array)$k->captures ?? $k->beginCaptures;
                    if ($captures) {
                        $option = [
                            'captureHandlerListener' => $this->captureHandlerListener
                        ];
                        $src = self::TreatCaptures($captures, $info->captures, $src, $option);
                    }
                    if ($k->patterns) {
                        // + | logic to treat match patterns  - normally not treated treat to patterns
                        // + | require engine treatment listener 
                        if ($inf = $this->m_engine_treatment_info) {
                            // + | condition missing or equal 
                            if ('treat' == $inf->type) {
                                list($callable, $end_token_id) = igk_extract($inf, 'callable|end_token_id');
                                $g = new static;
                                $g->m_matcher = $k->patterns;
                                $g->setParentInfo(null);
                                $g->matchPatternStateListener = $this->matchPatternStateListener;
                                $g->ouputTreatmentListener = $this->ouputTreatmentListener ?? Activator::CreateNewInstance(RegexMatcherOutputListener::class, [
                                    'output' => ''
                                ]);
                                $g->saveState();
                                $g->treat($src, $callable, $end_token_id);
                                $treated = $g->getOuput() . substr($src, $g->getLastPosition());
                                $g->restoreState();
                            } else {
                                $treated = $inf($this, $k->patterns, $src);
                            }
                        } else {
                            igk_die("Engine treatment required to treat match's patterns");
                        }
                    } else {
                        $treated = $src;
                    }
                    if (($offset == 0) && $v_size == 0) {
                        // + | update to next offset 
                        self::_SkipToEndOfLine($offset, $v_sln, $v_nextline_offset);
                    }
                    return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                        'tag' => '2',
                        'tokenID' => $k['tokenID'],
                        'match' => $info->match,
                        'from' => $info->pos,
                        'to' => $n,
                        'value' => $treated, 
                        'sourceValue' => $bsrc, 
                        'option' => $option,
                        // + | passing info
                        'parentInfo' => $info->parent,
                        'beginCaptures' => $info->captures,
                        'captures' => $info->captures,
                        'endCaptures' => $info->captures,
                        'emptyLine' => $info->emptyLine,
                    ]);
            }
        }
    }
    /**
     * set parent
     * @param ?RegexDetectInfo $parent 
     * @return void 
     */
    protected function _setParent(?RegexDetectInfo $parent)
    {
        $this->m_parent = $parent;
    }
    /**
     * skip to end of line definition
     * @param int &$offset 
     * @param int $strlen 
     * @param mixed $nextLineOffset 
     * @return void 
     */
    private static function _SkipToEndOfLine(int &$offset, int $strlen, $nextLineOffset)
    {
        if (false === $nextLineOffset) {
            $offset = $strlen;
        } else {
            $offset = $nextLineOffset + 1;
        }
    }
    /**
    * call with the treat method to handle capture treatment or custom replacement techniques
    * @param mixed $info
    * @param string $value
    * @param ?array $endCap
    * @return string
    */
    protected function _treatEndCaptures($info, string $value, ?array $endCap = null): string
    {
        $v_t = [];
        list($beginCaptures, $endCaptures, $captures, $type) = igk_extract($info->match, 'beginCaptures|endCaptures|captures|type');
        $_scap = $beginCaptures ?? $captures;
        $_ecap = $endCaptures ?? $captures;
        $_treatment_info = $this->m_engine_treatment_info;
        $_listener = null;
        if ($_treatment_info) {
            $_listener = (function ($info, $callable, $value) {
                return function (RegexCaptureInfo $cap, $option) use ($callable, $info, $value) {
                    // + | ----------------
                    // + | CREATE INSTANCE OF REGEX CAPTURE - 
                    // + | ----------------
                    // + | 
                    $inf = Activator::CreateNewInstance(RegexMatcherCapture::class, [
                        'tag' => '_treatEndCaptures',
                        'from' => $cap->getPos(),
                        'to' => $cap->getTo(),
                        'value' => $cap->getValue()
                    ]);
                    $inf->parentInfo = $info;
                    $inf->tokenID = $option->name ?? $info->match->tokenID;
                    return $callable($inf, $inf->to + 1, $value);
                };
            })($info, $_treatment_info->callable, $value);
        }
        switch ($type) {
            case RegexMatcherPattern::MATCH_TYPE:
                if ($_scap) {
                    $cap = $info->captures;
                    $opts = null;
                    $l = self::TreatCaptures($_scap, $cap, $cap[0], $opts);
                    $v_t['treated'] = $l;
                }
                break;
            case RegexMatcherPattern::BEGIN_END_TYPE:
            case RegexMatcherPattern::BEGIN_WHILE_TYPE:
                if ($_scap || $_ecap) {
                    if ($_scap) {
                        $l = null;
                        $cap = $info->captures;
                        if ($t_info = RegexTreatCapture::CreateFromRegexResult($cap, $_scap)) {
                            $l =  $t_info->treat($_listener);
                        } else {
                            $opts = null;
                            $l = self::TreatCaptures($_scap, $cap, $cap[0][0], $opts);
                        }
                        $v_t['startTreated'] = $l;
                    }
                    $cap = $endCap;
                    if ($cap && $_ecap) {
                        if ($t_info = RegexTreatCapture::CreateFromRegexResult($cap, $_ecap)) {
                            $l =  $t_info->treat($_listener);
                        } else {
                            $opts = null;
                            $l = self::TreatCaptures($_ecap, $cap, $cap[0][0], $opts);
                        }
                        $v_t['endTreated'] = $l;
                    }
                }
                break;
        }
        if ($v_t) {
            if (key_exists('treated', $v_t)) {
                $c = $v_t['treated'];
                return $c;
            } else {
                $endPos = null;
                $startLength = 0;
                $begin = $end = '';
                $offset = 0;
                if (!is_null($c = igk_getv($v_t, 'startTreated'))) {
                    $cap = $info->captures;
                    $offset = $cap[0][1];
                    $begin = $c;
                    $startLength = strlen($cap[0][0]);
                }
                if (!is_null($c = igk_getv($v_t, 'endTreated'))) {
                    $cap = $endCap;
                    $end = $c;
                    $endPos =  $cap[0][1] - $offset;
                }
                $v_s = RegexMatcherUtility::TreatBeginEndCapture($value, $begin, $end, $startLength, $endPos);
                return $v_s;
            }
        }
        return $value;
    }
    /**
    * detect info comparaison
    * @param RegexDetectInfo $info parent info
    * @param RegexDetectInfo $compared_end
    * @param int & $offset
    * @param mixed & $v_continue
    */
    protected function _handleComparedMatchItem(RegexDetectInfo $info, RegexDetectInfo $compared_end, int &$offset, &$v_continue = false)
    {
        $l = $compared_end;
        $k = $l->match;
        list($v_id, $v_type) = igk_extract($k, 'tokenID|type');
        $v_continue = false;
        if ($v_type != RegexMatcherPattern::MATCH_TYPE) {
            $v_continue = true;
            return;
        }
        $this->_setParent($info);
        $_size = strlen($compared_end->value);
        if (!$compared_end->emptyLine && ($_size == 0)) {
            $offset = $l->pos;
            $info->endType = 'end';
            $this->m_last_detect_info = null;
            return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                $this,
                'tag' => '3',
                'tokenID' =>  $v_id,
                'from' => $l->pos,
                'to' => $l->pos,
                'value' => '',
                'sourceValue' => '',
                'beginCaptures' => $l->captures,
                'endCaptures' => $l->captures,
                'parentInfo' => $info,
                'match' => $k
            ]);
        } else {
            $n = $l->pos + $_size;
            $offset = $n;
            return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                $this,
                'tag' => '_continue_to_with_child_',
                'tokenID' => $v_id,
                'from' => $l->pos,
                'to' => $n,
                'info' => $l,
                'value' =>  $compared_end->value,
                'sourceValue' => $compared_end->value,
                'beginCaptures' => $compared_end->captures, // + | fix info captures
                'captures' => $compared_end->captures,
                'endCaptures' => null,
                'parentInfo' => $info,
                'match' => $k,
                'emptyLine' => $compared_end->emptyLine
            ]);
        }
        $v_continue = true;
    }
    /**
    * treat end info
    * @param mixed $info
    * @param string $source
    * @param int $n
    * @param int $option position
    * @throws IGKException
    * @return mixed
    */
    private function _endinfo($info, string $source,  int $n, ?array $endcapture = null)
    {
        $k = $info->match;
        $src = substr($source, $info->pos, $n - $info->pos);
        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
            $this,
            'tag' => '_endinfo_',
            'match' => $info->match,
            'tokenID' => $k['tokenID'],
            'from' => $info->pos,
            'to' => $n,
            'value' => $src,
            'sourceValue' => $src,
            'beginCaptures' => $info->captures,
            'endCaptures' => $endcapture,
            'parentInfo' => $info->parent,
            'info' => $info 
        ]);
    }
    /**
     * update table regex offset 
     * @param array &$tab 
     * @param int $offset 
     * @return void 
     */
    private static function _UpdateTabOffset(array &$tab, int $offset)
    {
        foreach (array_keys($tab) as $key) {
            $tab[$key][1] += $offset;
        }
    }
    /**
     * check for regex with move to next line detection
     * @param string $regex regex againts
     * @param string $source source to check
     * @param int $offset offset to match
     * @param bool & $move_to_next_line is start line request
     * @return array|false 
     */
    private function _matchOffset(string $regex, string $source, int $offset, &$move_to_next_line = false)
    {
        $j = null;
        $start_line = preg_match(self::REGEX_START_LINE, $regex) > 0;
        $end_line = preg_match(self::REGEX_END_LINE, $regex) > 0;
        $result = [];
        $tab = [];
        $error = preg_last_error_msg();
        if ($offset <= strlen($source)) {
            if (false !== (preg_match($regex, $source, $tab, PREG_OFFSET_CAPTURE, $offset))) {
                if (!empty($tab)) {
                    $result[] = $tab;
                }
            } else {
                $error = preg_last_error_msg();
                igk_die(implode("\n", ['regex - produce ', $error, $regex]));
                return false;
            }
        }
        $tab = []; // + | reset tab
        $TLen = strlen($source);
        $next_line = $offset < $TLen ? strpos($source, "\n", $offset) : false;
        if ($start_line && $end_line) {
            if ($offset == 0) {
                $sline = substr($source, 0, $next_line);
                if (preg_match($regex, $sline, $tab, PREG_OFFSET_CAPTURE, 0)) {
                    self::_UpdateTabOffset($tab, $offset);
                    $result[] = $tab;
                }
            }
        }
        if ($start_line && ($offset > 0) && ($offset < $TLen)) {
            // + | cut next offset             
            $fsource = [];
            // + | detect previous offset line splitter 
            if (($source[$offset - 1] == "\n") && ($next_line != $offset)) {
                $fsource[] = $offset;
            }
            if ($next_line !== false)
                $fsource[] = $next_line + 1;
            while (count($fsource) > 0) {
                $coffset = array_shift($fsource);
                if (($coffset > 0) && /* ($next_line!=$offset) */ preg_match($regex, substr($source, $coffset), $tab, PREG_OFFSET_CAPTURE, 0)) {
                    self::_UpdateTabOffset($tab, $coffset);
                    $result[] = $tab;
                }
            }
        }
        if ($end_line && ($offset < $TLen)) {
            $bnext_line = false;
            $coffset = ($next_line === false) ? $TLen : $next_line;
            if ($next_line !== false) {
                $bnext_line = true;
            }
            $v_size = abs($coffset - $offset);
            $j = substr($source, $offset, $v_size);
            $lc = false;
            if (!trim($j) && ($regex == self::REGEX_CONTINUES_EMPTY_LINE)) {
                $ln = $coffset;
                while (($pos = strpos($source, "\n", $ln)) && (preg_match($regex, substr($source, $offset, $pos - $offset)))) {
                    $ln = $pos + 1;
                }
                $j = substr($source, $offset, $ln - $offset);
                $lc = true;
            }
            if ((!$lc || strlen($j)) && preg_match($regex, $j, $tab, PREG_OFFSET_CAPTURE, 0)) {
                self::_UpdateTabOffset($tab, $offset);
                $result[] = $tab;
                $move_to_next_line =  $bnext_line;
            } else if ($result && ($v_size == 0) && ($offset < $result[0][0][1])) {
                $result = [];
                $move_to_next_line = true;
            }
        }
        if ($result) {
            usort($result, function ($a, $b) {
                if (!key_exists(0, $a)) {
                    igk_wln_e('failed');
                }
                return $a[0][1] <=> $b[0][1];
            });
            return $result[0];
        }
        if ($start_line && !$move_to_next_line) {
            $move_to_next_line = true;
        }
        return false;
    }
    /**
    * detecd for compared pattern
    * @param mixed $info
    * @param array $patterns
    * @param string $source
    * @param int &$offset
    * @throws Exception
    * @return mixed|void
    */
    private function _comparedPattern($info, array $patterns, string $source, int &$offset)
    {
        if (!$patterns) {
            return null;
        };
        $g = new static;
        $this->_initSubMatcherContainer($g);
        $g->setMatcher($patterns);
        $g->setParentInfo($info);
        $g->m_tag = __METHOD__;
        $tpos = $offset;
        return $g->detect($source, $tpos);
    }
    /**
    * set regex detect information
    * @param mixed $detectInfo
    * @return mixed
    */
    public function setParentInfo(?RegexDetectInfo $detectInfo)
    {
        $this->m_parentInfo = $detectInfo;
    }
    /**
     * auto generate doc.
     * @param static $g
     * @return void
     */
    private function _initSubMatcherContainer($g)
    {
        /**
         * passing definition to init sub pattern 
         */
        $g->patternCreatorClass = $this->patternCreatorClass;
    }
    /**
     * reduce an return the mininum of this 
     * @param mixed &$result 
     * @return mixed 
     */
    private static function _ReduceResult(&$result)
    {
        if (count($result) == 1) {
            return $result[0];
        }
        $tab = [];
        $min = null;
        foreach ($result as $k) {
            if (isset($tab[$k->pos]))
                continue;
            $tab[$k->pos] = $k;
            $min = is_null($min) ? $k->pos : min($k->pos, $min);
        }
        $result = array_values($tab);
        return $tab[$min];
    }
    /**
     * start match 
     * @param ?RegexDetectInfo $info 
     * @param mixed &$result 
     * @param mixed $b 
     * @param mixed $source 
     * @param mixed &$offset 
     * @param mixed $k 
     * @param mixed &$next_line 
     * @return void 
     * @throws Exception 
     */
    private function _startMatch(?RegexDetectInfo $info, &$result, $b, $source, &$offset, $k, &$next_line)
    {
        $is_empty_line = $b == RegexMatcherUtility::REGEX_EMPTY_LINE;
        if ($b) {
            $b = RegexMatcherUtility::ConverToRegex($b);
        }
        if ($b) {
            $v_move_to_next_line = false;
            $tab = $this->_matchOffset($b, $source, $offset, $v_move_to_next_line);
            if ($tab) {
                // + | create detect result info    
                $result[] = Activator::CreateNewInstance(RegexDetectInfo::class, [
                    'pos' => $tab[0][1],
                    'value' => $tab[0][0],
                    'match' => $k,
                    'captures' => $tab,
                    'parent' => $info,
                    'moveToNextLine' => $v_move_to_next_line, 
                    'emptyLine' => $is_empty_line
                ]);
            }
            $next_line = $next_line || $v_move_to_next_line;
        }
    }
    /**
     * retrieve pattern type 
     * @param mixed $k 
     * @return mixed 
     * @throws Exception 
     */
    public static function GetPatternType($k): string
    {
        assert(!is_null($k), 'pattern is null');
        $v_type = igk_getv($k, 'type');
        if (is_null($v_type)) {
            $v_type = $k->type = RegexMatcherUtility::GetPatternType($k);
        }
        return $v_type;
    }
    /**
    * check for multi - line buffering detection
    * @param string $src
    * @param int & $offset
    * @param mixed $offset
    * @return bool
    */
    protected function _lineBufferDetected(string $src,int & $offset, & $out=null):bool{
        $v_backupOffset = $offset;
        $v_ln = strlen($src);
        $out = null;
        if (($v_backupOffset >= $v_ln) && !$this->markEndOfSource) {
            if ($this->markEndOfSource && ($tdc = $this->getParent())) {
                $out = $tdc;
            } 
            return true;  
        }
        if (!is_null($this->m_lineMarkSingleEndOffset)) {
            $offset = $this->m_lineMarkSingleEndOffset;
            $this->m_lineMarkSingleEndOffset = $this->m_lineLastDetectionInfo = null;
        } else {
            if ($p = $this->m_lineLastDetectionInfo) {
                $offset = $p->to;
                $this->m_lineLastDetectionInfo = null;
                $out= $p->info;
                return true;
            }
        }
        return false;
    }
    /**
    * detecting regex type
    * @param string $source The input string
    * @param int & $offset
    * @return ?IRegexMatcherDetectInfo
    */
    public function detect(string $source, int &$offset)
    {
        $v_detect = null;
        if ($this->_lineBufferDetected($source, $offset, $v_detect)){
            return $v_detect;
        }
        $v_flag_current = false;
        if ($this->splittingDefinition) {
            $offset = 0;
            if (!is_null($this->m_last_offset)) {
                $this->m_last_offset = null;
            }
        }
        $v_skip_detect = null;
        if ($p = $this->m_parent) {
            if (($this->m_last_offset == $offset) && ($this->m_last_detect_info === $p)) {
                igk_die(__CLASS__ . ' > parent not updated. matcher misconfiguration #' . $p->id());
            }
            $this->m_last_offset = $offset;
            $this->m_last_detect_info = $p;
            $this->_setParent($p->parent);
            return $p;
        }
        if ($this->m_last_detect_info) {
            $this->m_last_offset = 0;
            $this->m_last_detect_info = null;
        }
        if (!is_null($this->m_last_offset) && ($this->m_last_offset == $offset)) {
            $error = true;
            if (($lo = $this->m_last_detect_end_info) && ($lo->pos == $offset)) {
                // + | skip
                $error = false;
                if ($lo->endType == 'end') {
                    $this->m_last_detect_end_info = null;
                    $offset = $lo->pos;
                } else {
                    $l = strlen($this->m_last_detect_end_info->value);
                    if ($l == 0) {
                        $v_skip_detect = $this->m_last_detect_end_info->match;
                    }
                    $offset = $this->m_last_detect_end_info->pos + $l;
                    $v_flag_current = true;
                }
            }
            if ($error)
                throw new Exception("[BLF] - offset not update " . $offset);
        }
        $this->m_last_offset = $offset;
        $this->m_last_detect_info = null;
        $result = [];
        $next_line = false;
        $info = $this->m_parentInfo;
        $detect = true;
        $ln = strlen($source);
        $match_patterns = $this->m_initialPatterns ?? $this->m_matcher;
        $v_detect = null;
        while ($detect) {
            $detect = false;
            $tm = $match_patterns; 
            while (count($tm) > 0) {
                $v_ck = key($tm);
                $k = array_shift($tm);
                if (is_array($k)) {
                    // + |  create an pattern object
                    $v_ctab = igk_array_is_assoc_only($k) ? $k : ['patterns' => $k];
                    $k = $this->createPattern($v_ctab);
                    $this->m_matcher[$v_ck] = $k;
                }
                if (igk_getv($k, 'captureMode') == RegexMatcherPattern::AUTO_RESET_CAPTURE_MODE) {
                    if ($this->checkSkipDefinition($k)) {
                        continue;
                    }
                }
                if ($v_skip_detect && ($v_skip_detect === $k)) {
                    continue;
                }
                $v_type = self::GetPatternType($k);
                switch ($v_type) {
                    case self::BEGIN_END_TYPE:
                        $b = igk_getv($k, 'begin');
                        if ($b) {
                            $this->_startMatch($info, $result, $b, $source, $offset, $k, $next_line);
                        }
                        break;
                    case self::INCLUDE:
                        if ($k instanceof IRegexMatcherPatternContainer) {
                            $tinfo = null;
                            $k->startMatch($info, $tinfo, $source, $offset);
                            if ($tinfo) {
                                $result[] = $tinfo;
                            }
                        } else
                            throw new Exception('include - not support');
                        break;
                    case self::BEGIN_WHILE_TYPE:
                        throw new Exception('begin/while not implement');
                        break;
                    case self::MATCH_TYPE:
                        $b = igk_getv($k, 'match');
                        if ($b) {
                            $this->_startMatch($info, $result, $b, $source, $offset, $k, $next_line);
                        } else {
                            if ($patterns = igk_getv($k, 'patterns')) {
                                // + | for pattern only 
                                array_unshift($tm, ...$patterns);
                            }
                        }
                        break;
                }
            }
            $toffset = $offset < $ln ? strpos($source, "\n",  $offset) : false;
            if (($toffset !== false) && ($offset == $toffset)) {
                $toffset++;
            }
            if (count($result) > 0) {
                $bp = $result;
                $r = self::_ReduceResult($bp);
                // + | because of detect offset must be set de $r->pos in end.
                $offset = $r->pos;
                if ($v_flag_current && $next_line) {
                    if ((false !== $toffset) && (($r->pos - 1) > $toffset)) {
                        $detect = true;
                        $offset = $toffset + 1;
                        $next_line = false;
                        $result = [];
                        continue;
                    }
                } else {
                    if ($next_line && ($toffset !== false) && ($toffset != $offset)) {
                        if ($r->pos > $toffset) {
                            $detect = true;
                            $offset = $toffset;
                            $next_line = false;
                            $result = [];
                            continue;
                        }
                    }
                }
                $this->m_last_match = null;
                $v_detect = $r;
                break;
            }
            if ($next_line && ($toffset !== false)) {
                $offset = $toffset; // + 1;
                $detect = $ln > $offset;
            } else
                $offset = strlen($source);
            $next_line = false;
        }
        $v_detect && $this->autoSkipDefinition($v_detect, $offset);
        return $v_detect;
    }
    /**
    * auto generate doc.
    * @return null|RegexDetectInfo
    */
    public function getParent(): ?RegexDetectInfo
    {
        return $this->m_parent;
    }
    /**
     * retrieve class creator
     * @return string 
     */
    protected function _getClassCreator(): string
    {
        if ($cl = $this->patternCreatorClass) {
            is_subclass_of($cl, RegexMatcherPattern::class) || igk_die('class not a subclass of RegexMatcherPattern');
        }
        return $cl ?? RegexMatcherPattern::class;
    }
    /**
    * auto generate doc.
    * @param string $expression
    * @param ?string $end
    * @param ?string $tokenID
    * @param null|string $refid
    * @param ?array $patterns
    * @return $this
    */
    public function begin(string $expression, ?string $end = null, ?string $tokenID = null, ?string $refid = null, ?array $patterns = null)
    {
        $inf =  Activator::CreateNewInstance($this->_getClassCreator(), [
            $this,
            'type' => RegexMatcherPattern::BEGIN_END_TYPE,
            'begin' => $expression,
            'end' => $end,
            'tokenID' => $tokenID,
            'refid' => $refid,
            'patterns' => $patterns
        ]);
        if ($refid) {
            $this->m_references[$refid] = $inf;
        }
        $this->m_last = $inf;
        if ($this->autoStore) {
            $this->m_matcher[] = $inf;
        }
        return $this;
    }
    /**
     * While.
     * @param string $expression
     * @param null|string $end
     * @param null|string $tokenID
     * @param null|string $refid
     * @param null|array $patterns
     */
    public function while(string $expression, ?string $end = null, ?string $tokenID = null, ?string $refid = null, ?array $patterns = null)
    {
        $inf =  Activator::CreateNewInstance($this->_getClassCreator(), [
            $this,
            'type' => RegexMatcherPattern::BEGIN_WHILE_TYPE,
            'begin' => $expression,
            'end' => $end,
            'tokenID' => $tokenID,
            'refid' => $refid,
            'patterns' => $patterns
        ]);
        if ($refid) {
            $this->m_references[$refid] = $inf;
        }
        $this->m_matcher[] = $inf;
        return $this;
    }
    /**
    * match type
    * @param string $expression
    * @param string|null $tokenID
    * @param null|string $refid
    * @param ?array $pattern
    * @throws IGKException
    * @return $this
    */
    public function match(string $expression, ?string $tokenID = null, ?string $refid = null, ?array $pattern = null)
    {
        $inf = Activator::CreateNewInstance($this->_getClassCreator(), [
            $this,
            'type' => RegexMatcherPattern::MATCH_TYPE,
            'match' => $expression,
            'tokenID' => $tokenID,
            'refid' => $refid
        ]);
        if ($refid) {
            $this->m_references[$refid] = $inf;
        }
        $this->m_last = $inf;
        if ($this->autoStore)
            $this->m_matcher[] = $inf;
        return $this;
    }
    /**
     * get referenceOnlyBlock
     * @param mixed $tab
     * @return RegexMatcherPattern
     */
    public function referenceOnly()
    {
        if (is_null($this->m_refOnly)) {
            $this->m_refOnly =  Activator::CreateNewInstance($this->_getClassCreator(), array_merge([
                $this,
            ], [
                'type' => 'reference'
            ]));
        }
        return $this->m_refOnly;
    }
    /**
    * auto generate doc.
    * @param string $src
    * @param ?callable $filter callable {(string $g)=>boolean}
    * @param mixed & $offset
    * @return array
    */
    public function extract(string $src, $filter = null, &$offset = 0)
    {
        $match = [];
        $pos = &$offset;
        while ($g = $this->detect($src, $pos)) {
            $g = $this->end($g, $src, $pos);
            if (!$filter || $filter($g))
                $match[] = $g->value;
        }
        return $match;
    }
    /**
    * treat text by passing captured segment to the callable.
    * @param string & $src
    * @param string $src
    * @param string $end_token_id
    * @throws Exception
    * @return void
    */
    public function treat(string &$src, callable $callable, string $end_token_id = '__end__')
    {
        $pos = 0;
        $skip = false;
        $bck_src = $src;
        $ln = strlen($src);
        $v_otl = $this->ouputTreatmentListener;
        $v_ref = false;
        if ($v_otl instanceof RegexMatcherOutputListener) {
            // + | --------------------------------------------------------------------
            // + | do not change output treatment
            // + |            
            $v_otl->output = $src;
            $v_ref = true;
        }
        // + | save treatment info 
        $this->m_engine_treatment_info = (object)Activator::CreateNewInstance(RegexMatcherEngineInfo::class, [
            'type' => __FUNCTION__,
            'callable' => $callable,
            'end_token_id' => $end_token_id
        ]);
        $detect = false;
        while ($g = $this->detect($src, $pos)) {
            $detect = true;
            // + | retrieve the info type then continue to end 
            $e = $this->end($g, $src, $pos);
            $bpos = $pos;
            if (!$e || ($callable($e, $pos, $src) === true)) {
                $skip = true;
                break;
            }
            if ($bpos != $pos) {
                // + | position changed
                $this->m_last_offset = null;
            }
        } 
        if (!$detect) {
            if ($v_ref)
                $pos = $ln;
            else {
                $pos = 0;
            }
        } else {
            if (!$skip) { 
                $src_len = strlen($src); 
                if (!is_null($this->m_last_offset)) {
                    $pos = $this->m_last_offset;
                }
                // + | end trailing text 
                $r = substr($src, $pos);
                if (($rlen = strlen($r)) > 0) {
                    // + | origin position 
                    $pos = $ln - $rlen;
                    $callable(Activator::CreateNewInstance(RegexMatcherCapture::class, [
                        'tokenID' => $end_token_id,
                        'value' => $r,
                        'from' => $pos,
                        'to' => $src_len,
                        'parentInfo' => null,
                        'trailingEnd' => true
                    ]), $pos, $bck_src);
                }
            }
        }
        $this->m_pos = $pos;
    }
    /**
     * get end last position 
     * @return null|int 
     */
    public function getLastPosition()
    {
        return $this->m_pos;
    }
    /**
     * append string detection on top level pattern
     * @param string $tokenID 
     * @param bool $escaped 
     * @return $this 
     * @throws IGKException 
     */
    public function appendStringDetection($tokenID = 'string', bool $escaped = false)
    {
        $l = $this->begin("(\"|')", "\\1", $tokenID)->last();
        if ($escaped) {
            $l->patterns = [
                $this->createPattern(['match' => '\\\\.'])
            ];
        }
        return $this;
    }
    /**
     * create string pattern 
     * @param string $tokenID 
     * @param bool $escaped 
     * @return RegexMatcherPattern 
     */
    public function createStringPattern($tokenID = 'string', bool $escaped = false): RegexMatcherPattern
    {
        $l = $this->createPattern(['begin' => "(\"|')", "end" => "\\1", "tokenID" => $tokenID]);
        if ($escaped) {
            $l->patterns = [
                $this->createPattern(['match' => '\\\\.'])
            ];
        }
        return $l;
    }
    /**
     * helper mark some definition 
     * @param string $mark 
     * @param string $tokenID 
     * @return $this 
     * @throws IGKException 
     */
    public function appendSingleLineComment($mark = '\/\/', $tokenID = 'single-comment'): RegexMatcherContainer
    {
        $this->match($mark . "(.+)?", $tokenID);
        return $this;
    }
    /**
    * append brank '()'
    * @param string $tokenId
    * @param mixed $refid
    * @throws IGKException
    * @return $this
    */
    public function appendBrank($tokenId = 'brank', $refid = null): RegexMatcherContainer
    {
        $this->begin('\(', '\)', $tokenId, $refid);
        return $this;
    }
    /**
     * append curly brank
     * @param string $tokenId 
     * @param mixed $refid 
     * @return $this 
     * @throws IGKException 
     */
    public function appendCurlyBrank($tokenId = 'curly-brank', $refid = null)
    {
        $this->begin('\{', '\}', $tokenId, $refid);
        return $this;
    }
    /**
     * append sqare brank
     * @param string $tokenId 
     * @param mixed $refid 
     * @return $this 
     * @throws IGKException 
     */
    public function appendSquareBrank($tokenId = 'square-brank', $refid = null): RegexMatcherContainer
    {
        $this->begin('\[', '\]', $tokenId, $refid);
        return $this;
    }
    /**
    * auto generate doc.
    * @param mixed $tokenId
    * @param mixed $refid
    * @return $this
    */
    public function appendCommentDocBlock($tokenId = 'comment-docbloc', $refid = null): RegexMatcherContainer
    {
        $this->begin('\/\*\*', '\*\/', $tokenId, $refid);
        return $this;
    }
    /**
     * append empty line detection 
     * - ^[^\\S\\n]*(?=\\n)
     * - ^\\h*(?=\\n)
     * @param string $tokenID 
     * @return $this 
     * @throws IGKException 
     * @throws Exception 
     */
    public function appendEmptyLineDetection(string $tokenID = 'empty-line'): RegexMatcherContainer
    {
        $this->match(RegexMatcherUtility::REGEX_EMPTY_LINE, $tokenID)->last();
        return $this;
    }
    /**
     * Append multiline comment.
     * @param mixed $begin
     * @param mixed $end
     * @param mixed $tokenId
     * @param null|mixed $refid
     */
    public function appendMultilineComment($begin = '\/\*', $end = '\*\/', $tokenId = 'comment-multiline', $refid = null)
    {
        $this->begin($begin, $end, $tokenId, $refid);
        return $this;
    }
    /**
     * Init treat closure.
     * @param mixed $mark
     * @param mixed $captures
     * @param mixed $cap
     */
    public static function _InitTreatClosure($mark, $captures, $cap)
    {
        return function ($v) use ($mark, $captures, $cap) {
            return $v;
        };
    }
    /**
    * treat captures
    * @param array<int|string, string|ICaptureDefinition|callable> $captures capture definition
    * @param mixed $cap regex captures
    * @param string $sourceValue
    * @param mixed & $option
    * @throws Exception
    * @return ?string
    */
    private static function _TreatCaptures($captures, $cap, string $sourceValue, &$option = null)
    {
        $offset = 0;
        ksort($captures);
        $boffset = $cap[0][1];
        $v = $cap[0][0];
        $lpos = 0;
        $rv = '';
        $spos = 0;
        $select = false;
        foreach ($captures as $k => $mark) {
            if (!is_numeric($k)) {
                continue;
            }
            if (!($mark instanceof Closure)) {
                $mark = self::_InitTreatClosure($mark, $captures, $cap);
            }
            $c = igk_getv($cap, $k);
            if (!$c) continue;
            $offset = $c[1] - $boffset;
            $ln = strlen($c[0]);
            $option = $option ?? igk_createobj();
            $ch = $mark($c[0], $sourceValue, $k, $option);
            $rv = substr($rv, 0, $lpos) . substr($v, $spos, $offset - $spos) . $ch . substr($v, $offset + $ln);
            $lpos = $offset + strlen($ch);
            $spos = $offset + $ln;
            $select = true;
        }
        if (!$select)
            $rv = $v;
        return $rv;
    }
    /**
     * append matcher pattern
     * @param RegexMatcherPattern $pattern 
     * @return void 
     */
    public function append(RegexMatcherPattern $pattern)
    {
        if ($pattern && ($pattern->getMatcher() === $this)) {
            if (array_search($pattern, $this->m_matcher) === false) {
                $this->m_matcher[] = $pattern;
                if (($pattern->refid) && (!in_array($pattern->refid, $this->m_references))) {
                    $this->m_references[$pattern->refid] = $pattern;
                }
            }
        }
    }
    /**
     * set flags of patterns
     * @param ?array $patterns 
     * @return void 
     */
    public function setInitialPatterns(?array $patterns)
    {
        $this->m_initialPatterns = $patterns;
    }
    /**
     * export regex container
     * @param string $name scopeName of the definition
     * @return array assoc array of definitions 
     */
    public function export($name): array
    {
        $ct = new RegexMatcherContainerTmLanguageConverter;
        return $ct->convert($this, $name);
    }
    /**
     * export to json encoding
     * @param self $container 
     * @param string $name 
     * @return string|false 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function EncodeToJSON($container, string $name)
    {
        return JSon::Encode($container->export($name), JSonEncodeOption::IgnoreEmpty(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    /**
    * auto generate doc.
    * @param mixed $list
    * @var array $list
    * @return void
    */
    public function loadRepository($list)
    {
        $cl = $this->_getClassCreator();
        $tlist = array_keys(get_class_vars($cl));
        foreach ($list as $k => $v) {
            $m = [$this];
            foreach ($tlist as $kk) {
                if ($l = igk_getv($v, $kk)) {
                    $m[$kk] = $l;
                }
            }
            if ($c = Activator::CreateNewInstance($cl, $m)) {
                // + | update data type 
                $c->type = RegexMatcherPattern::GetMatcherType($c);
                if (is_int($k)) {
                    $k = 'item_' . $k;
                }
                $this->m_references[$k] = $c;
            }
        }
    }
    /**
     * create a chain list atable 
     * @param array $cap tab regex result 
     * @return object[] 
     */
    public static function CreateChainList($cap)
    {    
        $root = (object)[];
        $root->childs = [];
        $root->value = $cap[0][0];
        $root->indice = $cap[0][1];
        $root->parent = null;
        $li = [$root];
        array_shift($cap);
        $k = 1;
        $named = null;
        while (count($cap) > 0) {
            $key = key($cap);
            $v = array_shift($cap);
            if (!is_numeric($key)) {
                $named = $key;
                continue;
            }
            $child = (object)[];
            $child->childs = [];
            $child->parent = null;
            $child->value = $v[0];
            $child->indice = $v[1];
            $ki = $k - 1;
            $vparent = null;
            $clen = $child->indice + strlen($child->value);
            while (($ki >= 0) && (!$vparent)) {
                $preview = $li[$ki--];
                $plen = $preview->indice + strlen($preview->value);
                if (($child->indice >= $preview->indice) && ($plen >= $clen)) {
                    $vparent = $preview;
                }
            }
            $child->parent = $vparent;
            if ($vparent) {
                $vparent->childs[] = $k;
            }
            $li[] = $child;
            $k++;
            if ($named) {
                $li[$named] = $child;
            }
        }
        return $li;
    }
    /**
    * auto generate doc.
    * @param array $captures
    * @param mixed $cap
    * @param string $sourceValue
    * @param mixed & $option
    * @param mixed $chainList
    * @return mixed
    */
    public static function TreatCaptures(array $captures, $cap, string $sourceValue, &$option = null, $chainList = null)
    {
        $chainList  = $chainList ?? self::CreateChainList($cap);
        ksort($captures);
        $boffset = $cap[0][1];
        $v = $cap[0][0];
        $lpos = 0;
        $rv = '';
        $spos = 0;
        $marks = [];
        $cap_treat_mark = [];
        $cap_listener = igk_getv($option, 'captureHandlerListener');
        $handle_mark_capture = function ($k, $mark, $c, $register = true) use ($cap_listener, $captures, $cap, &$handle_mark_capture, &$cap_treat_mark, &$option, &$marks, $sourceValue, $boffset, $chainList) {
            if (!($mark instanceof Closure)) {
                $mark = $cap_listener ?? self::_InitTreatClosure($mark, $captures, $cap);
            }
            $option = $option ?? igk_createobj();
            $chain = $chainList[$k];
            $offset = $c[1] - $boffset;
            $src = $c[0];
            $ch = '';
            if ($chain->childs) {
                // + | contains children so request so update next children with mark value 
                $children = array_slice($chain->childs, 0);
                $ch = $chain->value;
                $nsb = $src;
                $toffset = 0;
                while (count($children) > 0) {
                    $tq = array_shift($children);
                    if (key_exists($tq, $cap_treat_mark)) {
                        throw new Exception("not allowed");
                        continue;
                    }
                    $cmark = igk_getv($captures, $tq);
                    if ($tc = igk_getv($cap, $tq)) {
                        $sch = $handle_mark_capture($tq, $cmark, $tc, false);
                        $sb = $tc[1] - $boffset;
                        $prefix = substr($src, $toffset, $sb - $toffset) . $sch;
                        $nsb = $prefix . substr($src, $tc[1] + strlen($tc[0]));
                        $toffset = strlen($prefix);
                    }
                }
                $ch = $mark($nsb, $sourceValue, $k, $option);
            } else {
                $ch = $mark($src, $sourceValue, $k, $option);
            }
            if ($register) {
                $marks[] = (object)[
                    "value" => $src,
                    "treatValue" => $ch,
                    "indice" => $offset
                ];
            }
            $cap_treat_mark[$k] = [$ch, $offset];
            return $ch;
        };
        foreach ($captures as $k => $mark) {
            if (!is_numeric($k) || key_exists($k, $cap_treat_mark)) {
                continue;
            }
            $c = igk_getv($cap, $k);
            if (!$c) continue;
            $handle_mark_capture($k, $mark, $c);
        }
        $rv = $v;
        if (count($marks) > 0) {
            $spos = 0;
            $lpos = 0;
            $tv = '';
            while (count($marks) > 0) {
                $q = array_shift($marks);
                $prefix = substr($v, $spos, $q->indice - $spos) . $q->treatValue;
                $tv = substr($tv, 0, $lpos) . $prefix . substr($v, $q->indice + strlen($q->value));
                $spos = $q->indice + strlen($q->value);
                $lpos += strlen($prefix);
            }
            $rv = $tv;
        }
        return $rv;
    }
    /**
     * auto generate doc.
     * @param array $args
     * @return RegexMatcherPattern
     */
    public function createPattern(array $args): RegexMatcherPattern
    {
        if (isset($args[$i = 'include'])) {
            if ($inc = igk_getv($args, $i)) {
                if ($inc[0] == '#') {
                    $id = substr($inc, 1);
                    if ($l = igk_getv($this->m_references, $id)) {
                        return $l;
                    }
                } else {
                    if (!($l = igk_getv(self::$sm_LoadingFormatters, $inc))) {
                        $app = igk_app();
                        $regex_f = null;
                        if ($srv = $app->getService(IGKServices::FORMATTER_SERVICE)) {
                            $g = $srv->resolveFormat($inc);
                            $regex_f = $g ?? igk_getv($this->m_options, 'showError') &&  igk_die('failed to resolved ');
                        }
                        $l = new RegexMatcherPatternContainer($regex_f, $this);
                        self::$sm_LoadingFormatters[$inc] = $l;
                    }
                    return $l;
                }
            }
        }
        return Activator::CreateNewInstance($this->_getClassCreator(), array_merge([$this], $args));
    }
    /**
     * replace source string
     * @param string $src 
     * @param callable $callback 
     * @param int $offset 
     * @return string 
     */
    public function replace(string $src, callable $callback, int $offset = 0): string
    {
        $o = '';
        $pos = $offset;
        $toffset = 0;
        while ($g = $this->detect($src, $pos)) {
            if ($e = $this->end($g, $src, $pos)) {
                $o .= substr($src, $toffset, $e->from - $toffset);
                $o .= $callback($e, $o, $toffset);
                $toffset = $e->to;
            }
        }
        $o .= substr($src, $toffset);
        return $o;
    }
    /**
     * updateline buffer definition 
     * @param string $line 
     * @param mixed &$pos 
     * @return string 
     */
    public function updateBufferLine(string $line, &$pos): string
    {
        /**
         * @var mixed $buff 
         */
        $buff = $this->getBuffer(true) ?? '';
        $pos = strlen($buff);
        $line = $buff . $line;
        return $line;
    }
    /**
    * auto generate doc.
    * @param mixed $clear
    * @return mixed
    */
    protected function getBuffer($clear = false)
    {
        $sb = $this->m_lineBuffer;
        if ($clear) {
            $this->m_lineBuffer = null;
        }
        return $sb;
    }
    /**
    * auto generate doc.
    * @param string $source
    * @param mixed $offset
    * @return void
    */
    protected function _skipLineBufferEnd(string $source, $offset)
    {
        $v_backupOffset = $offset;
        $v_ln = strlen($source);
        if ((!$this->markEndOfSource) &&
            ($v_backupOffset >= $v_ln)
        ) {
            return true;
        }
    }
}