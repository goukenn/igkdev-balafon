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
    public function __toString(){
        $g = $this->key;
        if (!empty($this->color)){
            $g.=", ".$this->color;
        }
        return sprintf("[cl:%s]", $g);
    }
}