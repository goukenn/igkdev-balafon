<?php

namespace IGK\System\Library; 
class curl extends \IGKLibraryBase{
    public function init():bool{
        if (!function_exists("curl_init")){
            return false;
        }
        include __DIR__."/Curl/.functions.pinc";
        return true;
    }
}