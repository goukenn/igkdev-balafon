<?php

namespace IGK\System\Traits;

use function igk_resources_gets as __;
/**
 * disable magic setting for an object
 */
trait NoSetExtraPropertyTrait
{
    public function __set($n,$v){
        igk_die(sprintf(__("set [%s] not allowed"), get_class($this)."::".$n));
    }
}