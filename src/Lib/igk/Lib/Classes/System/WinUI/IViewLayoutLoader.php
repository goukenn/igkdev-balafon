<?php

// @author: C.A.D. BONDJE DOUE
// @filename: IViewLayoutLoader.php
// @date: 20220801 09:52:19
// @desc: layout loader interface

namespace IGK\System\WinUI;

use IGK\Controllers\BaseController;

interface IViewLayoutLoader{
    /**
     * get the current controller
     * @return BaseController 
     */
    function getController(): BaseController;
    /**
     * include file in layout
     * @param string $file 
     * @param null|array $args 
     * @return mixed 
     */
    function include(string $file, ?array $args);
}