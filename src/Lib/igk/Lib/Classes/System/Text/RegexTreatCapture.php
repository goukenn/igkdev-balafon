<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexTreatCapture.php
// @date: 20241106 10:33:55
namespace IGK\System\Text;
use Closure;
use Exception;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexTreatCapture{
    // + | --------------------------------------------------------------------
    // + | private members
    // + |
    private $m_source_value;
    private $m_captures;
    private $m_treat_capture;
    private $m_offset;
    private $m_info;
    /**
     * object treat listener
     * @var mixed
     */
    var $treatListener;
    const MARK_KEY = '\0:mark';
    const REGEX_FLAG = PREG_OFFSET_CAPTURE;
    protected function __construct(string $source_value, int $offset, $captures, $treat_capture)
    {    
        $this->m_source_value = $source_value;
        $this->m_captures = $captures; 
        $this->m_treat_capture = $treat_capture; 
        $this->m_offset = $offset;
        $this->m_info = self::OrderCaptures($this->m_captures);
    }
    /**
     * treat capture 
     * @param array $capture 
     * @return void 
     */
    public function setRegexCaptures(array $captures){
        list($this->m_source_value, $this->m_offset) = array_shift($captures);
        $this->m_captures = $captures;
        $this->m_info = self::OrderCaptures($captures);
    }
    /**
     * treat source capture 
     * @return string 
     */
    public function treat($listener=null){
        $listener = $listener ?? $this->treatListener;
        return self::TreatCapture($this->m_source_value, $this->m_offset, $this->m_info, $this->m_treat_capture, $listener);
    }
    /**
     * order regex captures
     * @param array $captures 
     * @return array<int|string, object> 
     */
    public static function OrderCaptures(array $captures){
        $get_parent = function ($cap_info, $inf) {
            while (count($cap_info) > 0) {
                $q = array_pop($cap_info);
                if (($q->pos <= $inf->pos) && ($q->to >= $inf->to)) {
                    return $q;
                }
            }
            return null;
        };
        $cap_info = [];
        $k_index = 1;
        $v_lkname = null;
        while (count($captures) > 0) {
            $kl = key($captures);
            $q = array_shift($captures);
            if (!is_int($kl)) {
                $v_lkname = $kl;
                continue;
            }
            $pos = $q[1];
            $to = $pos + strlen($q[0]);
            $inf = RegexCaptureInfo::CreateFrom([
                'parent' => null,
                'index' => $k_index,
                'pos' => $pos,
                'to' => $to,
                'value' => $q[0], // original value
                'data' => $q[0],  // data to replace
                'childs' => [],
                self::MARK_KEY => 0,
            ]);
            if ($cap_info) {
                $tc = $get_parent($cap_info, $inf); //count($cap_info)>
                $inf->parent = $tc;
                if ($tc) {
                    $tc->childs[] = $inf;
                }
            }
            $cap_info[$k_index] = $inf;
            if ($v_lkname) {
                $cap_info[$v_lkname] = &$cap_info[$k_index];
                $v_lkname = null;
            }
            $k_index++;
        }
        return $cap_info;
    }
    /**
     * create from regex with offset result - missing offset
     * @param null|array<int,string> $tab regex result
     * @param null|array<int|string, string|callable>  $treat_capture 
     * @return null|static 
     */
    public static function CreateFromRegexResult(?array $tab, array $treat_capture){
        if (is_null($tab) || !$tab){
            return null;
        }
        $v = array_shift($tab);
        /**
         * 
         */
        $c = new static($v[0],$v[1], $tab, $treat_capture);
        return $c;
    }
    /**
     * Treat capture
     * @param string source value
     * @param array<string|int, IRegexCaptureInfo> $capture_info
     * @param array<string|int, array> $capture
     * @return mixed 
     * @throws Exception 
     */
    public static function TreatCapture(string $source_value, int $offset, array $capture_info, $capture, $callable){
        $mark_key = self::MARK_KEY;
        $v_output = '';
        $v_toutput = [];
        $v_handled = [];
        $__handle_capture = function ($v, $cap, $callable)use(& $v_handled) {
            if (!$callable){
                $callable = function($cap, $option){
                    // handle - with option 
                };
            }
            $data = null;
            if (is_string($v)) {
                $data = $callable($cap, (object)['name' => $v]);
            } else if ($v instanceof Closure) {
                $data = $v($cap, $callable);
            } else if (is_object($v)){
                list($name, $patterns) = igk_extract($v, 'name|patterns');
                if ($patterns){
                    if ($callable instanceof IRegexCapturePatternListener){
                        $cap = $callable->treatPattern($cap, $patterns);
                    }
                    else {
                        $data = $callable($cap, (object)['name'=>$name ?? $cap->tokenID]);
                    } 
                }
            }
            if (is_string($data)){
                $cap->data = $data;
            }
            $v_handled [ $cap->index] = $cap;
        };
        $__treat_output = function (string $source_value, array $list, int $offset=0) {
            $v_output = '';
            usort($list, function ($a, $b) {
                return  $a->pos <=> $b->pos;
            });
            $v_lpos = 0;
            $v = $source_value;
            while (count($list)) {
                $q = array_shift($list);
                if ($q->pos == -1){
                    continue;
                }
                $pos = ($q->pos-$offset);
                $to = ($q->to -$offset);
                $v_output .= substr($v, $v_lpos, $pos - $v_lpos) . $q->value;
                $v_lpos = $to;
            }
            $v_output .= substr($v, $v_lpos);
            return $v_output;
        };
        $v_gcapture = null;
        foreach ($capture as $index => $v) {
            if ($index==0){
                // + | ignore capture 0 
                $v_gcapture = $v;
                continue; 
            }
            $v_cap = igk_getv($capture_info, $index);
            if (is_null($v_cap)){
                continue;
            }
            if (!is_int($index) && $v_cap->{$mark_key}){
                if (isset($v_handled[$v_cap->index]))
                {   
                    continue;
                }
                $v_cap->{$mark_key}=0;
                $v_p = $v_cap;
                // reset chain data 
                while($v_p = $v_p->parent){
                    $v_p->{$mark_key}=0;
                    $v_p->data = $v_p->value; 
                }
            }
            $v_tcap = [$v_cap];
            while (count($v_tcap) > 0) {
                $cap = array_shift($v_tcap);
                if (!$cap->{$mark_key}) {
                    // copy childrens tab
                    $c = $cap->childs;
                    $v_tcouput = null;
                    while (count($c) > 0) {
                        if (is_null($v_tcouput)) {
                            $v_tcouput = [];
                        }
                        $q = array_shift($c);
                        if ($q instanceof RegexCaptureMarker) {
                            list($q, $list) = igk_extract($q, 'value|list');
                            $v_tcouput = array_merge($list, $v_tcouput);
                            continue;
                        }
                        if ($q->{$mark_key}) {
                            // already marked - update ethe v_tcouput
                            $v_tcouput[] = (object)['pos' => $q->pos, 'to' => $q->to, 'value' => $q->data];
                            continue;
                        }
                        if ($q->childs) {
                            array_unshift($c, new RegexCaptureMarker($q, $v_tcouput));
                            array_unshift($c, ...$q->childs);
                            $v_tcouput = null;
                            continue;
                        }
                        $v_rcap = igk_getv($capture, $q->index);
                        if ($v_rcap) {
                            // handle capture : 
                            $__handle_capture($v_rcap, $q, $callable);
                        }
                        $v_tcouput[] = (object)['pos' => $q->pos, 'to' => $q->to, 'value' => $q->data];
                        $q->{$mark_key} = 1;
                    }
                    if ($v_tcouput) {
                        $cap->data = $__treat_output($cap->value, $v_tcouput, $cap->pos);
                    } else {
                        $__handle_capture($v, $cap, $callable);
                    }
                    $cap->{$mark_key} = 1;
                    if (is_null($cap->parent)) {
                        $v_toutput[] = (object)['pos' => $cap->pos, 'to' => $cap->to, 'value' => $cap->data]; // v_toutputsubstr($source_value, 0, $cap->pos). $cap->data. 
                    } else {
                        // + | force passing to parent 
                        $_p = $cap->parent;
                        $_p->data = $_p->value;
                        $_p->{$mark_key} = 0;
                        $v_toutput = [];
                        array_unshift($v_tcap,  $_p );
                    }
                } else if ($cap->parent && !$cap->parent->{$mark_key}){
                    array_unshift($v_tcap, $cap->parent);
                }
            }
        }
        if ($v_toutput) {
            $v_output = $__treat_output($source_value, $v_toutput, $offset);
        } else{
            $v_output = $source_value;
        }
        if ($v_gcapture){
            // + | ---------------------------------------------------------
            // + | update global capture 
            // + | 
            if ($v_gcapture instanceof Closure){
                if(is_string($v_t = $v_gcapture($v_output, $offset))){
                    $v_output = $v_t;
                }
            }
        }
        return $v_output;
    }
}