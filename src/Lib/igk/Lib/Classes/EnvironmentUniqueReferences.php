<?php
// @author: C.A.D. BONDJE DOUE
// @file: EnvironmentUniqueReferences.php
// @date: 20250423 21:28:48
namespace IGK;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* 
* @package IGK
* @author C.A.D. BONDJE DOUE
*/
class EnvironmentUniqueReferences implements ArrayAccess{
    use ArrayAccessSelfTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_data;

    /**
    * auto generate doc.
    * @param mixed $v
    */
    protected function _access_offsetGet($v){
        return igk_getv($this->m_data, $v);
    }

    /**
    * auto generate doc.
    * @param mixed $k
    * @param mixed $v
    */
    protected function _access_offsetSet($k , $v){
        $this->m_data[$k] = $v;
    }
}