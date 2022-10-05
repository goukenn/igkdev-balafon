<?php
// @author: C.A.D. BONDJE DOUE
// @file: DeprecatedMethodException.php
// @date: 20220908 03:41:14
namespace IGK\System\Exceptions;

use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Exceptions
*/
class DeprecatedMethodException extends IGKException{
    public function __construct(string $method)
    {
        parent::__construct($method);
    }
}