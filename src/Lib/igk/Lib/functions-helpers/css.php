<?php
// @author: C.A.D. BONDJE DOUE
// @filename: css.php
// @date: 20221109 14:38:40
// @desc: css helper function 

/**
 * filter array condition 
 * @param mixed $tab 
 * @return string 
 */
function igk_css_get_class($tab):string{
    $m = [];
    foreach($tab as $k=>$v){
        if ($v) $m[] = $k;
    }
    return implode(' ', $m); 
}


