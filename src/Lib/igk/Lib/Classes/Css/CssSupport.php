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
* Css support.
* @package IGK\Css
*/
class CssSupport implements ArrayAccess{

    /**
    * Property: rule.
    * @var mixed
    */
    var $rule;

    /**
    * Property: media.
    * @var mixed
    */
    var $media; 
    /**
     * added for css definition 
     * @var mixed
     */
    var $def;
    use ArrayAccessSelfTrait;

    /**
    * .ctr
    * @param mixed $rule
    */
    public function __construct($rule)
    {
        $this->rule = $rule;
        $this->media = new IGKMedia("@support", $rule);
    }

    /**
    * Returns string representation.
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
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */

    public function _access_OffsetSet($n, $v){
        $this->media[$n] = $v;
    }

    /**
    * Access offset get.
    * @param mixed $n
    */

    public function _access_OffsetGet($n){
        return $this->media[$n];
    }

    /**
    * Returns Css Def.
    * @param mixed $theme
    * @param mixed $systheme
    * @param mixed $minfile
    */

    public function getCssDef($theme, $systheme, $minfile=true){
        return $this->media->getCssDef($theme, $systheme, $minfile);
    }
}