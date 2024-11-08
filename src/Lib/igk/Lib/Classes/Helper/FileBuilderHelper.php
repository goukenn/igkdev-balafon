<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FileBuilderHelper.php
// @date: 20220828 14:58:07
// @desc: 

namespace IGK\Helper;

use Closure;
use IGK\System\Console\Logger;

abstract class FileBuilderHelper
{
    public static function Build($data, $force = false, ?object $bind = null)
    {
        foreach ($data as $n => $c) {
            if ($force || !file_exists($n)) {
                if ($c instanceof Closure) {
                    if ($bind) {
                        $c = Closure::fromCallable($c)->bindTo($bind);
                    }
                    $c($n);
                }else{// just touch the file
                    igk_io_w2file($n, '');
                }
                Logger::info("generate : " . $n);
            }
        }
    }
}
