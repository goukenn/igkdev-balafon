<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterSplitterTrait.php
// @date: 20250730 12:55:17
namespace IGK\System\Text\Formatters\Traits;

use Closure;
use Exception;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Text\IReplaceCapturedFormatDefinition;
use IGK\System\Text\RegexMatcherCapture;

/**
 * splitter formatter definition 
 * @package IGK\System\Text\Formatters\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait FormatterSplitterTrait
{

    /**
    * Property: splitter join.
    * @var mixed
    */
    private $m_splitter_join;

    /**
    * Property: split node.
    * @var mixed
    */
    private $m_split_node;

    /**
    * Property: marked.
    * @var mixed
    */
    protected $m_marked;

    /**
    * Outputs.
    * @param null|string $source
    * @return string
    */
    public function output(?string $source = null): string
    {
        $s = $this->_treatOuput(parent::output($source));
        return $s;
    }

    /**
    * Treat ouput.
    * @param string $o
    */
    protected function _treatOuput(string $o)
    {
        if ($this->m_split_node) {
            $o = HtmlRenderer::Encapsulate($this->m_split_node, $o);
        }
        return $o;
    }

    /**
    * auto generate doc.
    * @param string $v
    * @return string
    */

    protected function willFormatContentBeforePrefixTabStop(string $v): string
    {
        return ltrim($v);
    }

    /**
    * auto generate doc.
    * @param array $args
    * @return mixed
    */

    protected function _dispatch($e, string $fname, array $args)
    {
        return call_user_func_array([$e, $fname], $args);
    }
    /**
     * treat node chain return string or splitted array definition 
     * @param mixed $e 
     * @param mixed $chains 
     * @param mixed $chainCallback on chain  
     * @return string[]|string 
     */

    protected function _treatChains(RegexMatcherCapture $e, $chains, ?callable $chainCallback = null)
    {
        $n = $cp = null;
        if ($engine = $this->m_host_engine) {
            $cp = $this->_dispatch($engine, __FUNCTION__, func_get_args());
            if (is_array($cp)) {
                return $cp;
            }
        }
        $v_marked = &$this->m_marked;
        $offset = 0;
        $v = $e->value;
        $n = '';
        $dt = [];
        $skipline = false;
        // UPDATE: skip next line before join the content flag
        $v_skipNextSplitLine = false;

        while (count($chains)) {
            $r = array_shift($chains);
            //$v_skipNextSplitLine = $this->getFlag('line-flag');
            if ($chainCallback) {
                $chainCallback($r);
            }
            if ($before = substr($v, $offset, $r->from - $e->from - $offset)) {
                $n .= $before;
            }
            if (isset($v_marked[$r->from])) {
                // append to buffer node 
                $n .= $r->value;
                $skipline = false;
            } else {
                $sp = $r->match->splitLine;
                if ($sp) {
                    // if ($v_skipNextSplitLine) {
                    //     $this->unsetFlag('line-flag'); 
                    // }
                    //if (!empty($n)){
                    $dt[] = !empty($n) ? $n : '';
                    $skipline = true;
                    //}
                    if (isset($r->match->useReplaceData)) {
                        // split line an replace with
                        $n = $r->value;
                        $skipline = false;
                    } else
                        $n = '';
                    $v_skipNextSplitLine = true;
                } else {
                    $n .= $r->value;
                }
            }
            if ($r->match->flags) {
                $this->updateFlags($r->match->flags);
            }
            $offset = $r->to - $e->from; 
        }
        $n .= substr($v, $offset);
        if ($dt) {
            if (!empty($n)) {
                if ($v_skipNextSplitLine) {
                    $dt[count($dt)-1] .= $n;
                } else
                    $dt[] = $n;
            } else {
                if ($skipline) {
                    $dt[] = '';
                }
            }
            $this->formatSplittedList($e, $dt);
            // marked splitter content 
            return $dt;
        }
        if ($cp && ($cp != $n)) {
            igk_debug_wln_e(__FILE__ . ":" . __LINE__, '-----------------------', 'cp', $cp, 'cn', $n ?? '');
        }
        return $n;
    }

    /**
    * auto generate doc.
    * @param mixed &$list
    * @return void
    */

    protected function formatSplittedList(RegexMatcherCapture $e, array &$list)
    {
        $dt = &$list;
        // auto format block definition
        if ($e->match->isBlock) {
            if ($scap = $e->beginCaptures[0][0]) {
                $start = $dt[0];
                if (igk_str_startwith($start, $scap)) {
                    if (!empty($l = igk_str_rm_start($start, $scap, 1))) {
                        $dt[0] = $l;
                    } else {
                        array_shift($dt);
                    }
                    array_unshift($dt, $scap);
                }
            }
            if ($e->endCaptures && ($ecap = $e->endCaptures[0][0])) {
                $end = $dt[count($dt) - 1];
                if (igk_str_endwith($end, $ecap)) {
                    $l = igk_str_rm_last($end, $ecap, 1);
                    array_pop($dt);
                    array_push($dt, $l);
                    array_push($dt, $ecap);
                }
            }
        }
    }
    /**
     * treat current flags before sample
     * @param IReplaceCapturedFormatDefinition $e 
     * @return void 
     * @throws Exception 
     */

    protected function _treatFlags(IReplaceCapturedFormatDefinition $e)
    {
        if (!$flags = $e->match->flags) {
            return;
        }
        $flags = (array)$flags;
        if (($j = igk_getv($flags, 'no-tab')) || ($j = in_array('no-tab', $flags))) {
            $s = true;
            if ($j instanceof Closure) {
                $s = $j($e, $this);
            }
            $this->setFlag('no-tab', $s);
        }
    }
    /**
     * transform must return a string
     * @param IReplaceCapturedFormatDefinition $e 
     * @return string 
     */

    public function transform(IReplaceCapturedFormatDefinition $e): string
    {
        $tv = $e->value;


        if (!is_array($tv)) {
            $tv = [$tv];
        }
        $out = [];
        $js = count($tv) > 1 ? $this->splitterJoin(false) : '';
        $jbloc = $e->closeBlock;
        $cn = null;
        $_will_prefix = false;
        // save state 
        $_bflag = $this->getFlag('no-tab');
        $this->_treatFlags($e);
        while (($tc = count($tv)) > 0) {
            $c = array_shift($tv);
            if (is_null($c)) {
                continue;
            }
            if ($_will_prefix) {
                // + | format adjusted before prefix with tab stop
                $c = $this->willFormatContentBeforePrefixTabStop($c);
            }
            $e->value = $c;
            $r = parent::transform($e);
            if ($jbloc && ($tc == 1) && (($cdepth = $this->depth) > 0)) {
                $this->depth = max(0, $cdepth - 1);
                $cn = $this->splitterJoin(true);
                $this->depth = $cdepth;
            } else if ($js === true) {
                $cn = $this->splitterJoin(true);
            }
            $out[] = $cn . $r;
            $cn = $js;
            if ($js) {
                $js = true;
            }
            $_will_prefix = true;
        }
        // + | restaure state
        $this->setFlag('no-tab', $_bflag);
        return implode('', $out);
    }
    /**
     * splitter join data definition 
     * @return string 
     * @throws Exception 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws CssParserException 
     */

    public function splitterJoin(): string
    {
        if (is_null($this->m_splitter_join)) {
            $this->m_split_node = $this->createSplitterjoin();
            $this->m_splitter_join = HtmlRenderer::SplitterJoin($this->m_split_node, "\n");
        }
        return $this->m_splitter_join;
    }
}
