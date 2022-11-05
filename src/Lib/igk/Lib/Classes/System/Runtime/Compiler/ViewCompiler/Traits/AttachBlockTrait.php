<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttachBlockTrait.php
// @date: 20221026 17:34:24
namespace IGK\System\Runtime\Compiler\ViewCompiler\Traits;

use IGK\Helper\Activator;
use IGK\System\Runtime\Compiler\CompilerFlagState;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewReadBlockFlagInfo;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler\ViewCompiler\Traits
 */
trait AttachBlockTrait
{
    protected function  _attacheBlock($block, $options, $id, $value, $flagid =  CompilerFlagState::READ_BLOCK)
    {
        if ($pblock = $this->m_block) {
            if ($pblock->closed()) {
                $pblock = null;
            }
        }
        $block->parent = $pblock;
        $this->m_block = $block;
        $options->flag = $flagid; //  CompilerFlagState::READ_BLOCK;
        $options->flagOptions = Activator::CreateNewInstance(ViewReadBlockFlagInfo::class, [
            "type" => $value,
            "buffer" => &$block->buffer,
            "condition" => $block->requireCondition(),
            "depth" => $options->depth,
            "multicode" => false,
        ]);
        if ($block->isInnerBlock()) {
            $v_last = igk_array_peek_last($this->m_containers);
            if (!$v_last || !$block->childOf($v_last->type)) {
                igk_die("syntax not valid. " .
                    ($v_last ?
                        $block->type . " not a child of " . $v_last->type :
                        "no container found for " . $block->type));
            }
            $v_last->blocks[] = $block;
            $block->parent = $v_last;
        } else {
            if (is_null($block->parent)) {
                $this->instruction_blocks[] = $block;
            } else {
                $block->parent->blocks[] = $block;
            }
            if ($block->isChildContainer()) {
                $this->m_containers[] = $block;
            } else {
                array_pop($this->m_containers);
            }
        }
        $this->_pushBuffer($options, $options->flagOptions->buffer, $options->flag);
    }
}
