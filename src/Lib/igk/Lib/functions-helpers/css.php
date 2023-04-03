<?php
// @author: C.A.D. BONDJE DOUE
// @filename: css.php
// @date: 20221109 14:38:40
// @desc: css helper function 

if (!function_exists('igk_css_get_class')){
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
        return trim(implode(' ', $m)); 
    }    
}


if (!function_exists('igk_css_litteral')){
    /**
     * filter array condition 
     * @param mixed $tab 
     * @return string 
     */
    function igk_css_litteral(array $tab):?string{ 
        if (empty($g = trim(implode(' ', array_filter($tab)))))
            return null;
        return $g;
    }    
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

function igk_css_minify(string $source){
    $o = "";
    $ln = strlen($source);
    $offset = 0;
    $end = false;
    $ch = '';
    $skip = 0;
    
    while(!$end && ($offset<$ln)){
        $lchar = $ch;
        $ch = $source[$offset];
        switch($ch){
            case '/':
                //detect mutiline comment
                break;
            case '*':
                if ($lchar == '/'){
                    // multiline comment detected 
                    if (($pos = strpos($source, "*/", $offset)) !== false){
                        $lv = substr($source, $offset, $pos - $offset + 2);
                        $offset = $pos + 2;
                        $ch = '';
                        if (strpos($lv, "*#") ===0){
                            $o = rtrim($o, "/")."\n/".$lv;
                        }else{
                            $o .= $lv;
                        }
                    }
                }
                break;
            case ':':
                $o = rtrim($o).$ch;
                $skip = 1;
                $ch = '';
                break;
            case ' ':
                if (!$skip){
                    $o.= $ch;
                    $skip = 1;
                }
                $ch = '';
                break;
            case "\t":
            case "\r":
            case "\n":
                if (!$skip){
                    // $o.= ' ';
                    $skip = 1;
                }
                $ch = '';
        }
        $o .= $ch;
        $offset++;
        if ($skip && !empty($ch)){
            $skip = 0;
        }
    }

    return $o;
}
/**
 * remove css comment
 * @param string $src 
 * @return string 
 */
function igk_css_rm_comment(string $src){
    $ln = strlen($src);
    $offset = 0;
    // remove css comment
    while(($pos = strpos($src, "/*", $offset))!== false){
        if ( ($end = strpos($src, "*/", $pos))===false){
            $src = substr($src, $pos);
            break;
        }
        $src = substr($src, 0, $pos).substr($src, $end + 2); 
        $offset = $pos;
    }
    return $src;
}

