<?php

// @author: C.A.D. BONDJE DOUE
// @filename: php.php
// @date: 20230129 09:46:57
// @desc: php function helpers

/**
 * get if module is present in module
 * @param mixed $mod_name exemple : mod_headers
 * @return true|void 
 */
function igk_php_is_module_enabled(string $mod_name){
    if (function_exists('apache_get_modules') && in_array($mod_name, apache_get_modules())) {
        return true;
    } 
    return false;
}

