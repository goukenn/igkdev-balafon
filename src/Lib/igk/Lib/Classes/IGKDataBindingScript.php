<?php
// @file: IGKDataBindingScript.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
final class IGKDataBindingScript extends IGKObject{
    private $_args, $_shifparent;
    public function __construct(){
        $this->_args=array();
        $this->_shifparent=0;
    }
    public function __get($n){
        if($n === "args")
            return $this->peek();
        return null;
    }
    public function __isset($n){
        return ($n === 'args');
    }
    public function Count(){
        return count($this->_args);
    }
    public function getArgs(){
        return $this->peek();
    }
    public function peek(){
        if((($c=count($this->_args)) - $this->_shifparent) > 0){
            return $this->_args[$c - (1 + $this->_shifparent)];
        }
        return null;
    }
    public function pop(){
        return array_pop($this->_args);
    }
    public function push($data){
        array_push($this->_args, $data);
    }
    public function resetShift(){
        $this->_shifparent=0;
    }
    public function shiftParent(){
        $this->_shifparent=1;
    }
}