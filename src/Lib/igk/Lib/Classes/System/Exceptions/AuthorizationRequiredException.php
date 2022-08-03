<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AuthorizationRequiredException.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\Exceptions;

use IGKException;
use Throwable;

class AuthorizationRequiredException extends IGKException{

    public function __construct($msg, $code=500, ?Throwable $throwable)
    {
        parent::__construct($msg, $code, $throwable);
    }
}