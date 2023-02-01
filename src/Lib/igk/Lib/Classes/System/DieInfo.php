<?php
// @author: C.A.D. BONDJE DOUE
// @file: DieInfo.php
// @date: 20230118 18:01:10
namespace IGK\System;


///<summary></summary>
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

    var $options;
    public function __construct(string $message, ?array $options=null)
    {
        $this->message = $message;
        $this->options = $options;
    }
    public function __toString()
    {
        return implode("\n", [$this->message] + $this->options);
    }
}