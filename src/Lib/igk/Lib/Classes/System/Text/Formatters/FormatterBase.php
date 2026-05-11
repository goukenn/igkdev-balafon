<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterBase.php
// @date: 20250730 09:52:59
namespace IGK\System\Text\Formatters;
use Closure;
use Error;
use Exception;
use IGK\Helper\StringUtility;
use IGK\System\Console\Logger;
use IGK\System\Core\Traits\SystemStateFlagTrait;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\IO\StringBuilder;
use IGK\System\Text\IReplaceCapturedFormatDefinition;
use IGK\System\Text\RegexMatcherCapture;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\Traits\ReplaceUtilityTrait;
use IGKException;
use IGKObject;
use ReflectionException;

/**
 * formatter 
 * @package IGK\System\Text\Formatters
 * @author C.A.D. BONDJE DOUE
 */
abstract class FormatterBase extends IGKObject
{
    use ReplaceUtilityTrait;
    use SystemStateFlagTrait;
    /**
    * Listener: visitor listener.
    * @var mixed
    */
    var $visitorListener;
    /**
     * activate eof
     * @var ?bool
     */
    var $eof;
    /**
     * render output with splitter
     * @var mixed
     */
    var $lineSplitter;
    /**
    * Property: host engine.
    * @var mixed
    */
    protected $m_host_engine;
    /**
    * Property: parent engine.
    * @var mixed
    */
    protected $m_parent_engine;
    /**
    * Property: sb.
    * @var mixed
    */
    protected $m_sb;
    /**
    * Property: depth.
    * @var mixed
    */
    protected $m_depth = 0;
    /**
     * sub list definition 
     */
    protected $m_sub;
    /**
    * auto generate doc.
    * @var int
    */
    protected $m_offset = 0;
    /**
     * store the transform object 
     * @var mixed
     */
    protected $m_transform;
    /**
     * hosted engine
     * @return null|mixed 
     */
    public function hostEngine()
    {
        return $this->m_host_engine;
    }
    /**
     * get depth
     * @return int 
     */
    public function getDepth()
    {
        return $this->m_depth;
    }
    /**
     * modify the deep
     * @param int $depth 
     * @return void 
     */
    protected function setDepth(int $depth)
    {
        $this->m_depth = $depth;
    }
    /**
    * .ctr
    */
    function __construct()
    {
        $this->m_sb = new StringBuilder;
        $this->m_sub = [];
    }
    /**
    * Returns Formatter Engine.
    * @param mixed $m
    */
    function getFormatterEngine($m)
    {
        if ($p = $m->getMatcher()->enginePatternListener) {
            return $p();
        }
        return null;
    }
    /**
    * auto generate doc.
    * @param mixed $regex
    * @param string $src
    * @param bool $useSource
    * @param array $patterns initialize patterns
    * @return ?string
    */
    public function exec($regex, string $src, bool $useSource = false, ?array $patterns = null)
    {
        $e = null;
        $pos = 0;
        $engine = $this;
        $engine_src = $useSource ? $src : null;
        $patterns && $regex->setInitialPatterns($patterns);
        $v_size = strlen($src);
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $ne = $engine;
                $this->m_host_engine = null;
                if ($n_engine = $this->getFormatterEngine($e->match)) {
                    $n_engine->updateDefinitionFrom($ne);
                    $this->m_host_engine = $n_engine;
                }
                $ne->eof = $pos >= $v_size;
                $ne->format($e, $engine_src);
            }
        } 
        $regex->setInitialPatterns(null);
        if (!$this->m_sub_chain && $engine->m_depth) {
            igk_wln_e(__FILE__ . ":" . __LINE__, 'not properly closed. depth #' . $engine->m_depth);
        }
        if ($e && is_null($e->endCaptures) && ($pos == $e->to)) {
            if ((!empty(trim($e->value)))
                && ($e->from != $pos) && !($e->match->eof)
            ) {
                Logger::info('---missing close capture---');
            }
            $engine->m_depth = 0;
        }
        if (!$this->m_sub_chain) {
            $this->bind = [];
        }
        return $engine->output($engine_src);
    }
    /**
     * update parent definition from 
     * @param mixed $parentEngine 
     * @return void 
     */
    protected function updateDefinitionFrom($parentEngine)
    {
        $this->m_sub = &$parentEngine->m_sub;
        $this->m_offset = &$parentEngine->m_offset;
        $this->m_depth = &$parentEngine->m_depth;
        $this->m_sb = $parentEngine->m_sb;
        $this->tabStop = $parentEngine->tabStop;
        $this->m_parent_engine = $parentEngine;
    }
    /**
     * treat with visitor logic
     * @param string $tid 
     * @param string $value 
     * @param mixed $e 
     * @param array $v_def 
     * @return mixed|void 
     */
    protected function _treat(string $tid, string $value, $e, $v_def = [])
    {
        $m = null;
        if ($this->visitorListener && method_exists($this->visitorListener, $fc = 'visit_' . StringUtility::FuncName($tid))) {
            $m = call_user_func_array([$this->visitorListener, $fc], [$value, $e, $v_def]);
        }
        if (is_null($m) && method_exists($this, $fc = '_visit_' . StringUtility::FuncName($tid))) {
            $m = call_user_func_array([$this, $fc], [$value, $e, $v_def]);
        }
        if (!is_null($m)) {
            return $m;
        }
    }
    /**
    * Chain transfrom.
    * @param RegexMatcherContainer $regex
    * @param array $patterns
    * @param string $v
    */
    public abstract function chainTransfrom(RegexMatcherContainer $regex, array $patterns, string $v);
    /**
     * create capture listener foreach capture items 
     * @param mixed $formatter 
     * @return Closure(string $v, mixed $cap, string $source, int $pos, string $type): mixed 
     */
    public static function CreateTreatmentListener($formatter)
    {
        return function (string $v, $cap, string $source, int $pos, string $type) use ($formatter) {
            $q = $formatter;
            $cl = null;
            $e = $q->m_transform;
            extract(igk_extract_var($cap, 'tokenID|name|patterns|class|replaceWith'));
            $tid = $tokenID ?? $name;
            $vsign = false;
            $vsource = $v;
            if (is_array($patterns) && $patterns) {
                $m = $e->match->getMatcher();
                $v = $q->chainTransfrom($m, $patterns, $v);
                $q->m_transform = $e;
                $vsign = true;
            }
            if ($class) {
                $cl = ' ' . (is_array($cl) ? implode(' ', $cl) : $cl);
            }
            if ($replaceWith) {
                if (is_string($replaceWith))
                    $v = static::ReplaceCaptureData($v, '/(.+)/', $replaceWith);
                else if ($replaceWith instanceof Closure) {
                    $v = $replaceWith($v);
                } else if (is_array($replaceWith)) {
                    foreach ($replaceWith as $rk => $rv) {
                        $v = str_replace($rk, $rv, $v);
                    }
                }
            }
            if (!$vsign || ($v == $vsource))
                $v = $formatter->transformCapture($v);
            $m = ($tid ? $q->_treat($tid, $v, $e, []) : null) ?? ($tid ? $q->_fallbackReplace($tid . $cl, $v) : null) ?? $v;
            return $m;
        };
    }
    /**
    * Transform capture.
    * @param string $v
    */
    function transformCapture(string $v)
    {
        return $v;
    }
    /**
    * auto generate doc.
    * @param ?IReplaceCapturedFormatDefinition $e
    * @return string
    */
    public function transform(IReplaceCapturedFormatDefinition $e): string
    {
        $v_def = [];
        $v_matcher = $e->match->getMatcher();
        // + | set opbject to transform 
        $this->m_transform = $e;
        if (is_null($v_matcher->captureTreatmentListener)) {
            $v_matcher->captureTreatmentListener = self::CreateTreatmentListener($this);
        }
        if ($l = self::ResolveCapture($e, $v_def)) {
            $e->value = $l;
        } else if ($v_def) {
            $v_def = $this->_treatResolveCaptureLogic($e, $v_def);
            $e->value = self::UpdateCaptureDef($e, $v_def, $e->value, function ($e, $s) {
                if (!$e->getHasSubChildren()) {
                    if (!($pr = $e->match->preserveContent)) {
                        if (strlen(trim($s)) > 0) {
                            $s = $this->_fallbackReplace('text', $s);
                        }
                    } else {
                        if (is_string($pr) && ($pr = $this->getPreserveCallback($pr))) {
                            $s = $pr($s);
                        }
                    }
                } else if (!$e->isDirty && (strlen(trim($s)) == 0)) {
                    $s = '';
                }
                return $s;
            });
        }
        $r = $this->_treatFormatLogic($e, $this->_beforeTreatFormatLogic($e, $v_def));
        $this->m_transform = null;
        return $r;
    }
    /**
    * auto generate doc.
    * @param string $cname
    * @return ?callable
    */
    protected function getPreserveCallback(string $cname)
    {
        if (in_array($cname, ['trim', 'rtrim', 'ltrim'])) {
            return $cname;
        }
    }
    /**
    * auto generate doc.
    * @param IReplaceCapturedFormatDefinition $e
    * @param array $v_def
    * @return array
    */
    protected function _treatResolveCaptureLogic(IReplaceCapturedFormatDefinition $e, array $v_def)
    {
        return $v_def;
    }
    /**
    * auto generate doc.
    * @param mixed $e
    * @param mixed $v
    * @return mixed
    */
    protected function _treatFormatLogic($e, $v)
    {
        return $v;
    }
    /**
    * auto generate doc.
    * @param mixed $e
    * @param mixed $v_def
    * @return mixed
    */
    protected function _beforeTreatFormatLogic($e, $v_def)
    {
        $tid = $e->tokenID;
        $s = $e->value;
        if ($tid) {
            $tid = explode(' ', $tid)[0];
            if ($m = $this->_treat($tid, $s, $e, $v_def)) {
                return $m;
            }
        }
        $g = self::ReplaceData($s, $e);
        if ($e->match->offScreen) {
            return $g;
        }
        $g = $this->_fallbackReplace($e->tokenID ?? 'preserve', $g);
        return $g;
    }
    /**
    * auto generate doc.
    * @param string $tid
    * @param string $value
    * @return string
    */
    protected function _fallbackReplace(string $tid, string $value): string
    {
        $g = $value;
        $tl = explode(' ', igk_str_rm_start($tid, 'f-'));
        $i = [];
        while (count($tl) > 0) {
            $q = array_shift($tl);
            $cl = igk_css_str2class_name($q);
            $cl = igk_getv([
                'reserved_operand' => 'rp',
            ], $cl, $cl);
            $i[] = $cl;
        }
        $cl = implode('.', array_unique($i));
        $g = '' . igk_html_host('span.' . $cl, $g);
        return $g;
    }
    /**
    * Returns Transform Obj.
    * @param RegexMatcherCapture $e
    * @return IReplaceCapturedFormatDefinition
    */
    abstract function getTransformObj(RegexMatcherCapture $e): IReplaceCapturedFormatDefinition;
    /**
    * Did treat chain listener.
    * @param IReplaceCapturedFormatDefinition $ce
    */
    protected function didTreatChainListener(IReplaceCapturedFormatDefinition $ce) {}
    /**
     * override this to handle before treat chains
     * @param RegexMatcherCapture $chain 
     * @param IReplaceCapturedFormatDefinition $ce 
     * @return void 
     */
    protected function willTreatChainListener(RegexMatcherCapture $chain, IReplaceCapturedFormatDefinition $ce)
    {
        $ce->isDirty != (strlen(trim($ce->value)) > 0);
    }
    /**
    * format code with
    * @param mixed $e
    * @param ?string $source
    * @throws Error
    * @return void
    */
    public function format(RegexMatcherCapture $e, ?string $source = null)
    {
        $tid = $e->tokenID;
        $sub = &$this->m_sub;
        igk_is_debug() && Logger::info('tokenid:' . $tid . ' from ' . $e->from . ' [' . json_encode($e->value) . ']');
        $chains = [];
        $offset = $this->m_offset ?? 0;
        if ($sub) {
            // + | check of sub children - passing to captured chain
            // + | NOTE: some element can have sub child that start at the same location
            // + | need to pass it to sub chain logic
            while (count($sub) && ($e->from <= $sub[0]->from)) {
                $q = array_shift($sub);
                array_unshift($chains, $q);
            }
        }
        $ce = $this->getTransformObj($e);
        $ce->chains = $chains;
        $ce->isDirty = false;
        if ($chains) {
            $e->value =
                $this->_treatChains($e, $chains, function ($chain) use ($ce) {
                    call_user_func_array([$this, 'willTreatChainListener'], [$chain, $ce]);
                });
            $this->didTreatChainListener($ce);
        } else {
            $s = $e->value;
            if (($e->parentInfo) && ($e->match->matchSplitOnParent)) {
                $e->value = ['', $s];
            } else if ((!$e->parentInfo) && ($e->match->matchLineFeed)) {
                $e->value = [$s, ''];
            } else {
                if ($e->match->preserveContent && (false !== strpos($s, "\n"))) {
                    $e->value = explode("\n", $s);
                }
            }
        }
        $e->value = ($e->match->ignoreOnEOF && $this->eof) ? '': $this->transform($ce);
        if ($e->parentInfo) {
            array_unshift($sub, $e);
        } else {
            $prev = $this->getFlag('prev'); 
            $skipped = $this->getFlag('skipped'); 
            $line_flag = $this->getFlag('line-flag'); 
            if ($source) {
                $before = substr($source, $offset, $e->from - $offset);
                if (strlen($before) > 0) {
                    if (empty(trim($before))) {
                        $before = ' ';
                    }
                    if (($before != ' ') || !$skipped) {
                        $before = $this->_treatBefore($before);
                        $this->m_sb->append($before);
                    }
                }
            }
            if ($sp = $e->match->splitLine) {
                if (is_string($sp) && ($sp == 'after')) {
                    $this->m_sb->append($e->value);
                    $this->m_sb->append($this->splitterJoin(true));
                } else {
                    if (!$prev) {
                        $this->m_sb->append($this->splitterJoin(true));
                    } else {
                        $this->m_sb->rtrim();
                        if (!$this->eof){
                            $this->m_sb->append($this->splitterJoin(true));
                        }
                        $this->setFlag('skipped', 1);
                    }
                }
            } else {
                $this->m_sb->append($e->value);
                $this->unsetFlag('skipped');
            }
            $this->m_offset = $e->to;
            $this->setFlag('prev', $e);
        }
    }
    /**
    * Splitter join.
    * @return string
    */
    abstract function splitterJoin(): string;
    /**
     * flags on data 
     * @param null|array $flags 
     * @param string $id 
     * @return bool 
     */
    public static function ResolveFlag(?array $flags, string $id): bool
    {
        if (!$flags)
            return false;
        return in_array($id, $flags);
    }
    /**
    * FormatterBase Treat Chains .
    * @param mixed $e
    * @param mixed $chains
    * @param ?callable $willTreatChainListener
    * @return string|string[]
    */
    protected function _treatChains(RegexMatcherCapture $e, $chains, ?callable $willTreatChainListener = null)
    {
        $offset = 0;
        $v = $e->value;
        $n = '';
        while (count($chains)) {
            $r = array_shift($chains);
            if ($willTreatChainListener) {
                $willTreatChainListener($r);
            }
            $n .= substr($v, $offset, $r->from - $e->from - $offset) . $r->value;
            $offset = $r->to - $e->from;
        }
        $n .= substr($v, $offset);
        return $n;
    }
    /**
    * auto generate doc.
    * @param ?string $source
    * @return string
    */
    public function output(?string $source = null): string
    {
        if ($source) {
            $before = substr($source, $this->m_offset);
            if (!empty(trim($before))) {
                $before = $this->_treatBefore($before);
                $this->m_sb->append($before);
            }
        }
        $s = $this->m_sb . '';
        return ltrim($s);
    }
    /**
    * Treat before.
    * @param string $before
    */
    protected function _treatBefore(string $before)
    {
        if ($this->lineSplitter && (count($split = explode($this->lineSplitter, $before)) > 1)) {
            $before = implode($this->splitterJoin(), $split);
        }
        return $before;
    }
    /**
    * Tab.
    */
    public function tab()
    {
        return str_repeat($this->tabStop, $this->m_depth ?? 0);
    }
}