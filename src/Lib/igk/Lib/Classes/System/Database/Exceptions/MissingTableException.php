<?php
// @author: C.A.D. BONDJE DOUE
// @file: MissingTableException.php
// @date: 20230831 16:50:08
namespace IGK\System\Database\Exceptions;

use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Exceptions
*/
class MissingTableException extends IGKException{
    public function __construct(string $table){
        parent::__construct(sprintf('missing table : %s', $table));
    }
}