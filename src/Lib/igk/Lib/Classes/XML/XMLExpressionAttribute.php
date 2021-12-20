<?php
namespace IGK\System\XML;



/**
 * represent and template expression value. no need to converts
 * @package 
 */
class XMLExpressionAttribute implements IHtmlGetValue{
    private $m_value;
    public function __construct(string $expression){
        $this->m_value = $expression;
    }
    public function getValue($options = null) {     
        return  $this->m_value;
    }
} 