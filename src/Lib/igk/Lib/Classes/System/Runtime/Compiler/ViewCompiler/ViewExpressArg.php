<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressArg.php
// @date: 20221018 10:08:28
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayIterator; 
use IGK\System\Runtime\Compiler\ViewCompiler\Html\ExpressionArgNode;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewExpressionArg;
use IteratorAggregate;
use Traversable;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressArg implements IteratorAggregate, IViewExpressionArg{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $expression;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $value;
    /**
     * extract value
     * @var false
     */
    var $extract = false;

    /**
    * .ctr
    * @param mixed $expression
    * @param mixed $value
    * @param mixed $extract
    */
    public function __construct($expression, $value, $extract=false)
    {
        $this->expression = $expression;
        $this->value = $value;        
        $this->extract = $extract;
    }

    /**
    * auto generate doc.
    */
    public function getExpression() {
        return "$".$this->expression;
    }

    /**
    * auto generate doc.
    * @return Traversable
    */
    public function getIterator(): Traversable {
        return new ArrayIterator([$this->expression, $this->value]);
     }

    /**
    * get string presentation.
    */
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