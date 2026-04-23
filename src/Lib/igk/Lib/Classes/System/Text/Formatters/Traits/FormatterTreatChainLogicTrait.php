<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterTreatChainLogicTrait.php
// @date: 20250807 11:20:18
namespace IGK\System\Text\Formatters\Traits;
use IGK\Helper\StringUtility;
use IGK\System\Text\IReplaceCapturedFormatDefinition;

/**
 * 
 * @package IGK\System\Text\Formatters\Traits
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\System\Text\Formatters\Traits
*/
trait FormatterTreatChainLogicTrait
{
    /**
    * Property: chain logic.
    * @var mixed
    */
    protected $m_chain_logic = [];
    /**
    * auto generate doc.
    * @param IReplaceCapturedFormatDefinition $e
    * @return void
    */
    protected function beforeFormat(IReplaceCapturedFormatDefinition $e)
    {
        $p = null;
        $chains_logic = &$this->m_chain_logic;
        $q = $e;
        $engine = $this;
        $curr_info = $e->info;
        $last = igk_last($chains_logic);
        $closing = $curr_info && ($last && ($curr_info === $last));
        if (($p = $curr_info) ||  ($closing && ($p = $curr_info)) || (($p = $q->parentInfo) && ((!$chains_logic) || ($e->info !== $chains_logic[0])))
        ) {
            $v_detected = [];
            while ($p) {
                if (($tc = count($chains_logic)) > 0) {
                    if (($p === $chains_logic[$tc - 1]) || ($same_pos = ($chains_logic[$tc - 1]->pos == $p->pos))) {
                        break;
                    }
                }
                array_unshift($v_detected, $p);
                $m = $p->match;
                $cid = igk_extract_first(igk_getv($m->beginCaptures, 0), 'name|tokenID') ?? $m->tokenID;
                if ($m->isBlock) {
                    $this->incDepth();
                }
                if ($cid) {
                    if (method_exists($engine, $fc = '_init_chain_' . StringUtility::FuncName($cid))) {
                        call_user_func_array([$engine, $fc], [$p, 'start']);
                    }
                }
                $p = $p->parent;
            }
            if ($v_detected)
                $chains_logic = array_merge($chains_logic, $v_detected);
        }
    }
    /**
     * after format to chain logic handle
     * @param mixed $e 
     * @return void 
     * @throws Exception 
     */
    protected function afterFormat(IReplaceCapturedFormatDefinition $e)
    {
        $chains_logic = &$this->m_chain_logic;
        $engine = $this;
        $p = $e->info;
        if ($p) {
            if ($p === igk_last($chains_logic)) {
                $m = $p->match;
                $cid = igk_extract_first(igk_getv($m->endCaptures, 0), 'name|tokenID') ?? $m->tokenID;
                if ($m->isBlock) {
                    $this->decDepth();
                }
                if ($cid) {
                    if (method_exists($engine, $fc = '_init_chain_' . StringUtility::FuncName($cid))) {
                        call_user_func_array([$engine, $fc], [$p, 'close']);
                    }
                }
                array_pop($chains_logic);
            }
        }
    }
}