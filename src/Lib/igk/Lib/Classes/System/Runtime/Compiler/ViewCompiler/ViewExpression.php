<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpression.php
// @date: 20221017 04:01:50
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use ArrayAccess;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ViewExpression implements ArrayAccess{
    private $m_variables;
    var $extract;
    var $callback;
    use ArrayAccessSelfTrait;
    
    public function __construct(& $variables, $callback, $extract=false){
        $this->m_variables = & $variables;
        $this->extract = $extract;
        $this->callback = $callback;
    }
    public function _access_OffsetGet($expression){
        $fc = $this->callback;
        $value = null;
        $restore = false;
        $bck = [];
        $src = $expression;

        // igk_wln_e(__FILE__.":".__LINE__,  "bindf ......".$expression->source);
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


        // depend on some variables
        // $g = $this->m_variables["x"];
        //if ($g instanceof HtmlNode){
            // $this->m_variables["x"] = $g->render();
        //}
        $value = $fc(sprintf("return %s;",$src), (object)["data"=>$this->m_variables]);

        // restore valiables
        // $this->m_variables["x"] = $g;
        if ($restore){
            foreach($bck as $k=>$v){
                $this->m_variables[$k]=$v;
            }
        }
        // get evaluation response 
        $response = igk_getv($this->m_variables, ViewExpressionArgHelper::RESPONSE);

        // igk_debug_wln(__FILE__.":".__LINE__,  "the value : ", $value, $this->m_variables, $response, is_string($response));
        if ($this->extract){
            if ($expression instanceof ViewExpressionEval){
                return $expression;
            }
            if (is_object($response)){
                return $response;
            }
            // depend on 
            return new ViewExpressionEval($expression);            
        }
        return $response;
    }
    public function _access_OffsetSet($expression, $value){
        throw new NotImplementException(__METHOD__);
    }
}