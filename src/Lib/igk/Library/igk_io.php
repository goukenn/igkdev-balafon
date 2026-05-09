<?php

// @author: C.A.D. BONDJE DOUE
// @filename: igk_io.php
// @date: 20260506 09:22:51
// @desc: io utility functions

use IGK\Helper\IO;


/**
 * 
 * @param string $fn 
 * @param null|string $regex 
 * @param null|string $assoc 
 * @return object|null 
 */
function igk_io_split_litteral(string $fn, ?string $regex = null, ?string $assoc=null)
{
    if (is_null($regex) && is_null($assoc)){
        $assoc = 'number|title';
    }
    $regex = $regex ?? '/(?P<number>\\d+)-(?P<title>.+)/';
    $assoc = $assoc ?? '0';
    if (preg_match($regex, $fn, $tab)) { 
        return (object)igk_extract_assoc($tab, $assoc);
    }
}


