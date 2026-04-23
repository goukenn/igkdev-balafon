<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XMLExpressionAttribute.php
// @date: 20220803 13:48:54
// @desc: 
namespace IGK\System\XML;

/**
 * represent and template expression value. no need to converts
 * @package 
 */
class XMLExpressionAttribute implements IHtmlGetValue{
    /**
    * Property: value.
    * @var mixed
    */
    private $m_value;
    /**
    * .ctr
    * @param string $expression
    */
    public function __construct(string $expression){
        $this->m_value = $expression;
    }
    /**
    * Returns Value.
    * @param null|mixed $options
    */
    public function getValue($options = null) {     
        return  $this->m_value;
    }
} 