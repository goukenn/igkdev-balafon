<?php
// @author: C.A.D. BONDJE DOUE
// @file: DynamicActivableReference.php
// @date: 20260426 11:58:07
namespace IGK\System;


/**
* 
* @package IGK\System
* @author C.A.D. BONDJE DOUE
*/
class DynamicActivableReference{
    private $m_reference;
    public static function Create(& $refence): DynamicActivableReference{
        $c = new static;
        $c->m_reference = & $refence;

        return $c;
    }
    private function __construct()
    {
    }
    public function & getReference(){
        return $this->m_reference;
    }
}