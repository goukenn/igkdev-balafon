<?php
// @author: C.A.D. BONDJE DOUE
// @filename: curl.php
// @date: 20220803 13:48:55
// @desc: 

// @file: curl.php
// @desc: curl library
namespace IGK\System\Library; 
class curl extends \IGKLibraryBase{
    public function init():bool{
        if (!function_exists("curl_init")){
            return false;
        }
        // igk_ilog("init curl - ".igk_env_count(__METHOD__));
        include_once __DIR__."/Curl/.functions.pinc";
        return true;
    }
}