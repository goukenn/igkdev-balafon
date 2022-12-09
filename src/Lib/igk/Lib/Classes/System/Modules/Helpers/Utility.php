<?php
// @author: C.A.D. BONDJE DOUE
// @file: Utility.php
// @date: 20221208 15:27:41
namespace IGK\System\Modules\Helpers;


///<summary></summary>
/**
* 
* @package IGK\System\Modules\Helpers
*/
class Utility{
    public static function SanitizeName(string $dirname){
        return preg_replace("/[^0-9_a-z\/]/i", "",$dirname);
    }
}