<?php
namespace IGK\Helper;

///<summary> json utility options</summary>
/**
 * json utility options
 * @package IGK\Helper
 */
class JSONUtilityOption{
    var $ignore_empty;
    var $default_ouput;  

    public function __construct($ignore_empty=false, $default_ouput='{}')
    {
        $this->ignore_empty = $ignore_empty;
        $this->default_ouput = $default_ouput;
    }
}