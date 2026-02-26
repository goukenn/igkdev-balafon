<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DescValue.php
// @date: 20221230 11:01:13
// @desc: 
namespace IGK\System\Console;

/**
* auto generate doc.
* @package IGK\System\Console
*/
class DescValue{

    /**
    * auto generate doc.
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
    * auto generate doc.
    */

    public function __toString(){
        return $this->value;
    }
}