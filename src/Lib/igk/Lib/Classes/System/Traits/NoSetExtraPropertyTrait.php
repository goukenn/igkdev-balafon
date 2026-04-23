<?php
namespace IGK\System\Traits;
use function igk_resources_gets as __;

/**
 * disable magic setting for an object
 */
trait NoSetExtraPropertyTrait
{
    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n,$v){
        igk_die(sprintf(__("set [%s] not allowed"), get_class($this)."::".$n));
    }
}