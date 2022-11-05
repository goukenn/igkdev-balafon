<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionGetter.php
// @date: 20221015 22:47:16
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use Exception;
use IGK\Controllers\BaseController;
use IGK\System\Exceptions\OperationNotAllowedException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewGetterExpression;
use IGK\System\ViewExtractArgHelper;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionGetter extends ViewExpressionBase{
    var $listener;
    public function __construct(& $variables, $listener){
        parent::__construct($variables);
        $this->listener = $listener;
    }
    public function eval($src, $args){
        $src = "return ".rtrim($src,';').";";
        return call_user_func_array($this->listener, func_get_args());
    }   
    protected function _access_OffsetGet($name){
        if (is_string($name)){
            $p = ViewExpressionArgHelper::GetVar($name);
            if ($p instanceof HtmlNode)
                return $p;
            if ($p instanceof BaseController)
                return $p;

            return new ViewGetterExpression($name, $p);
        }else if ($name instanceof ViewExpressionEval){
            $name->listener = $this->listener;
            $args = [];
            $p = null;// $this->eval($name->source, $args);
            if($name->dependOn){
                foreach($name->dependOn as $k) {
                    $args[$k] = new ViewExpressionDependency(
                        ViewExpressionArgHelper::GetVar($k),
                        $k
                    );
                };
                // check inf eval return block value
                try{
                    $p = $this->eval($name->source, $args);
                }catch (\Error $ex){
                    // + | error raise because resolution of var failed.
                }
            }
            $name->value = $p;
            return $name; 
        }
        // igk_debug_wln(__FILE__.":".__LINE__,  "bind getter ..... $name ", $p);
        return null; // $p;
        // for concating
        // $args = new ViewExtractArgHelper($name, $p);
        // return $args;
    }

    protected function _access_OffsetSet($name, $value){
        throw new OperationNotAllowedException("Expression Getter can't set value");
    }

    public function __get($name){
        // for real value
        return $this->getValue($name);
    }
    /**
     * get value
     * @param mixed $name 
     * @return mixed 
     * @throws IGKException 
     */
    public function getValue($name){
        // for real value
        $p = ViewExpressionArgHelper::GetVar($name);
        return $p;
    }
    
    public function __toString()
    {
        return 'getter:::';
    }
}