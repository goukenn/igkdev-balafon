<?php
namespace IGK\System\Http;

use IGKException;
use Throwable;

class AuthorizationRequiredException extends NotAllowedRequestException{

    public function __construct($msg, ?Throwable $throwable=null)
    {
        // igk_wln_e("the message ". $msg);
        parent::__construct(null, $msg, $throwable);
    }
}