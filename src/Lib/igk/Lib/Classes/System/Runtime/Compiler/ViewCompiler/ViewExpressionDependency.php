<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionDependency.php
// @date: 20221102 12:23:23
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use ArrayAccess;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewExpressionDependency implements ArrayAccess{
    var $name;
    var $value;
    var $expression;
    var $evalValue;
    use ArrayAccessSelfTrait;

    public function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
        $this->expression = '';
    }
    public function __get($name){
        $this->expression .= "->".escapeshellarg($name);
        return $this;
    }
    public function __toString()
    {
        $v = $this->value;
        return $v;
    }
    public function _access_OffsetGet($n){
        $this->expression .= sprintf("[%s]", escapeshellarg($n));
        return $this;
    }
    public function __call($name, $arg){
        if (is_object($this->value)){
            if (($this->value instanceof HtmlNode ) || method_exists($this->value, $name))
                return call_user_func([$this->value, $name], $arg );
        }
        return null;
    }
}