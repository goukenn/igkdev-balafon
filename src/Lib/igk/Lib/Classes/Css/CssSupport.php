<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssSupport.php
// @date: 20220423 08:02:18
// @desc: Css Support
namespace IGK\Css;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKMedia;

/**
* auto generate doc.
* @package IGK\Css
*/
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

    /**
    * auto generate doc.
    */
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

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
    public function _access_OffsetSet($n, $v){
        $this->media[$n] = $v;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function _access_OffsetGet($n){
        return $this->media[$n];
    }

    /**
    * auto generate doc.
    * @param mixed $theme
    * @param mixed $systheme
    * @param mixed $minfile
    */
    public function getCssDef($theme, $systheme, $minfile=true){
        return $this->media->getCssDef($theme, $systheme, $minfile);
    }
}