<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JSONUtilityOption.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Helper;
/**
 * json utility options
 * @package IGK\Helper
 */
class JSONUtilityOption{
    /**
    * Property: ignore empty.
    * @var mixed
    */
    var $ignore_empty;
    /**
    * Property: default ouput.
    * @var mixed
    */
    var $default_ouput;
    /**
    * .ctr
    * @param mixed $ignore_empty
    * @param mixed $default_ouput
    */
    public function __construct($ignore_empty=false, $default_ouput='{}')
    {
        $this->ignore_empty = $ignore_empty;
        $this->default_ouput = $default_ouput;
    }
}