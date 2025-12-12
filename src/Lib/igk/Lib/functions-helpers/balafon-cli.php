<?php

if (!function_exists('getallheaders')){
    /**
     * inject getallheaders on cli-call 
     * @return array 
     */
    function getallheaders(){       
        return [
            'User-Agent'=>'balafon-cli',
            'host'=>'balafon'
        ];
    }
}