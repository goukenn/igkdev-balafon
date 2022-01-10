<?php

namespace IGK\System\Html;

abstract class HtmlAttributeValue implements IHtmlGetValue{
    protected $value;

    public function setValue($value){
        $this->value = $value;
        return $this;
    }
}