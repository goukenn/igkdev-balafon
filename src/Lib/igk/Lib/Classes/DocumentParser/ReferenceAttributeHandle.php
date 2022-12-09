<?php



namespace IGK\DocumentParser;

use IGK\System\Html\IHtmlGetValue;

class ReferenceAttributeHandle implements IHtmlGetValue{
    var $value;
    var $condition = false;
    public function __construct($v, ?callable $condition = null)
    {
        $this->value = $v;
    }
    public function getValue($option=null){
        return null;
    }
}