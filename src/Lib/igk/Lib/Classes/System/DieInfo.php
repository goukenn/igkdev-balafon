<?php
// @author: C.A.D. BONDJE DOUE
// @file: DieInfo.php
// @date: 20230118 18:01:10
namespace IGK\System;
/**
* 
* @package IGK\System
*/
class DieInfo{
    /**
     * message
     * @var 
     */
    var $message;

    /**
    * Property: options.
    * @var mixed
    */
    var $options;

    /**
    * .ctr
    * @param string $message
    * @param null|array $options
    */
    public function __construct(string $message, ?array $options=null)
    {
        $this->message = $message;
        $this->options = $options;
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return implode("\n", [$this->message] + $this->options);
    }
}