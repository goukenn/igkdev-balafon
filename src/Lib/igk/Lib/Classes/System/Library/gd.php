<?php

namespace IGK\System\Library;

use IGKGD;

class gd extends \IGKLibraryBase{
    public function init():bool{
        // initialize function
        if(!extension_loaded("gd")){   
            return false;
        }
        require_once IGK_LIB_DIR."/igk_gd.php";
        return class_exists(IGKGD::class);
        
    }
}