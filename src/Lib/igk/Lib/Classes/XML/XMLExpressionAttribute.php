<?php
namespace IGK\System\XML;

use IIGKHtmlGetValue;

/**
 * represent and template expression value. no need to converts
 * @package 
 */
class XMLExpressionAttribute implements IIGKHtmlGetValue{
    private $m_value;
    public function __construct(string $expression){
        $this->m_value = $expression;
    }
    public function getValue($options = null) {     
        return  $this->m_value;
    }
} 