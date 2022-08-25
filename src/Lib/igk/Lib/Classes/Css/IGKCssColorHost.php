<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCssColorHost.php
// @date: 20220729 08:59:16
// @desc: 

namespace IGK\Css;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

class IGKCssColorHost implements ArrayAccess{
    use ArrayAccessSelfTrait;
    const PRIMARY_COLOR = 'inherit';
    private $_;
    private function __construct(){
    }
    public static function Create(& $color){
        $c = new self();
        $c->_ = & $color;
        return $c;
    }
    public function _access_offsetSet($n,$v):void{    
        if (key_exists($n, $this->_)){
            return;
        }
        $this->_[$n] = $v;
    }
    public function _access_offsetGet($n){
        return igk_getv($this->_, $n, self::PRIMARY_COLOR);
    }
    public function _access_offsetUnset($n):void{
        unset($this->_[$n]);
    }
    public function _access_offsetExists($n):bool{
        return key_exists($n, $this->_);
    }
}