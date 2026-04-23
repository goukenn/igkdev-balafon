<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionDependency.php
// @date: 20221102 12:23:23
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayAccess;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewExpressionDependency implements ArrayAccess{
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Property: value.
    * @var mixed
    */
    var $value;
    /**
    * Property: expression.
    * @var mixed
    */
    var $expression;
    /**
    * Property: eval value.
    * @var mixed
    */
    var $evalValue;
    use ArrayAccessSelfTrait;
    /**
    * .ctr
    * @param mixed $value
    * @param mixed $name
    */
    public function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
        $this->expression = '';
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        $this->expression .= "->".escapeshellarg($name);
        return $this;
    }
    /**
    * get string presentation.
    */
    public function __toString()
    {
        $v = $this->value;
        return $v;
    }
    /**
    * Access offset get.
    * @param mixed $n
    */
    public function _access_OffsetGet($n){
        $this->expression .= sprintf("[%s]", escapeshellarg($n));
        return $this;
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arg
    */
    public function __call($name, $arg){
        if (is_object($this->value)){
            if (($this->value instanceof HtmlNode ) || method_exists($this->value, $name))
                return call_user_func([$this->value, $name], $arg );
        }
        return null;
    }
}