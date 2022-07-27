<?php


namespace IGK\System\WinUI;

use IGK\Controllers\BaseController;

interface IViewLayoutLoader{
    function getController(): BaseController;
    function include(string $file, ?array $args);
}