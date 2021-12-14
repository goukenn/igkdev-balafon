<?php

namespace IGK\System\Exceptions;

use IGKException;
use function igk_resources_gets as __;

class ArgumentNotValidException extends IGKException{
    /**
     * 
     * @param string $argname 
     * @return void 
     */
    public function __construct($argname){
        parent::__construct( sprintf(__("Argument not valid %s"), $argname));
    }
}