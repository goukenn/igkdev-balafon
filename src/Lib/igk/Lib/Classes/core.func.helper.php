<?php

namespace IGK;

if (!function_exists('typeof')){
    function typeof($o){
        if (is_null($o)){
            return 'null';
        }
        if (is_object($o)){
            return get_class($o);
        }
        if (is_array($o)){
            return 'array';
        }
        if (is_string($o)){
            return 'string';
        }
        if (is_bool($o)){
            return 'bool';
        }
        if (is_int($o)){
            return 'int';
        }

    }
}