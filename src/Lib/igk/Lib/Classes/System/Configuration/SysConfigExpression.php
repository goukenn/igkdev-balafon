<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SysConfigExpression.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Configuration;
use IGK\System\Html\IHtmlGetValue;
/**
 * retrieve sys expression
 * @package IGK\System\Configuration
 */
class SysConfigExpression implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $expression;
    /**
     * entry tag
     * @var string
     */
    protected $tag= 'sys';

    /**
    * .ctr
    * @param mixed $expression
    */
    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function getValue($options = null) { 
       return $this->getStoreValue(); 
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return (string)igk_configs()->get($this->expression);
    }

    /**
    * auto generate doc.
    */
    public function getStoreValue(){
        if (!empty($ex = trim($this->expression)))
            return sprintf("{{ %s.%s }}", $this->tag, $ex);
        return null;
    }
}