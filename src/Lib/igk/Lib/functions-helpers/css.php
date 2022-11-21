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


///<summary>helper: used to treat style value . {sys: ...data}</summary>
/**
 * helper: used to treat style value . {sys: ...data}
 */
function igk_css_treat_value(string $v, \IGK\Css\ICssStyleContainer $theme, ?\IGK\Css\ICssStyleContainer  $systheme = null, $themeexport = 1)
{
    $reg = IGK_CSS_TREAT_REGEX;
    $pos = 0;
    $tab = [];
    while (($pos = strpos($v, "{", $pos)) !== false) {
        $ob = igk_str_read_brank($v, $pos, "}", "{");
        if (!isset($tab[$ob])){
            $tab[$ob] = igk_css_treat_bracket($ob, $theme, $systheme);
            $v = str_replace($ob, $tab[$ob], $v);
        }
    }     
    return $v;
}

