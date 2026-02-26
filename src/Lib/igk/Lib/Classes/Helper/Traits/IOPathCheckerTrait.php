<?php
namespace IGK\Helper\Traits;

/**
* auto generate doc.
* @package IGK\Helper\Traits
*/
trait IOPathCheckerTrait{
     /**
     * check if path is root path 
     * @param string $path 
     * @return bool 
     */
    public static function IsRootPath(string $path): bool
    {
        $_ISUNIX = in_array(strtolower(PHP_OS), ['linux', 'darwin']);
        if ($_ISUNIX) {
            return igk_str_startwith($path, '/');
        }
        return preg_match("/[a-z]:(\\|\/)/i", $path);
    }
}