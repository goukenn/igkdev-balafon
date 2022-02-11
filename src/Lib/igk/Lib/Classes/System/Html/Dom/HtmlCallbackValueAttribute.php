<?php

namespace IGK\System\Html\Dom;

class HtmlCallbackValueAttribute extends HtmlItemAttribute{
    var $callback;
    public function __construct(callable $callback)
    { 
        $this->callback = $callback;
    }
    public function getValue($option = null) {  
        $fc = $this->callback;
        return $fc($option, $this);
    }

}