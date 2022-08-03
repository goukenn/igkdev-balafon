<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IViewLayoutLoader.php
// @date: 20220801 09:52:19
// @desc: layout loader interface

namespace IGK\System\WinUI;

use IGK\Controllers\BaseController;

interface IViewLayoutLoader{
    function getController(): BaseController;
    function include(string $file, ?array $args);
}