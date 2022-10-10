<?php
// @author: C.A.D. BONDJE DOUE
// @file: SessionOperatorIncCallback.php
// @date: 20221010 01:48:20
namespace IGK\System\Http\Session;


///<summary></summary>
/**
* 
* @package IGK\System\Http\Session
*/
class SessionOperatorIncCallback extends SessionOperatorBase {
    var $step;
    public function __construct(?int $step = 1)
    {
        $this->step = is_null($step) ? 1: $step;
    }
    public function invoke($a) { 
        if ($a)
            $a+= $this->step;
        else 
            $a = $this->step;
        return $a;
    }

}