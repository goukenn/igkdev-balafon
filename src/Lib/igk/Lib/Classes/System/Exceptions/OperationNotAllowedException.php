<?php
// @author: C.A.D. BONDJE DOUE
// @file: OperationNotAllowedException.php
// @date: 20221103 09:06:28
namespace IGK\System\Exceptions;

use IGKException;
use Throwable;

///<summary></summary>
/**
* Operation not allowed exception
* @package IGK\System\Exceptions
*/
class OperationNotAllowedException extends IGKException{
    public function __construct($msg, $code= 500, ?Throwable $throwable=null)
    {
        parent::__construct($msg, $code, $throwable);
    }
}