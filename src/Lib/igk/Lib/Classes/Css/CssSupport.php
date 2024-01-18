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
    /**
     * added for css definition 
     * @var mixed
     */
    var $def;
    use ArrayAccessSelfTrait;

    public function __construct($rule)
    {
        $this->rule = $rule;
        $this->media = new IGKMedia("@support", $rule);
    }
    public function __toString()
    {  
        return "@support(".$this->rule."){".$this->media . "}";
    }
    /**
     * set value
     * @param mixed $key 
     * @param mixed $value 
     * @return $this 
     */
    public function set($key, $value){
        $this[$key] = $value;
        return $this;
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