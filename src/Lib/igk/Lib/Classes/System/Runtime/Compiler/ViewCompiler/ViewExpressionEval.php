<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionEval.php
// @date: 20221016 09:33:44
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use ArrayAccess;
use IGK\System\Html\IHtmlGetValue;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* on compilation store expression to evaluate
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionEval implements IHtmlGetValue, ArrayAccess{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $source;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $variables = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $value;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $listener;
    use ArrayAccessSelfTrait;
    /**
     * array of dependency
     * @var null|array
     */
    var $dependOn;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $tagname = "igk:view-expression-eval";

    /**
    * auto generate doc.
    */
    public function getCanRenderTag(){
        return true;
    }
    /**
     * assert that string are equal
     * @param string $eval 
     * @param null|array $dependOn 
     * @return void 
     */

    public function __construct(string $eval, ?array & $dependOn=null)
    {        
        // + | detect if value stream is escapsed
        if (strpos($eval, "<?=") === 0){
            $eval = ltrim(substr($eval, 3));
            if (strrpos($eval, "?>") === (strlen($eval)-2)){
                $eval = rtrim(substr($eval, 0, -2));
            }
        }
        $this->source = $eval;   
        $this->dependOn = & $dependOn; 
    }
    /**
     * expression to evaluate value
     * @param mixed $options 
     * @return string 
     */

    public function getValue($options = null) {
        return $this->__toString();
     }

    /**
    * get string presentation.
    */
    public function __toString(){
        return sprintf ("<?= %s ?>", $this->source); 
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function getTagName($options = null)
    {
        return $this->__toString();
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        if ($this->value)
            return call_user_func_array([$this->value, $name], $arguments);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function _access_OffsetGet($n){
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
    public function _access_OffsetSet($n, $v){
        return $this;
    }
}