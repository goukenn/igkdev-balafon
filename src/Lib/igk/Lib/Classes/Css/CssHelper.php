<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssHelper.php
// @date: 20221005 08:03:37
// @desc: 

namespace IGK\Css;

/**
 * helper utility method
 * @package IGK\Css
 */
abstract class CssHelper{
    public static function MapToFileCallback(){
        return function($a){ return explode("|", $a)[0]; };
    }
}