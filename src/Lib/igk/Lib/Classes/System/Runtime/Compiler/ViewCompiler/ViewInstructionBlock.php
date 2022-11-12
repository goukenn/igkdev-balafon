<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewInstructionBlock.php
// @date: 20221110 11:07:08
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
class ViewInstructionBlock implements ArrayAccess, IteratorAggregate, Countable
{
    use ArrayAccessSelfTrait;

    private $m_blocks = [];
    private $m_marker = 0;

    var $space_comment = 1;
    var $pattern_marker = "/\/\/\s*\+\s*\|/";

    public function count(): int
    {
        return count($this->m_blocks);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->m_blocks);
    }

    protected function _access_OffsetGet($n)
    {
        return $this->m_blocks[$n];
    }
    protected function _access_OffsetSet($n, $v)
    {
        if ($this->space_comment) {
            if (is_string($v)) {
                $p = trim($this->pattern_marker, $this->pattern_marker[0]);        
                if (preg_match("#^(" . $p . ")$#", trim($v))) {
                    $this->m_marker = 1;
                } else {
                    if ($this->m_marker) {
                        $this->m_blocks[] = "\n";
                        $this->m_marker = false;
                    }
                }
            }
        }
        if (is_null($n)) {
            $this->m_blocks[] = $v;
        } else {
            $this->m_blocks[$n] = $v;
        }
        return $this;
    }
    public function &getBlockReference()
    {
        return $this->m_blocks;
    }
    /**
     * shift block
     * @return mixed 
     */
    public function shift()
    {
        return array_shift($this->m_blocks);
    }
}
