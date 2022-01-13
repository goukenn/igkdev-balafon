<?php

namespace IGK\System\Configuration;

use IGK\System\Html\IHtmlGetValue;

/**
 * 
 * @package 
 */
class SysConfigExpression implements IHtmlGetValue{
    var $expression;
    /**
     * entry tag
     * @var string
     */
    protected $tag= "sys";

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function getValue($options = null) { 
       return $this->getStoreValue(); 
    }

    public function __toString()
    {
        return (string)igk_sys_configs()->get($this->expression);
    }
    public function getStoreValue(){
        if (!empty($ex = trim($this->expression)))
            return sprintf("{{ %s.%s }}", $this->tag, $ex);
        return null;
    }
   
}