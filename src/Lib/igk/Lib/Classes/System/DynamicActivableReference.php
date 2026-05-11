<?php
// @author: C.A.D. BONDJE DOUE
// @file: DynamicActivableReference.php
// @date: 20260426 11:58:07
namespace IGK\System;
/**
* auto generate doc.
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System
*/
class DynamicActivableReference{
    private $m_reference;
    /**
    * auto generate doc.
    * @param mixed & $refence
    * @return DynamicActivableReference
    */
    public static function Create(& $refence): DynamicActivableReference{
        $c = new static;
        $c->m_reference = & $refence;

        return $c;
    }
    /**
    * .ctr
    * @return void
    */
    private function __construct()
    {
    }
    /**
    * auto generate doc.
    * @return void
    */
    public function & getReference(){
        return $this->m_reference;
    }
}