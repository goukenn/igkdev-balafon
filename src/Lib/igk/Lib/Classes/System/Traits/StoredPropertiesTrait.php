<?php
// @author: C.A.D. BONDJE DOUE
// @file: StoredPropertiesTrait.php
// @date: 20230306 04:32:30
namespace IGK\System\Traits;


///<summary></summary>
/**
* since 8.0 dynamic property is deprecated to user properties trait that will handle auto property \
* and magic __get and __set to be implement in user.
* @package IGK\System\Traits
*/
trait StoredPropertiesTrait{
    protected $m_properties = [];

    public function getProperty($n){
        return igk_getv($this->m_properties, $n);
    }
    public function setProperty($n, $v){
        if (is_null($v)){
            unset($this->m_properties[$n]);
            return $this;
        }
        $this->m_properties[$n] = $v;
        return $this;
    }
}