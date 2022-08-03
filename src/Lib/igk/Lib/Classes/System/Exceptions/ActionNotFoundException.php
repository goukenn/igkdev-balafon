<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ActionNotFoundException.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Exceptions;

use IGKException;
use Throwable;
use function igk_resources_gets as __;

class ActionNotFoundException extends IGKException{
    public function __construct($name, ?Throwable $throwable=null )
    { 
        parent::__construct(
            sprintf(__("Action [%s] not found"), $name),
            404, $throwable );
    }
    public function headers(){
        return [
            // "WWW-Authenticate: Basic realm=".escapeshellarg($this->getMessage())
        ];
    }
}