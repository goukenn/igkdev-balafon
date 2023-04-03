<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExpressionEval.php
// @date: 20221016 09:33:44
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use ArrayAccess;
use IGK\System\Html\IHtmlGetValue;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* on compilation store expression to evaluate
* @package IGK\System\Runtime\Compiler
*/
class ViewExpressionEval implements IHtmlGetValue, ArrayAccess{
    var $source;
    var $variables = [];
    var $value;
    var $listener;
    use ArrayAccessSelfTrait;
    /**
     * array of dependency
     * @var null|array
     */
    var $dependOn;
    protected $tagname = "igk:view-expression-eval";

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
    public function __toString(){
        return sprintf ("<?= %s ?>", $this->source); 
    }
    public function getTagName($options = null)
    {
        return $this->__toString();
    }    
    public function __call($name, $arguments)
    {
        if ($this->value)
            return call_user_func_array([$this->value, $name], $arguments);
        return $this;
    }
    public function _access_OffsetGet($n){
        return $this;
    }
    public function _access_OffsetSet($n, $v){
        return $this;
    }
}