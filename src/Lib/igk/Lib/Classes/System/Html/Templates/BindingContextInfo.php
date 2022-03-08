<?php

namespace IGK\System\Html\Templates;

use IGK\Exceptions\BindingContextPropertyNotFoundException;

/**
 * extra binding context
 * @package 
 */
class BindingContextInfo{
    /**
     * raw data to pass binding info
     * @var mixed
     */
    var $raw;

    /**
     * controller to pass
     * @var ?IGK\Controllers\BaseController 
     */
    var $ctrl;

    public function __toString()
    {
        return json_encode(array_filter((array)$this));
    }
    public function __get($n){
        throw new BindingContextPropertyNotFoundException($n);
    }

    public function to_array(){
        return (array)$this;
    }
}