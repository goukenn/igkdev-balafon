<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLoop.php
// @date: 20220803 13:48:56
// @desc: 


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