<?php
// @author: C.A.D. BONDJE DOUE
// @filename: gd.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\Library;
use IGKGD;
/**
* Gd.
* @package IGK\System\Library
*/
class gd extends \IGKLibraryBase{
    /**
     * Initializes the GD library extension.
     *
     * @return bool True if the GD extension is loaded and the class exists.
     */
    public function init():bool{
        // initialize function
        if(!extension_loaded("gd")){
            return false;
        }
        require_once IGK_LIB_DIR."/igk_gd.php";
        return class_exists(IGKGD::class);
    }
}