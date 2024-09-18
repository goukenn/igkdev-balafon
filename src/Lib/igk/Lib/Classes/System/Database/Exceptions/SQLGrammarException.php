<?php
// @author: C.A.D. BONDJE DOUE
// @file: SQLGrammarException.php
// @date: 20240908 11:10:28
namespace IGK\System\Database\Exceptions;

use IGKException;
use Throwable;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Exceptions
* @author C.A.D. BONDJE DOUE
*/
class SQLGrammarException extends IGKException{
    public function __construct($msg, $code=500, Throwable $throwable=null)
    {
        parent::__construct($msg, $code, $throwable);
    }
}