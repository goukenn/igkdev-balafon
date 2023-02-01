<?php


// @author: C.A.D. BONDJE DOUE
// @filename: DescValue.php
// @date: 20221230 11:01:13
// @desc: 
namespace IGK\System\Console;


class DescValue{
    var $value;
    public function __construct($v){
        $this->value = $v;
    }
    public function __toString(){
        return $this->value;
    }
}
