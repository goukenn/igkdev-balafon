<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssColorDef.php
// @date: 20220730 10:18:32
// @desc: color definition 
namespace IGK\Css;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
class CssColorMarkValue{
    var $key;
    var $color;
    /**
     * Parse a CSS color mark value from a string.
     *
     * @param string $data The string to parse.
     * @return ?CssColorMarkValue The parsed instance or null if no match.
     */
    public static function Parse(string $data): ?CssColorMarkValue{
        $cl = null;
        if (preg_match("/\[cl:\s*(?P<name>".IGK_IDENTIFIER_PATTERN.")\s*(,(?P<def>(.+)))?\]/i", $data, $ref)){
            if (empty($key = trim(igk_getv($ref, "name", null)))){                
                igk_die("Parse not a valid value key is empty");
            }
            $cl = new static;      
            $cl->key = $key;
            $cl->color = trim(igk_getv($ref, "def", ""));
        }
        return $cl;        
    }
    /**
     * Return the string representation of this color mark value.
     *
     * @return string
     */
    public function __toString(){
        $g = $this->key;
        if (!empty($this->color)){
            $g.=", ".$this->color;
        }
        return sprintf("[cl:%s]", $g);
    }
}