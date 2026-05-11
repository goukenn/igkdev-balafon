<?php
// @author: C.A.D. BONDJE DOUE
// @file: ActivatorReference.php
// @date: 20260426 11:22:52
namespace IGK\Helper;


/**
* pass reference to activator
* @package IGK\Helper
* @author C.A.D. BONDJE DOUE
*/
class ActivatorReference{
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    private $m_reference;
    /**
    * .ctr
    * @return void
    */
    private function __construct(){

    }
    /**
    * auto generate doc.
    * @param mixed & $reference
    * @return ActivatorReference
    */
    public static function Create(& $reference ): ActivatorReference{
        $c = new static;
        $c->m_reference = & $reference;
        return $c;
    }
    /**
    * auto generate doc.
    * @return void
    */
    public function & getReference(){
        return $this->m_reference;
    }
}