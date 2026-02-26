<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DescValue.php
// @date: 20221230 11:01:13
// @desc: 
namespace IGK\System\Console;

/**
* Desc value.
* @package IGK\System\Console
*/
class DescValue{

    /**
    * Property: value.
    * @var mixed
    */
    var $value;

    /**
    * .ctr
    * @param mixed $v
    */
    public function __construct($v){
        $this->value = $v;
    }

    /**
    * Returns string representation.
    */

    public function __toString(){
        return $this->value;
    }
}