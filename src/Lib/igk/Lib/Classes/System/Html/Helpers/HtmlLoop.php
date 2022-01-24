<?php

namespace IGK\System\Html\Helpers;

/**
 * loop helper function
 * @package IGK\System\Html\Helpers
 */
class HtmlLoop{
    public static function list($n, $i){
        $n->li()->Content = $i;
    }
}