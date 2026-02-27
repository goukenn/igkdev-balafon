<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionBase.php
// @date: 20221015 22:48:55
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
abstract class ViewExpressionBase implements ArrayAccess{

    /**
    * Property: vars.
    * @var mixed
    */
    protected $m_vars = [];

    /**
    * Property: variables.
    * @var mixed
    */
    protected $m_variables;
    use ArrayAccessSelfTrait;

    /**
    * .ctr
    * @param mixed & $variables
    */
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