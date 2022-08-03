<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerHelper.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Helper;
use IGK\Controllers\BaseController;

/**
 * invoke controller basic method
 * @package IGK\Helper
 */
class ControllerHelper{
    public static function __callStatic($name, $arguments)
    {
        $controller = $arguments[0];
        return BaseController::Invoke($controller, $name, array_slice($arguments, 1));
    }
}