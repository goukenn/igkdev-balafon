<?php
namespace IGK\Exceptions;

use IGKException;
use Throwable;

class AuthorizationRequiredException extends IGKException{

    public function __construct($msg, $code=500, ?Throwable $throwable)
    {
        parent::__construct($msg, $code, $throwable);
    }
}