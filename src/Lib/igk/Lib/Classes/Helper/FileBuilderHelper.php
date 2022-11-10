<?php

// @author: C.A.D. BONDJE DOUE
// @filename: FileBuilderHelper.php
// @date: 20220828 14:58:07
// @desc: 

namespace IGK\Helper;

use Closure;
use IGK\System\Console\Logger;

class FileBuilderHelper
{
    public static function Build($data, $force = false, ?object $bind =null )
    {
        $builder = new self;
        foreach ($data as $n => $c) {
            if ($force || !file_exists($n)) {
                if ($bind){
                    $c = Closure::fromCallable($c)->bindTo($bind);
                }
                $c($n);
                $builder->info("generate : " . $n);
            }
        }
    }
    public function info($msg)
    {
        Logger::info(...func_get_args());
    }
}


