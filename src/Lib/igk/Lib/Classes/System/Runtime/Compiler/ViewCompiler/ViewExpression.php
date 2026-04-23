<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpression.php
// @date: 20221017 04:01:50
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayAccess;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
class ViewExpression implements ArrayAccess{
    /**
    * Property: variables.
    * @var mixed
    */
    private $m_variables;
    /**
    * Property: extract.
    * @var mixed
    */
    var $extract;
    /**
    * Callback handler for callback.
    * @var mixed
    */
    var $callback;
    use ArrayAccessSelfTrait;
    /**
    * .ctr
    * @param mixed & $variables
    * @param mixed $callback
    * @param mixed $extract
    */
    public function __construct(& $variables, $callback, $extract=false){
        $this->m_variables = & $variables;
        $this->extract = $extract;
        $this->callback = $callback;
    }
    /**
    * Access offset get.
    * @param mixed $expression
    */
    public function _access_OffsetGet($expression){
        $fc = $this->callback;
        $value = null;
        $restore = false;
        $bck = [];
        $src = $expression;
        if ($expression instanceof ViewExpressionEval)
        {
            if ($expression->dependOn){
                $restore = true;
                foreach(array_keys($expression->dependOn) as $k){
                    $bck[$k] = ViewExpressionArgHelper::GetVar($k);
                    $g = igk_getv($this->m_variables, $k);
                    if ($g instanceof HtmlNode){
                        $this->m_variables[$k] = $g->render();
                    }
                }
            }
            $src = $expression->source; 
        }
        $value = $fc(sprintf("return %s;",$src), (object)["data"=>$this->m_variables]);
        if ($restore){
            foreach($bck as $k=>$v){
                $this->m_variables[$k]=$v;
            }
        }
        $response = igk_getv($this->m_variables, ViewExpressionArgHelper::RESPONSE);
        if ($this->extract){
            if ($expression instanceof ViewExpressionEval){
                return $expression;
            }
            if (is_object($response)){
                return $response;
            }
            return new ViewExpressionEval($expression);            
        }
        return $response;
    }
    /**
    * Access offset set.
    * @param mixed $expression
    * @param mixed $value
    */
    public function _access_OffsetSet($expression, $value){
        throw new NotImplementException(__METHOD__);
    }
}