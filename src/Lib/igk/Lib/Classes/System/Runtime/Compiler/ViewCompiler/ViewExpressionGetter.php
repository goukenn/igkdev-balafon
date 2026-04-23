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

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionGetter extends ViewExpressionBase{
    /**
    * Listener: listener.
    * @var mixed
    */
    var $listener;
    /**
    * .ctr
    * @param mixed & $variables
    * @param mixed $listener
    */
    public function __construct(& $variables, $listener){
        parent::__construct($variables);
        $this->listener = $listener;
    }
    /**
    * Eval.
    * @param mixed $src
    * @param mixed $args
    */
    public function eval($src, $args){
        $src = "return ".rtrim($src,';').";";
        return call_user_func_array($this->listener, func_get_args());
    }
    /**
    * Access offset get.
    * @param mixed $name
    */
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
            $p = null;
            if($name->dependOn){
                foreach($name->dependOn as $k) {
                    $args[$k] = new ViewExpressionDependency(
                        ViewExpressionArgHelper::GetVar($k),
                        $k
                    );
                };
                try{
                    $p = $this->eval($name->source, $args);
                }catch (\Error $ex){
                    // + | error raise because resolution of var failed.
                }
            }
            $name->value = $p;
            return $name; 
        }
        return null; 
    }
    /**
    * Access offset set.
    * @param mixed $name
    * @param mixed $value
    */
    protected function _access_OffsetSet($name, $value){
        throw new OperationNotAllowedException("Expression Getter can't set value");
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        return $this->getValue($name);
    }
    /**
     * get value
     * @param mixed $name 
     * @return mixed 
     * @throws IGKException 
     */
    public function getValue($name){
        $p = ViewExpressionArgHelper::GetVar($name);
        return $p;
    }
    /**
    * get string presentation.
    */
    public function __toString()
    {
        return 'getter:::';
    }
}