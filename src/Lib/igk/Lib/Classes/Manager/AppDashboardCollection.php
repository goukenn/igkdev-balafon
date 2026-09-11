<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppDashboardCollection.php
// @date: 20260903 15:41:58
namespace IGK\Manager;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

/**
* 
* @package IGK\Manager
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Manager
*/
class AppDashboardCollection implements ArrayAccess{
    use ArrayAccessSelfTrait;
    /**
    * auto generate doc.
    * @var array
    */
    private $m_collection = [];
    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    * @return void
    */
    protected function _access_OffsetSet($n, $v){
        $g = AppDashboardItem::CreateNewInstance($v);
        if (is_null($n)){
            $this->m_collection[] = $g;
        }else{
            $this->m_collection[$n] = $g;
        }
    }
    /**
    * auto generate doc.
    * @param mixed $n
    * @return mixed
    */
    protected function _access_OffsetGet($n){
        return igk_getv($this->m_collection, $n);
    }
    /**
    * auto generate doc.
    * @return void
    */
    public function to_array(){
        return $this->m_collection;
    }
}