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
/**
 * 
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
class ViewInstructionBlock implements ArrayAccess, IteratorAggregate, Countable
{
    use ArrayAccessSelfTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_blocks = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_marker = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $space_comment = 1;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $pattern_marker = "/\/\/\s*\+\s*\|/";

    /**
    * auto generate doc.
    * @return int
    */
    public function count(): int
    {
        return count($this->m_blocks);
    }

    /**
    * auto generate doc.
    * @return Traversable
    */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->m_blocks);
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    protected function _access_OffsetGet($n)
    {
        return $this->m_blocks[$n];
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
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

    /**
    * auto generate doc.
    */
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