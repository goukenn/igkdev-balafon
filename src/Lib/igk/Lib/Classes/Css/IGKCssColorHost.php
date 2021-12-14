<?php

class IGKCssColorHost implements ArrayAccess{
    private $_;
    private function __construct(){

    }
    public static function Create(& $color){
        $c = new self();
        $c->_ = & $color;
        return $c;
    }
    public function offsetSet($n,$v):void{    
        if (key_exists($n, $this->_)){
            return;
        }
        $this->_[$n] = $v;
    }
    public function offsetGet($n){
        return $this->_[$n];
    }
    public function offsetUnset($n):void{
        unset($this->_[$n]);
    }
    public function offsetExists($n):bool{
        return key_exists($n, $this->_);
    }
}