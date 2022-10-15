<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionSetter.php
// @date: 20221015 12:26:05
namespace IGK\System\Runtime\Compiler;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* var to handle expression var expression setter
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionSetter implements ArrayAccess{
    private $m_vars = [];
    private $m_variables;
    private $m_update =false;
    use ArrayAccessSelfTrait;

    public function __construct(& $variables)
    {
        $this->m_variables = & $variables;
    }
    
    protected function _access_OffsetSet($n, $v){
        $c = new ViewExpressionSetterValueInfo($v, function(){
            $this->m_update = true;
        });        
        $this->m_vars[$n] = $c;
        $this->m_variables[$n] = $v;
        $this->m_update = true;
        // igk_wln_e(__FILE__.":".__LINE__,  $this, $this->m_variables);
    }
    protected function _access_OffsetGet($n){
        if ($v = igk_getv($this->m_vars, $n)){
            return $v->getValue();
        }
    }
    /**
     * check if the setter contains value
     * @param mixed $name 
     * @return bool 
     */
    public function contains($name): bool{
        return key_exists($name, $this->m_vars);
    }
    public function getIsUpdate(){
        return $this->m_update;
    }
    public function resetUpdate(){
        $this->m_update = false;
    }
}