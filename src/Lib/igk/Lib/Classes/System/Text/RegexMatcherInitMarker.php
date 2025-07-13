<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherInitMarker.php
// @date: 20250706 08:03:36
namespace IGK\System\Text;
/**
 * 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexMatcherInitMarker
{
    var $tokens = [];
    public function __construct() {}
    /**
     * 
     * @param mixed $e 
     * @param ?string $src 
     * @param int $pos 
     * @param callable $fc 
     * @return bool 
     */
    public function mark($e, $src, $pos, callable $fc)
    {
        return $this->init($this->tokens, $e, $src, $pos, $fc);
    }
    /**
     * retrieve identifier 
     * @return string 
     */
    public function identifier()
    {
        $s = [];
        $tg = $this->tokens;
        while (count($tg) > 0) {
            $q = array_pop($tg);
            $s[] = $q->tokenID;
        }
        return implode(',', $s);
    }
    /**
     * 
     * @param mixed &$v_rt 
     * @param mixed $e 
     * @param mixed $src 
     * @param mixed $pos 
     * @param callable({tokenID:string,pos:int})  $fc 
     * @return bool 
     */
    public function init(&$v_rt, $e, $src, $pos, callable $fc)
    {
        $tg = igk_last($v_rt);
        $p = $e->parentInfo;
        $chain = [];
        $pos = $pos ?? $e->to;
        if ($tg) {
            if ($tg->pos == $e->from) {
                return false;
            }
            while ($tg && $p) {
                if ($tg->pos == $e->from) {
                    return false;
                }
                if ($p->pos == $tg->pos) {
                    if ($chain) {
                        while (count($chain) > 0) {
                            $q = array_shift($chain);
                            $v_te = (object)[
                                'tokenID' => $q->match->tokenID,
                                'pos' => $q->pos
                            ];
                            if (!isset($v_rt[$v_te->pos])) {
                                $fc($v_te, $src, $pos);
                                $v_rt[$v_te->pos] = $v_te;
                            }
                        }
                    }
                    break;
                } else {
                    if ($p->pos < $tg->pos) {
                        array_pop($v_rt);
                        if (!($tg = igk_last($v_rt))) {
                            break;
                        }
                        continue;
                    }
                    array_unshift($chain, $p);
                    $p = $p->parent;
                }
            }
        } else {
            while ($p) {
                array_unshift($chain, $p);
                $p = $p->parent;
            }
        }
          // load chain
        while (count($chain) > 0) {
            $q = array_shift($chain);
            $v_te = (object)[
                'tokenID' => $q->match->tokenID,
                'pos' => $q->pos
            ];
            if (!isset($v_rt[$v_te->pos])) {
                $fc($v_te, $src, $pos);
                $v_rt[$v_te->pos] = $v_te;
            }
        }
        // + | load mark
        return !isset($v_rt[$e->from]);
    }
}