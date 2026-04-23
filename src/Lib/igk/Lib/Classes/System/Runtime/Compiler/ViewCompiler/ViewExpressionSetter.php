<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionSetter.php
// @date: 20221015 12:26:05
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayAccess;
use IGK\System\Html\Dom\HtmlNode;

/**
* var to handle expression var expression setter
* @package IGK\System\Runtime\Compiler
*/
final class ViewExpressionSetter extends ViewExpressionBase implements ArrayAccess{
    /**
    * Property: update.
    * @var mixed
    */
    private $m_update =false;
    /**
    * Property: update express.
    * @var mixed
    */
    private $update_express=null;    
    /**
     * last update variable
     * @var mixed
     */
    private $m_name;
    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    protected function _access_OffsetSet($n, $v){
        $c = new ViewExpressionSetterValueInfo($n, $v, function($n){
            $this->m_update = true;
            $this->m_name = $n;
        });        
        $this->m_vars[$n] = $c;
        $this->m_variables[$n] = $v;
        $this->m_update = true; 
        $this->update_express = "\$$n = ";
        $this->m_name = $n;
        if ($v instanceof ViewExpressionEval)
            $this->update_express .= $v->source.";";
        else 
            $this->update_express .= var_export($v, true).";";
    }
    /**
    * Access offset get.
    * @param mixed $n
    */
    protected function _access_OffsetGet($n){
        if ($v = igk_getv($this->m_vars, $n)){
            return $v->getValue();
        }
        if (key_exists($n, $this->m_variables)){
            $p = $this->m_variables[$n];
            if (is_null($p)){
                igk_die($n." is used but it value is null");
            }
            $this->_access_OffsetSet($n, $p);
            return $p; 
        }
    }
    /**
    * Returns Is Update.
    */
    public function getIsUpdate(){
        return $this->m_update;
    }
    /**
    * Resets Update.
    */
    public function resetUpdate(){
        $this->m_update = false;
        $this->update_express = null;
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if ($this->contains($n))
            return $this->_access_OffsetGet($n);
        return $this->m_variables[$n];
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        $g = $this->__get($name);
        if ($g){
            return $g->name(...$arguments);
        }
    }
    /**
     * get update expression
     * @return null 
     */
    public function getExpression(string $source){
        $n =  $this->m_name;
        $o = igk_getv($this->m_variables, $n);
        if ($o instanceof IViewCompilerArgument){
            return null;
        }
        if ($o instanceof HtmlNode){
            return null;
        }
        $m = ViewTokenizeArgConstants::SETTER_VAR.'[\''.$n."'] = ";
        $source = ltrim(str_replace($m, "", $source));
        return $source;
    }
}