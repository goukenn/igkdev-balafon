<?php
// @author: C.A.D. BONDJE DOUE
// @filename: zip.php
// @date: 20220803 13:48:55
// @desc: 



namespace IGK\System\Library;

/**
 * zip library 
 * @package IGK\System\Library
 */
class zip extends \IGKLibraryBase{
    public function init():bool{ 
        if (in_array("zip", get_loaded_extensions(false))){
            require_once(IGK_LIB_DIR . "/igk_zip.php");
            return true;
        }
        return false;
    }

}