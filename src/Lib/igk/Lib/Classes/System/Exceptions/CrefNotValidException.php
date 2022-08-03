<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CrefNotValidException.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Exceptions;

use IGKException;
use function igk_resources_gets as __;

///<summary>raise the cref not valid security </summary>
/**
 * cref not valid exception
 * @package IGK\System\Security
 */
class CrefNotValidException extends IGKException{
    public function __construct($code=500, ?\Throwable $throwable=null){
        parent::__construct(__("Cref Security failed"), $code, $throwable);
    }
}