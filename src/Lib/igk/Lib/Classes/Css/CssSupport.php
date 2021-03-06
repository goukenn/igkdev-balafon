<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CssSupport.php
// @date: 20220423 08:02:18
// @desc: Css Support

namespace IGK\Css;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKMedia;

class CssSupport implements ArrayAccess{
    var $rule;
    var $media; 
    use ArrayAccessSelfTrait;

    public function __construct($rule)
    {
        $this->rule = $rule;
        $this->media = new IGKMedia("@support", $rule);
    }
    public function __toString()
    {  
        return "@support(".$this->rule."){".$this->media."" . "}";
    }

    public function _access_OffsetSet($n, $v){
        $this->media[$n] = $v;
    }
    public function _access_OffsetGet($n){
        return $this->media[$n];
    }
    public function getCssDef($theme, $systheme, $minfile=true){
        return $this->media->getCssDef($theme, $systheme, $minfile);
    }
}