<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActionUtility.php
// @date: 20250621 08:11:12
namespace IGK\System\Actions\Helper;
/**
* auto generate doc.
* @package IGK\System\Actions\Helper
* @author C.A.D. BONDJE DOUE
*/
class ActionUtility{
    /**
     * glue arguments 
     * @param array $arg 
     * @return array<int, mixed> 
     */
    public static function GlueArgs(array $arg){
        return array_filter(array_values($arg) , function($a){
            return is_string($a) || is_numeric($a);
        });
    }
}