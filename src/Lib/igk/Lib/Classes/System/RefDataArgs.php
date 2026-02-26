<?php
// @author: C.A.D. BONDJE DOUE
// @file: RefDataArgs.php
// @date: 20251023 23:30:42
namespace IGK\System;


/**
* 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
class RefDataArgs extends DataArgs{

    /**
    * .ctr
    * @param mixed $t
    */
    public function __construct($t)
    {
        parent::__construct($t);
    }
    /**
     * update the reference data
     * @param mixed $n 
     * @param mixed $v 
     * @return void 
     */

    function __set($n, $v){
        $this->p_data[$n] = $v;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
    function _access_offsetSet($n, $v){
        $this->__set($n, $v);        
    }
}