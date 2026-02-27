<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArrayMapHelper.php
// @date: 20231223 13:36:19
namespace IGK\System\Mapping\Helper;
use Exception;
/**
* store mapping array utility function 
* @package IGK\System\Mapping\Helper
*/
abstract class ArrayMapHelper{

    /**
    * auto generate doc.
    * @param int $throw
    * @return null|float
    */
    public static function DieNumberMap($o, $throw = 1){
        if (!is_numeric($o)){
            $throw && igk_die($o, 'not a number ');
            return null;
        }
        return floatval($o);
    }
}