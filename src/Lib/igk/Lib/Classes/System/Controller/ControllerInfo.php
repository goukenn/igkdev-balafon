<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Controller\ControllerInfo.php
// @date: 20220824 13:13:23
// @desc: 
namespace IGK\System\Controller;

use IGK\System\Traits\NoSetExtraPropertyTrait;

/**
 * store controller info
 * @package IGK\System\Controller
 */
class ControllerInfo{
    use NoSetExtraPropertyTrait;

    var $initCount;
}