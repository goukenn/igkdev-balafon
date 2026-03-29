<?php
// @author: C.A.D. BONDJE DOUE
// @filename: curl.php
// @date: 20220803 13:48:55
// @desc:
// @file: curl.php
// @desc: curl library
namespace IGK\System\Library;
/**
* Curl.
* @package IGK\System\Library
*/
class curl extends \IGKLibraryBase{
    /**
     * Initializes the cURL library extension.
     *
     * @return bool True if cURL is available and the functions file is included.
     */
    public function init():bool{
        if (!function_exists("curl_init")){
            return false;
        }
        // igk_ilog("init curl - ".igk_env_count(__METHOD__));
        include_once __DIR__."/Curl/.functions.pinc";
        return true;
    }
}