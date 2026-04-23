<?php
// @author: C.A.D. BONDJE DOUE
// @file: NoDynamicPropertyTrait.php
// @date: 20221206 07:40:53
namespace IGK\Core\Traits;
use IGK\System\Exceptions\OperationNotAllowedException;

/**
* auto generate doc.
* @package IGK\Core\Traits
*/
trait NoDynamicPropertyTrait{
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        throw new OperationNotAllowedException(__METHOD__);
    }
    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        throw new OperationNotAllowedException(__METHOD__);
    }
}