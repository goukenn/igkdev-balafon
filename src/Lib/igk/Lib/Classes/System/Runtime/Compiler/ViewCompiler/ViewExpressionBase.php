<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionBase.php
// @date: 20221015 22:48:55
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
abstract class ViewExpressionBase implements ArrayAccess{
    protected $m_vars = [];
    protected $m_variables;

    use ArrayAccessSelfTrait;

    public function __construct(& $variables)
    {
        $this->m_variables = & $variables;
    }

     /**
     * check if the setter contains value
     * @param mixed $name 
     * @return bool 
     */
    public function contains($name): bool{
        return key_exists($name, $this->m_vars);
    }
}