<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewGetterExpression.php
// @date: 20221028 16:02:25
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayAccess;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGK\System\Runtime\Compiler\ViewCompiler\Html\ExpressionArgNode;
use IGK\System\Runtime\Compiler\ViewCompiler\Html\ExpressionNodeBase;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewExpressionArg;
/**
* use to resolve getter expression string operations
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewGetterExpression implements IViewExpressionArg, ArrayAccess{
    use ArrayAccessSelfTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_name;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_value;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_resolv;

    /**
    * .ctr
    * @param mixed $name
    * @param mixed $value
    */
    public function __construct($name, $value)
    {
        $this->m_name = $name;
        $this->m_value = $value;
        $this->m_resolv = "";
    }
    /**
     * expression to resolv and render data
     * @return string 
     */

    public function getExpression() { 
        return '$'.$this->m_name;
    }

    /**
    * auto generate doc.
    * @param ViewGetterExpression $item
    */
    public static function GetInnerValue(ViewGetterExpression $item)  {
        return $item->m_value;
    }
    /**
     * get real in stored value
     * @param ViewGetterExpression $item 
     * @return mixed 
     */

    public static function GetRealValue(ViewGetterExpression $item){
        $v_v = self::GetInnerValue($item);
        if ($v_v instanceof ViewExpressionEval){
            return $v_v->value;
        }
        return $v_v;
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        $c = $this->m_resolv;
        $this->m_resolv = "";
        return '<?= $'.$this->m_name.$c.' ?>';
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        if ($this->m_value){
            if ($this->m_value instanceof ViewExpressionEval){
                return call_user_func_array([$this->m_value->value, $name], $arguments);
            }
        }
        $this->m_resolv .= sprintf("->".$name."(%s)", 
            implode(",", array_map([\IGK\Helper\ArrayUtils::class, "ArgumentsMap"], $arguments))
        );
        return $this;
    }

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        $this->m_resolv .= "->".$n;
        return $this;
    }

    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        // throw new NotImplementException(__METHOD__);
    }

    /**
    * auto generate doc.
    * @param mixed $name
    */
    protected function _access_OffsetGet($name){
        if (is_string($name)){
            $name = escapeshellarg($name);
        }
        $this->m_resolv .= "[".$name."]";
        return $this;
    }

    /**
    * auto generate doc.
    */
    public function createExpressionNode(){ 
        $c = $this->m_resolv;
        $this->m_resolv = "";
        $m = HtmlRenderer::class."::Render(";
        return new ExpressionNode('<?= '.$m.'$'.$this->m_name.$c.') ?>');          
    }
}

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ExpressionNode extends ExpressionNodeBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $expression;

    /**
    * .ctr
    * @param string $expression
    */
    public function __construct(string $expression)
    {
        $this->expression = $expression;
        parent::__construct();
    }

    /**
    * auto generate doc.
    */
    public function getCanRenderTag()
    {
        return false;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function render($options = null){
        return  ''.$this->expression.'';
    }
}