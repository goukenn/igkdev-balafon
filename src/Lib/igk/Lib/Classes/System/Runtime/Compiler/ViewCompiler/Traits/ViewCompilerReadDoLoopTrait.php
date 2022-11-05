<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerReadDoLoop.php
// @date: 20221026 17:16:03
namespace IGK\System\Runtime\Compiler\ViewCompiler\Traits;

use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Runtime\Compiler\ReadTokenOptions;
use IGK\System\Runtime\Compiler\ViewCompiler\CodeBlock\DoWhileBlock;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompilerConstants;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
trait ViewCompilerReadDoLoopTrait
{
    private $m_do_loop_config;
    /**
     * @param ReadTokenOptions $options 
     * @param null|string $id 
     * @param string $value 
     * @return void 
     */
    protected function _readDoWhileBlock(ReadTokenOptions $options, ?string $id, string $value)
    {
        $this->flagHandler = null; // [$this, '_readHandleDoWhileBlock'];
        if ($id == T_DO) {
            $this->_pushFlag($options);
            $block = new DoWhileBlock();
            $this->_attacheBlock($block, $options, $id, $value, '_readHandleDoWhileBlock');
            $options->flag = '_readHandleDoWhileBlock';
            $options->waitFor = "";
            // $block->blocks[] = "echo 'demos';";
            return;
        }
        if ($id == T_WHILE) {
            $this->_readBlock($options, $id, $value);
        }
    }

    /**
     * @param ReadTokenOptions $options 
     * @param null|string $id 
     * @param string $value 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _readHandleDoWhileBlock(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $fop = $options->flagOptions;
        $v_buffer = &$fop->buffer;
        $v_block = $this->m_block;

        if ($fop->depth == $options->depth) {
            switch ($id) {
                case T_WHILE:
                    // start block again
                    $fop->condition = true;
                    $fop->buffer = ""; // clean condition buffer
                    $this->_readCondition($options, $id, $value);
                    return true;
            }
        }
        if (($value == '{') && !$fop->multicode) { // multicode detection start
            $fop->multicode = true;
            return true;
        }
        if ($fop->condition) {
            if (($value == ')') && ($fop->depth == $options->depth)) {
                $fop->condition = false;
                $fop->out_condition = $v_buffer;
                $fop->condition_read = true;
                $v_buffer = "";
                return true;
            }
        } else if ($fop->condition_read && ($value == ';') && ($fop->depth == $options->depth)) {
            $this->_endHandleDoWhileBlock($options, $id, $value);
            return true;
        }

        if ($fop->multicode || (!$fop->condition && !$fop->condition_read)) {
            switch ($value) {
                case ';':
                    // append instruction to block
                    if (!empty($v_buffer)) {
                        $v_block->blocks[] = trim($v_buffer, ViewCompilerConstants::BLOCK_TRIM_CHAR) . $value;
                    }
                    $v_block->buffer = "";
                    $v_buffer = "";
                    if (!$fop->multicode) {
                        return true;
                    }
                    break;
                default:
                    // if (($fop->multicode) || (!$fop->multicode && (count($v_block->blocks) < 1))) {
                    return false;
                    // }
                    // $fixing multicode instruction by passing to buffer
                    // $v_block->buffer .= $value;
                    // $v_buffer .= $value;
                    break;
            }
        }
        return true;
    }

    protected function _endHandleDoWhileBlock(ReadTokenOptions $options, ?string $id, string $value)
    {
        $fop = $options->flagOptions;
        $this->m_block->condition = $fop->out_condition;
        $this->_endReadBlock($options, $id, $value);
    }
}
