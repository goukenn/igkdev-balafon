<?php

// @author: C.A.D. BONDJE DOUE
// @filename: translation.php
// @date: 20220831 19:42:32
// @desc: stranlation helpers

use IGK\IGKTranslator;

if (!function_exists('__')){
    function __(...$m){
        return igk_resources_gets(...$m);
    }
}else {
    require_once IGK_LIB_CLASSES_DIR.'/IGKTranslator';

    class translation extends IGKTranslator{
    }
}
