<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCssColorHost.php
// @date: 20220729 08:59:16
// @desc: 
namespace IGK\Css;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* Igkcss color host.
* @package IGK\Css
*/
class IGKCssColorHost implements ArrayAccess{
    use ArrayAccessSelfTrait;
    /**
    * Constant: primary color.
    * @var mixed
    */
    const PRIMARY_COLOR = 'inherit';
    /**
    * Property: .
    * @var mixed
    */
    private $_;
    /**
     * Constructor.
     */
    private function __construct(){
    }
    /**
     * Create a new IGKCssColorHost bound to the given color reference.
     *
     * @param mixed $color Reference to the color array.
     * @return static
     */
    public static function Create(& $color){
        $c = new self();
        $c->_ = & $color;
        return $c;
    }
    /**
     * Set the value at the given offset in the color array.
     *
     * @param mixed $n The offset key.
     * @param mixed $v The value to set.
     * @return void
     */
    public function _access_offsetSet($n,$v):void{
        $this->_[$n] = $v;
    }
    /**
     * Get the value at the given offset from the color array.
     *
     * @param mixed $n The offset key.
     * @return mixed
     */
    public function _access_offsetGet($n){
        return igk_getv($this->_, $n);
    }
    /**
     * Unset the value at the given offset in the color array.
     *
     * @param mixed $n The offset key.
     * @return void
     */
    public function _access_offsetUnset($n):void{
        unset($this->_[$n]);
    }
    /**
     * Check whether the given offset exists in the color array.
     *
     * @param mixed $n The offset key.
     * @return bool
     */
    public function _access_offsetExists($n):bool{
        return key_exists($n, $this->_);
    }
}