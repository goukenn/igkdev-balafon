<?php

namespace IGK\System\Exceptions;

use IGKException;

class LoadArticleException extends IGKException{
    public function __construct($key){
        $file = igk_environment()->last("FileLoader");
        parent::__construct( sprintf("Load article throw an error %s", $file), 500);
    }
}