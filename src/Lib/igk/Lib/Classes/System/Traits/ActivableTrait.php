<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActivableTrait.php
// @date: 20221123 22:35:39
namespace IGK\System\Traits;
use IGK\Helper\Activator;
/**
* use new intance 
* @package IGK\System\Traits
*/
trait ActivableTrait {
    /**
    * auto generate doc.
    * @param mixed $array
    * @return static
    */
    public static function ActivateNew($array){
        return Activator::CreateNewInstance(static::class, $array, true);
    }
}