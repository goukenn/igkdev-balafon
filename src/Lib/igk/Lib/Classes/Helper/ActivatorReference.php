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
    private $m_reference;
    private function __construct(){

    }
    public static function Create(& $reference ): ActivatorReference{
        $c = new static;
        $c->m_reference = & $reference;
        return $c;
    }
    public function & getReference(){
        return $this->m_reference;
    }
}