<?php

// @author: C.A.D. BONDJE DOUE
// @filename: translation.php
// @date: 20220831 19:42:32
// @desc: stranlation helpers

use IGK\IGKTranslator;
use IGK\System\Exceptions\ArgumentTypeNotValidException;

if (!function_exists('__')){
    /**
     * shortcut to igk_resource_gets global function 
     * @param mixed $m arguments to format using core translation
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    function __(...$m){
        return igk_resources_gets(...$m);
    }
}else {
    require_once IGK_LIB_CLASSES_DIR.'/IGKTranslator';

    class translation extends IGKTranslator{
    }
}
