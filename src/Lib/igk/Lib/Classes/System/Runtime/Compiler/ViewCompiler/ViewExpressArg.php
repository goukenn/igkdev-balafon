<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressArg.php
// @date: 20221018 10:08:28
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use ArrayIterator;
use IGK\System\Runtime\Compiler\Html\ExpressionArgNode;
use IGK\System\Runtime\Compiler\IViewExpressionArg;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressArg implements IteratorAggregate, IViewExpressionArg{
    var $expression;
    var $value;
    /**
     * extract value
     * @var false
     */
    var $extract = false;
    public function __construct($expression, $value, $extract=false)
    {
        $this->expression = $expression;
        $this->value = $value;        
        $this->extract = $extract;
    }

    public function getExpression() {
        return "$".$this->expression;
    }

    public function getIterator(): Traversable {
        return new ArrayIterator([$this->expression, $this->value]);
     }
    public function __toString(){
        return "<?= ".$this->getExpression()." ?>";
    }

    /**
     * create expression node
     * @return void 
     */
    public function createExpressionNode(){
        return new ExpressionArgNode($this."");  
    }
}