<?php

namespace IGK\Helper;

use Closure;

class InvocationHelper{
    /**
     * create inclusion helper 
     */
    public static function Include(): Closure{
        static $_include = null;
        if ($_include===null){ 
            $_include = (function(){         
                extract(func_get_arg(1));
                include func_get_arg(0);
            });
        }
        return $_include;
    }
}
