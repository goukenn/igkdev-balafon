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
use IGK\System\Text\RegexMatcherPattern;
use IGK\System\Text\IRegexMatchPatternOutpuTreatmentListener;
use IGKException;
///<summary></summary>
/**
 * extract definitio beetween begin/end definition 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherContainer implements IRegexMatcherContainer
{
    const REGEX_OPTION = '/^\(\?\b(?P<add>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\b(:\b(?P<remove>i(m|x|(mx|xm)?)|m(i|x|(ix|xi))?|x(i|m|(im|mi))?)\b)?\)/';
    /**
     * detect contain start line 
     */
    const REGEX_START_LINE = '/(?<!\\\\|\w|\[)\^/'; 

    const REGEX_CONTINUES_EMPTY_LINE = '/^\\s*$/';
    /**
     * detect contain end line 
     */
    const REGEX_END_LINE = '/(?<!\\\)\\$/';
    const BEGIN_END_TYPE = RegexMatcherPattern::BEGIN_END_TYPE;
    const BEGIN_WHILE_TYPE = 'begin/while';
    const MATCH_TYPE = 'match';
    const INCLUDE = 'include';
    /**
     * 
     * @var ?IRegexMatchPatternStateListener
     */
    var $matchPatternStateListener;
    /**
     * 
     * @var ?IRegexMatchPatternOutpuTreatmentListener
     */
    var $ouputTreatmentListener;
    /**
     * 
     * @var mixed
     */
    private $m_parent;
    /**
     * last match
     * @var ?RegexMatcherPattern
     */
    private $m_last_match;
    /**
     * last match end info
     * @var mixed
     */
    private $m_last_info;
    /**
     * to avoid infinite loop on match
     * @var ?int
     */
    private $m_last_offset;
    private $m_startflag;
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
    private $m_refOnly;
    /**
     * to dispatch for match calling 
     * @var  
     */
    private $m_engine_treatement_info;
    /**
     * get last inserted match information 
     * @return ?RegexMatcherPattern
     * @throws Exception 
     */
    public function last()
    {
        $c = count($this->m_matcher);
        return $c > 0 ? igk_getv($this->m_matcher, $c - 1) : null;
    }
    /**
     * 
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
     * 
     * @param string $id 
     * @return mixed 
     * @throws Exception 
     */
    public function getMatcherByRefId(string $id)
    {
        return igk_getv($this->m_references, $id);
    }
    /**
     * clear all 
     * @return void 
     */
    public function clear()
    {
        $this->m_matcher = [];
        $this->m_references = [];
    }
    public function __construct()
    {
        $this->m_startflag = false;
    }
    /**
     * do end operation 
     * @param RegexTreatMatchInfo $info object info class 
     * @param string $source 
     * @param int &$offset 
     * @return object|RegexMatcherCapture|void 
     * @throws Exception 
     */
    public function end($info, string $source, int &$offset = 0)
    {
        $e = $this->_treatEnd($info, $source, $offset);
        if ($e) {
            // + | --------------------------------------------------------------------
            // + | upate last info and parent definition 
            // + | 
            $this->m_last_info = $info;
            $this->m_parent = $e->parentInfo;
        }
        return $e;
    }
    /**
     * save container state
     * @return mixed 
     */
    public function saveState()
    {
        return ($f = $this->matchPatternStateListener) ? $f->saveState() : null;
    }
    /**
     * restore container state
     * @return mixed 
     */
    public function restoreState()
    {
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
     * 
     * @param RegexTreatMatchInfo $info 
     * @param mixed $source 
     * @param int &$offset 
     * @return mixed|void 
     * @throws IGKException 
     * @throws Exception 
     */
    protected function _treatEnd($info, $source, int &$offset)
    {

        $tabinfo = [$info];
        // skip offset update 
        $skip = $this->m_parent === $info;
        $v_size = strlen($info->value);
        if ($v_size == 0) {
            /// TODO: TREAT matching 
            // detect last end pattern 
            if ($info->match === $this->m_last_match) {
                if ($this->m_parent == null) {
                    $i = strpos($source, "\n", $offset);
                    if ($i === false) {
                        $offset = strlen($source) + 1;
                    } else
                        $offset++;
                    return null;
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
                    // - | $offset++;
                    $this->m_parent = $info->parent;
                    return $this->_endinfo($info, $source, $n);
            }
        }
        while (count($tabinfo) > 0) {
            $info = array_shift($tabinfo);
            $k = $info->match;
            $v_size = strlen($info->value);
            // + | update parent info - 
            $this->m_parent = $info->parent;

            switch ($k->type) {
                case RegexMatcherPattern::BEGIN_END_TYPE:
                case RegexMatcherPattern::BEGIN_WHILE_TYPE:
                    if (!property_exists($info->match,'patterns')){
                        $info->match->patterns = null;
                    }

                    $b = igk_getv($k, 'end');
                    $o = '';
                    if ($b) {
                        // + | is begin 
                        $b = $b ? sprintf("/%s/%s", $b, $o) : null;
                    }
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
                    $v_skipped =  $skip;
                    if (!$skip)
                        $offset += $v_size; // update offset to check end 
                    else
                        $skip = false;
                    $cpos = $offset;
                    $compared_end = ($cpos >= $offset) && $info->match->patterns ? $this->_comparedPattern($info, $info->match->patterns, $source, $cpos) : null;
                    $start_line = false;
                    if ($tab = $this->_matchOffset($b, $source, $offset, $start_line)) {
                        $v_current_offset = $tab[0][1];
                        $n = $v_current_offset  + strlen($tab[0][0]);
                        // + | if empty and offset not change then update to next 
                        if (empty($tab[0][0]) && !$v_skipped && ($v_current_offset == $offset)) {
                            $offset++; // + | move forward to detect the real next end that match condition
                            array_unshift($tabinfo, $info);
                            $skip = true;
                            continue 2;
                        }
                        // check of compared_end first match 
                        // $tln = $info->pos + strlen($info->value); 
                        // if ($compared_end && ($compared_end->pos < $n)) {
                        if ($compared_end && ($compared_end->pos < $v_current_offset)) {
                            // + | handle compared match item 
                            $v_continue = false;
                            $r = $this->_handleComparedMatchItem($info, $compared_end, $offset, $v_continue);
                            if ($r) {
                                // + | update match pattern value 
                                $r->value = substr($source, $r->from, $r->to-$r->from);
                                return $r;
                            }
                            // go back to current pos then check end 
                            $offset = $compared_end->pos;
                            array_unshift($tabinfo, $compared_end);
                            continue 2;
                        }
                        // update next offset 
                        $offset = $n;
                        $v_tvalue = substr($source, $info->pos, $n - $info->pos);
                        $v_tcaptures = $this->_treatEndCaptures($info, $v_tvalue, $tab);
                        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                            $this,
                            'tokenID' => igk_getv($k, 'tokenID'),
                            'from' => $info->pos,
                            'to' => $n,
                            'value' => $v_tcaptures,
                            'sourceData' => $v_tvalue,
                            'beginCaptures' => $info->captures,
                            'endCaptures' => $tab,
                            'parentInfo' => $info->parent
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
                            array_unshift($tabinfo, $info);
                            $skip = true;
                            continue 2;
                        }
                        $offset = $tln;
                        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                            'tokenID' => $k['tokenID'],
                            'from' => $info->pos,
                            'to' => $offset,
                            'value' => substr($source, $info->pos),
                            'beginCaptures' => $info->captures,
                            'parentInfo' => $info->parent
                        ]);
                    }
                    break;
                case RegexMatcherPattern::MATCH_TYPE:
                    $n = $info->pos + $v_size;
                    $offset = $n;
                    $treated = $src = substr($source, $info->pos, $n - $info->pos);
                    $option = null;
                    if ($k->captures) {
                        $src = self::TreatCaptures($k->captures, $info->captures, $src, $option);
                    }
                    if ($k->patterns) {
                        // + | logic to treat match patterns  - normally not treated treat to patterns
                        // + | require engine treatment listener 
                        $inf = $this->m_engine_treatement_info;
                        if ($inf->type = 'treat') {
                            list($callable, $end_token_id) = igk_extract($inf, 'callable|end_token_id');
                            $g = new static;
                            $g->m_matcher = $k->patterns;
                            $g->m_parentInfo = null;
                            // pass listener 
                            $g->matchPatternStateListener = $this->matchPatternStateListener;
                            $g->ouputTreatmentListener = $this->ouputTreatmentListener;
                            $g->saveState();
                            $g->treat($src, $callable, $end_token_id);
                            $src = $g->getOuput() . substr($src, $g->getLastPosition());
                            $g->restoreState();
                        }
                    }
                    if (($offset == 0) && ($v_size == 0)) {
                        // + | update to next offset 
                        $offset = 1;
                    }
                    // - for match result
                    return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                        'tokenID' => $k['tokenID'],
                        'from' => $info->pos,
                        'to' => $n,
                        'value' => $src, // real value 
                        'sourceValue' => $treated, // source value
                        'parentInfo' => $info->parent,
                        'beginCaptures' => $info->captures,
                        'captures' => $info->captures,
                        'endCaptures' => $info->captures,
                        'option' => $option
                    ]);
            }
        }
    }
    /**
     * 
     * @return mixed 
     */
    protected function _treatEndCaptures($info, string $value, ?array $endCap = null)
    {
        $v_t = [];
        list($beginCaptures, $endCaptures, $captures, $type) = igk_extract($info->match, 'beginCaptures|endCaptures|captures|type');
        $_scap = $beginCaptures ?? $captures;
        $_ecap = $endCaptures ?? $captures;
        $_treatment_info = $this->m_engine_treatement_info;
        $_listener = null;
        if ($_treatment_info) {
            $_listener = (function ($info, $callable, $value) {
                return function (RegexCaptureInfo $cap, $option) use ($callable, $info, $value) {
                    // + | ----------------
                    // + | CREATE INSTANCE OF REGEX CAPTURE - 
                    // + | ----------------
                    // + | 
                    $inf = Activator::CreateNewInstance(RegexMatcherCapture::class, [
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
                            $l = self::TreatCaptures($_ecap, $cap, $cap[0], $opts);
                        }
                        $v_t['endTreated'] = $l;
                    }
                }
                break;
        }
        if ($v_t) {
            // update value
            if (key_exists('treated', $v_t)) {
                $c = $v_t['treated'];
                return $c;
            } else {
                $endPos = null;
                $startLength = 0;
                $begin = $end = '';
                if (!is_null($c = igk_getv($v_t, 'startTreated'))) {
                    $cap = $info->captures;
                    $begin = $c;
                    $startLength = strlen($cap[0][0]);
                }
                if (!is_null($c = igk_getv($v_t, 'endTreated'))) {
                    $cap = $endCap;
                    $end = $c;
                    $endPos =  $cap[0][1];
                }
                $v_s = RegexMatcherUtility::TreatBeginEndCapture($value, $begin, $end, $startLength, $endPos);
                return $v_s;
            }
        }
        return $value;
    }
    /**
     * 
     */
    protected function _handleComparedMatchItem($info, $compared_end, &$offset, &$v_continue = false)
    {
        $l = $compared_end;
        $k = $l->match;
        list($v_id, $v_type) = igk_extract($k, 'tokenID|type');
        $v_continue = false;
        if ($v_type != RegexMatcherPattern::MATCH_TYPE) {
            // continue until detected the end of this block
            $v_continue = true;
            return;
        }
        $this->m_parent = $info;
        if (empty($compared_end->value)) {
            // return the base definition 
            $info->endType = 'end';
            $offset = $l->pos;
            return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                $this,
                'tokenID' =>  $v_id,
                'from' => $l->pos,
                'to' => $l->pos,
                'value' => '',
                'beginCaptures' => $l->captures,
                'endCaptures' => $l->captures,
                'parentInfo' => $info
            ]);
        } else {
            // capture continue capture to childs 
            $n = $l->pos + strlen($compared_end->value);
            $offset = $n;
            return Activator::CreateNewInstance(RegexMatcherCapture::class, [
                $this,
                'tokenID' => $v_id,
                'from' => $l->pos,
                'to' => $n,
                'value' =>  null,
                'beginCaptures' => $info->captures,
                'endCaptures' => null,
                'parentInfo' => $info
            ]); 
        }
        $v_continue = true;
    }
    /**
     * treat end info
     * @param mixed $info 
     * @param string $source 
     * @param int $n 
     * @param mixed $endcapture 
     * @return mixed 
     * @throws IGKException 
     */
    private function _endinfo($info, string $source,  int $n, $endcapture = null)
    {
        $k = $info->match;
        return Activator::CreateNewInstance(RegexMatcherCapture::class, [
            $this,
            'tokenID' => $k['tokenID'],
            'from' => $info->pos,
            'to' => $n,
            'value' => substr($source, $info->pos, $n - $info->pos),
            'beginCaptures' => $info->captures,
            'endCaptures' => $endcapture,
            'parentInfo' => $info->parent
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
     * check for regex with start_line detection
     * @param string $regex end regex
     * @param string $source source of to check
     * @param int $offset offet to match
     * @param bool &$start_line is start line request
     * @return array|false 
     */
    private function _matchOffset(string $regex, string $source, int $offset, &$start_line = false)
    {
        $start_line = preg_match(self::REGEX_START_LINE, $regex) > 0;
        $end_line = preg_match(self::REGEX_END_LINE , $regex) > 0;
        $result = [];
        $tab = [];
        if (preg_match($regex, $source, $tab, PREG_OFFSET_CAPTURE, $offset)) {
            $result[] = $tab;
        }
        $tab = [];
        $TLen = strlen($source);
        $next_line = $offset < $TLen ? strpos($source, "\n", $offset) : false;

        if ($start_line && $end_line){
            if ($offset==0){
                $sline = substr($source, 0, $next_line);
                if (preg_match($regex, $sline, $tab, PREG_OFFSET_CAPTURE, 0)) {
                    // update offset
                    self::_UpdateTabOffset($tab, $offset);
                    $result[] = $tab;
                }
            }
        }


        if ($start_line && ($offset < $TLen)) {
            // + | get next line  
            $coffset = $offset;
            if (false !== $next_line) {
                $coffset = $next_line == $offset ? $offset + 1 : $next_line + 1;
            }
            if (preg_match($regex, substr($source, $coffset), $tab, PREG_OFFSET_CAPTURE, 0)) {
                // update offset
                self::_UpdateTabOffset($tab, $coffset);
                $result[] = $tab;
            }
        }
        if ($end_line && ($offset < $TLen)){
            $coffset = $offset>0?$next_line: 1;
            $j = substr($source, $offset, abs($coffset - $offset));
            // check if continue line match 
            $lc = false;
            if (!trim($j) && ($regex == self::REGEX_CONTINUES_EMPTY_LINE)){
                $ln = $coffset; 
                while(($pos = strpos($source,"\n",$ln)) && (preg_match($regex,substr($source, $offset, $pos-$offset)))){
                    $ln = $pos+1; 
                }
                $j = substr($source, $offset, $ln - $offset);  
                $lc = true;
            }  
            if ( (!$lc || strlen($j)) && preg_match($regex, $j, $tab, PREG_OFFSET_CAPTURE, 0)) {
                // update offset
                self::_UpdateTabOffset($tab, $offset);
                $result[] = $tab;
            } 
        }
        // compare result
        if ($result) {
            usort($result, function ($a, $b) {
                return $a[0][1] <=> $b[0][1];
            });
            return $result[0];
        }
        return false;
    }
    /**
     * detecd for compared pattern
     * @param mixed $patterns 
     * @param mixed $source 
     * @param mixed &$offset 
     * @return mixed|void 
     * @throws Exception 
     */
    private function _comparedPattern($info, $patterns, $source, &$offset)
    {
        if (!$patterns) {
            return null;
        };
        $g = new static;
        $g->m_matcher = $patterns;
        $g->m_parentInfo = $info;
        $g->m_tag = __METHOD__;
        $tpos = $offset;
        return $g->detect($source, $tpos);
    }
    /**
     * treat regex
     * @param string $b include regex
     * @param string &$o ouput options
     * @return string the non // regex definition 
     * @throws Exception 
     */
    protected function _treatRegex(string $b, string &$o)
    {
        $o = '';
        if (preg_match(self::REGEX_OPTION, $b, $tab)) {
            $a = $tab['add'];
            $x = false;
            if ($a) {
                if (strpos($a, 'i') !== false) {
                    $o .= 'i';
                }
                if (strpos($a, 'm') !== false) {
                    $o .= 'm';
                }
                if (strpos('x', $a) !== false) {
                    $x = true;
                }
            }
            $a = igk_getv($tab, 'remove');
            if ($a) {
                if (strpos('i', $a) !== false) {
                    $o .= str_replace('i', '', $o);
                }
                if (strpos('m', $a) !== false) {
                    $o .= str_replace('m', '', $o);
                }
                if (strpos('x', $a) !== false) {
                    $x = false;
                }
            }
            // TODO : handle extra data
            $b = substr($b, strlen($tab[0]));
            if ($x) {
                $b = RegexMatcherUtility::TreatExtended($b);
            }
        }
        return $b;
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
     * 
     * @param mixed $info 
     * @param mixed &$result 
     * @param mixed $b 
     * @param mixed $source 
     * @param mixed &$offset 
     * @param mixed $k 
     * @param mixed &$next_line 
     * @return void 
     * @throws Exception 
     */
    private function _startMatch($info, &$result, $b, $source, &$offset, $k, &$next_line)
    {
        $o = '';
        if ($b) {
            $b = $this->_treatRegex($b, $o);
            $b = sprintf("/%s/%s", $b, $o);
        }
        if ($b) {
            $cline = false;
            $tab = $this->_matchOffset($b, $source, $offset, $cline);
            if ($tab) {
                // + | create detect result info
                $result[] = (object)[
                    'pos' => $tab[0][1],
                    'value' => $tab[0][0],
                    'match' => $k,
                    'captures' => $tab,
                    'parent' => $info
                ];
            }
            $next_line = $next_line || $cline;
        }
    }
    /**
     * detecting regex type 
     * @param string $source,
     * @param int $offset position offset to match
     * @return ?IRegexMatcherDetectInfo
     */
    public function detect(string $source, int &$offset = 0)
    {
        $v_flag_current = false;
        if (!$this->m_startflag) {
            $this->m_startflag = true;
        }
        if ($this->m_parent) {
            // continue with detected parent
            return $this->m_parent;
        }
        if (!is_null($this->m_last_offset) && ($this->m_last_offset == $offset)) {
            $error = true;
            if (($this->m_last_info) && ($this->m_last_info->pos == $offset)) {
                //skip
                $error = false;
                $offset = $this->m_last_info->pos + strlen($this->m_last_info->value) + 1;
                $v_flag_current = true;
            }
            // detect that 
            if ($error)
                throw new Exception("[BLF] - offset not update " . $offset);
        }
        $this->m_last_offset = $offset;
        // $v_end_line_detect_offset = null;
        $m = $this->m_matcher;
        $result = [];
        $next_line = false;
        $info = $this->m_parentInfo;
        $detect = true;
        $ln = strlen($source);
        while ($detect) {
            $detect = false;
            foreach ($m as $k) {
                if (is_array($k)){
                    // create an object
                    $k = (object)$k;
                }
                if (!isset($k->type)){
                    $k->type = RegexMatcherUtility::GetPatternType($k); 
                }
                switch ($k->type) {
                    case self::BEGIN_END_TYPE:
                        $b = igk_getv($k, 'begin');
                        if ($b) {
                            $this->_startMatch($info, $result, $b, $source, $offset, $k, $next_line);
                        }
                        break;
                    case self::INCLUDE:
                        throw new Exception('not implement');
                        break;
                    case self::BEGIN_WHILE_TYPE:
                        throw new Exception('not implement');
                        break;
                    case self::MATCH_TYPE:
                        $b = igk_getv($k, 'match');
                        if ($b) {
                            $this->_startMatch($info, $result, $b, $source, $offset, $k, $next_line);
                        }
                        break;
                }
            }
            // next line 
            $toffset = $offset < $ln ? strpos($source, "\n",  $offset) : false;
            if (($toffset !== false) && ($offset == $toffset)) {
                $toffset++;
            }
            if (count($result) > 0) {
                // $v_end_line_detect_offset = null;
                $bp = $result;
                $r = self::_ReduceResult($bp);
                if ($v_flag_current && $next_line) {
                    if ((($r->pos - 1) > $toffset)) {
                        $detect = true;
                        $offset = $toffset + 1;
                        $next_line = false;
                        $result = [];
                        continue;
                    }
                } else 
                if ($next_line  && ($toffset !== false) && ($toffset != $offset)) {
                    if ($r->pos > $toffset) {
                        // probably require to check start line and compare
                        $detect = true;
                        // $offset = $r->pos-1;
                        $offset = $toffset;
                        $next_line = false;
                        $result = [];
                        continue;
                    }
                }
                $offset = $r->pos;
                $this->m_last_match = null;
                return $r;
            }
            if ($next_line && ($toffset !== false)) {
                $offset = $toffset;// + 1;
                $detect = $ln > $offset;
            } else
                $offset = strlen($source);
            $next_line = false;
        }
        $this->m_startflag = false;
    }
    /**
     * 
     * @param string $expression 
     * @param null|string $end 
     * @param null|string $tokenID 
     * @param null|string $refid 
     * @return $this 
     */
    public function begin(string $expression, ?string $end = null, ?string $tokenID = null, ?string $refid = null, ?array $patterns = null)
    {
        $inf =  Activator::CreateNewInstance(RegexMatcherPattern::class, [
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
        $this->m_matcher[] = $inf;
        return $this;
    }
    public function while(string $expression, ?string $end = null, ?string $tokenID = null, ?string $refid = null, ?array $patterns = null)
    {
        $inf =  Activator::CreateNewInstance(RegexMatcherPattern::class, [
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
     * @return $this 
     * @throws IGKException 
     */
    public function match(string $expression, string $tokenID = null, ?string $refid = null, ?array $pattern = null)
    {
        $inf = Activator::CreateNewInstance(RegexMatcherPattern::class, [
            $this,
            'type' => RegexMatcherPattern::MATCH_TYPE,
            'match' => $expression,
            'tokenID' => $tokenID,
            'refid' => $refid
        ]);
        if ($refid) {
            $this->m_references[$refid] = $inf;
        }
        $this->m_matcher[] = $inf;
        return $this;
    }
    /**
     * 
     * @param mixed $tab 
     * @return RegexMatcherPattern 
     * @throws IGKException 
     */
    public function referenceOnly()
    {
        if (is_null($this->m_refOnly)) {
            $this->m_refOnly =  Activator::CreateNewInstance(RegexMatcherPattern::class, array_merge([
                $this,
            ], [
                'type' => 'reference'
            ]));
        }
        return $this->m_refOnly;
    }
    /**
     * 
     * @param string $src source 
     * @param ?callable $filter callable {(string $g)=>boolean}
     * @return array 
     * @throws Exception 
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
     * @param string $src 
     * @param callable(IRegexMatcherCapture, & int, & string ):void $callable 
     * @return void 
     * @throws Exception 
     */
    public function treat(string $src, callable $callable, string $end_token_id = '__end__')
    {
        $pos = 0;
        $skip = false;
        // + | save treatment info 
        $this->m_engine_treatement_info = (object)[
            'type' => __FUNCTION__,
            'callable' => $callable,
            'end_token_id' => $end_token_id
        ];
        while ($g = $this->detect($src, $pos)) {
            //retrieve the info type then continue to end 
            $g = $this->end($g, $src, $pos);
            $bpos = $pos;
            if (!$g || ($callable($g, $pos, $src) === true)) {
                $skip = true;
                break;
            }
            if ($bpos != $pos) {
                // + | position changed
                $this->m_last_offset = null;
            }
        }
        if (!$skip) {
            $src_len = strlen($src);
            if (($src_len == $pos) && ($this->m_last_offset < $pos)) {
                $pos = $this->m_last_offset;
            }
            $r = substr($src, $pos);
            if (strlen($r) > 0) {
                $callable(Activator::CreateNewInstance(RegexMatcherCapture::class, [
                    'tokenID' => $end_token_id,
                    'value' => $r,
                    'from' => $pos,
                    'to' => $src_len,
                    'parentInfo' => null,
                    'trailingEnd' => true
                ]), $pos, $src);
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
     * @return $this 
     * @throws IGKException 
     */
    public function appendStringDetection($tokenID = 'string')
    {
        $this->begin("(\"|')", "\\1", $tokenID);
        return $this;
    }
    /**
     * helper mark some definition 
     * @param string $mark 
     * @param string $tokenID 
     * @return $this 
     * @throws IGKException 
     */
    public function appendSingleLineComment($mark = '\/\/', $tokenID = 'single-comment')
    {
        $this->match($mark . ".+", $tokenID);
        return $this;
    }
    /**
     * append brank '()'
     * @param string $tokenId 
     * @return $this 
     * @throws IGKException 
     */
    public function appendBrank($tokenId = 'brank', $refid = null)
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
    public function appendSquareBrank($tokenId = 'square-brank', $refid = null)
    {
        $this->begin('\[', '\]', $tokenId, $refid);
        return $this;
    }
    public function appendCommentDocBlock($tokenId = 'comment-docbloc', $refid = null)
    {
        $this->begin('\/\*', '\*\/', $tokenId, $refid);
        return $this;
    }
    public static function _InitTreatClosure($mark,$captures, $cap)
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
     * @param string $option interanal options
     * @return ?string 
     * @throws Exception 
     */
    public static function _TreatCaptures($captures, $cap, string $sourceValue, &$option = null)
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
            // remove 
            // treat 
            // ------------
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
     * 
     * @param mixed $name 
     * @return void 
     */
    public function export($name)
    {
        $d = [];
        $d['scopeName'] = $name;
        $d['repository'] = $this->m_references;
        $d['patterns'] = $this->m_matcher;
        return $d;
    }


    /**
     * export to json encoding
     * @param self $container 
     * @return string|false 
     * @throws IGKException 
     * @throws Exception 
     */
    public static function EncodeToJSON($container, $name)
    {
        return JSon::Encode($container->export($name), JSonEncodeOption::IgnoreEmpty(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @var array $list
     * @return void 
     */
    public function loadRepository($list)
    {
        $tlist = array_keys(get_class_vars(RegexMatcherPattern::class));
        foreach ($list as $k => $v) {
            $m = [$this];
            foreach ($tlist as $kk) {
                if ($l = igk_getv($v, $kk)) {
                    $m[$kk] = $l;
                }
            }
            if ($c = Activator::CreateNewInstance(RegexMatcherPattern::class, $m)) {
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
     * @param mixed $cap 
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
        while (count($cap) > 0) {

            $v = array_shift($cap);
            $child = (object)[];
            $child->childs = [];
            $child->parent = null;
            $child->value = $v[0];
            $child->indice = $v[1];
            // get child parent
            $ki = $k - 1;
            $vparent = null;
            $clen = $child->indice + strlen($child->value);
            while (($ki >= 0) && (!$vparent)) {
                $preview = $li[$ki--];
                $plen = $preview->indice + strlen($preview->value);
                if (($child->indice >= $preview->indice) && ($plen >= $clen)) {
                    // is child of 
                    $vparent = $preview;
                }
            }
            $child->parent = $vparent;
            if ($vparent) {
                $vparent->childs[] = $k;
            }
            $li[] = $child;
            $k++;
        }
        return $li;
    }

    /**
     * 
     * @param mixed $captures 
     * @param mixed $cap matching . preg_rep result with OFFSET flag
     * @param string $sourceValue 
     * @param mixed &$option 
     * @param mixed $chainList 
     * @return mixed 
     * @throws Exception 
     */
    public static function TreatCaptures($captures, $cap, string $sourceValue, &$option = null, $chainList = null)
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

        $handle_mark_capture = function ($k, $mark, $c, $register = true) use ($captures, $cap, &$handle_mark_capture, &$cap_treat_mark, &$option, &$marks, $sourceValue, $boffset, $chainList) {
            if (!($mark instanceof Closure)) {
                $mark = self::_InitTreatClosure($mark,$captures, $cap);
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
                    $tc = igk_getv($cap, $tq);
                    $sch = $handle_mark_capture($tq, $cmark, $tc, false);
                    // just update the parent fields
                    $sb = $tc[1] - $boffset;
                    $prefix = substr($src, $toffset, $sb - $toffset) . $sch;
                    $nsb = $prefix . substr($src, $tc[1] + strlen($tc[0]));
                    $toffset = strlen($prefix);
                }
                // $ch = $nsb;
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
                // replace with marked value 
                $prefix = substr($v, $spos, $q->indice - $spos) . $q->treatValue;
                $tv = substr($tv, 0, $lpos) . $prefix . substr($v, $q->indice + strlen($q->value));
                $spos = $q->indice + strlen($q->value);

                $lpos += strlen($prefix);
            }
            $rv = $tv;
        }
        return $rv;
    }
}
