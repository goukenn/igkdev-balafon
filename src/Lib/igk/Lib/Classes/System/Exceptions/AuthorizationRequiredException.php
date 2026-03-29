<?php
// @author: C.A.D. BONDJE DOUE
// @filename: AuthorizationRequiredException.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\Exceptions;
use IGKException;
use Throwable;
/**
* Authorization required exception.
* @package IGK\Exceptions
*/
class AuthorizationRequiredException extends IGKException{
    /**
    * .ctr
    * @param mixed $msg
    * @param mixed $code
    * @param null|Throwable $throwable
    */
    public function __construct($msg, $code=500, ?Throwable $throwable = null)
    {
        parent::__construct($msg, $code, $throwable);
    }
}