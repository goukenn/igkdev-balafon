<?php

namespace IGK\System\Exceptions;

use IGKException;

class EnvironmentArrayException extends IGKException{
    public function __construct($key){
        parent::__construct( sprintf("Environment key %s not an array", $key), 500);
    }
}