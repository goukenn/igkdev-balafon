<?php


namespace IGK\System\Library;

/**
 * zip library 
 * @package IGK\System\Library
 */
class zip extends \IGKLibraryBase{
    public function init():bool{
        if (in_array("zip", get_loaded_extensions(false))){

            return true;
        }
        return false;
    }

}