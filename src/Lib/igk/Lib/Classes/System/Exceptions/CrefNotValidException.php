<?php
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