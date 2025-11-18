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
    private $m_data;
    protected function _access_offsetGet($v){
        return igk_getv($this->m_data, $v);
    }
    protected function _access_offsetSet($k , $v){
        $this->m_data[$k] = $v;
    }
}