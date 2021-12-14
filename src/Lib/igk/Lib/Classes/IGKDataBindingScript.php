<?php
// @file: IGKDataBindingScript.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKDataBindingScript extends IGKObject{
    private $_args, $_shifparent;
    ///<summary></summary>
    public function __construct(){
        $this->_args=array();
        $this->_shifparent=0;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __get($n){
        if($n === "args")
            return $this->peek();
        return null;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __isset($n){
        return ($n === 'args');
    }
    ///<summary></summary>
    public function Count(){
        return count($this->_args);
    }
    ///<summary></summary>
    public function getArgs(){
        return $this->peek();
    }
    ///<summary></summary>
    public function peek(){
        if((($c=count($this->_args)) - $this->_shifparent) > 0){
            return $this->_args[$c - (1 + $this->_shifparent)];
        }
        return null;
    }
    ///<summary></summary>
    public function pop(){
        return array_pop($this->_args);
    }
    ///<summary></summary>
    ///<param name="data"></param>
    public function push($data){
        array_push($this->_args, $data);
    }
    ///<summary></summary>
    public function resetShift(){
        $this->_shifparent=0;
    }
    ///<summary></summary>
    public function shiftParent(){
        $this->_shifparent=1;
    }
}
