<?php
// @author: C.A.D. BONDJE DOUE
// @file: OperationNotAllowedException.php
// @date: 20221103 09:06:28
namespace IGK\System\Exceptions;
use IGKException;
use Throwable;

/**
* Operation not allowed exception
* @package IGK\System\Exceptions
*/
class OperationNotAllowedException extends IGKException{
    /**
    * .ctr
    * @param mixed $msg
    * @param mixed $code
    * @param null|Throwable $throwable
    */
    public function __construct($msg, $code= 500, ?Throwable $throwable=null)
    {
        parent::__construct($msg, $code, $throwable);
    }
}