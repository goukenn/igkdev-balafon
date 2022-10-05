<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerNotFoundException.php
// @date: 20220901 09:24:15
// @desc: raise when controller not found

namespace IGK\System\Exceptions;

use IGKException;
use function igk_resources_gets as __;

/**
 * raise for controller not found
 * @package IGK\System\Exceptions
 */
class ControllerNotFoundException extends IGKException{
    public function __construct($controller)
    {
        parent::__construct(__(sprintf("Controller not found. [%s]", $controller)), 501);
    }
}