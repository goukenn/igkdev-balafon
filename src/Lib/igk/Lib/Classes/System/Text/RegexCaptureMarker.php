<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexCaptureMarker.php
// @date: 20241106 11:17:24
namespace IGK\System\Text;

/**
* capture marker 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
final class RegexCaptureMarker{
    /**
    * Property: value.
    * @var mixed
    */
    var $value;
    /**
    * Collection of list.
    * @var mixed
    */
    var $list;
    /**
    * .ctr
    * @param mixed $value
    * @param mixed $list
    */
    public function __construct($value, $list)
    {
        $this->value = $value;
        $this->list = $list;
    }
}