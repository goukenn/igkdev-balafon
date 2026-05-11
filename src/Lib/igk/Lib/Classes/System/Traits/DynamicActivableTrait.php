<?php
// @author: C.A.D. BONDJE DOUE
// @file: DynamicActivableTrait.php
// @date: 20250208 16:15:34
namespace IGK\System\Traits;
use Exception;
use IGK\Helper\ActivatorReference;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\DynamicActivableReference;
use IGKException;
/**
* auto generate doc.
* @package IGK\System\Traits
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Traits
*/
trait DynamicActivableTrait{
    private $m_reflist;
    /**
    * Property: data.
    * @var mixed
    */
    protected $data;
    /**
    * To array.
    * @return ?array
    */
    public function to_array(): ?array {
        if ($this->m_reflist && count($this->m_reflist)>0){
            $db = $this->data;
            foreach(array_keys($this->m_reflist) as $k ){
                $db[$k] = & $this->data[$k];
            }
            return $db;
        }
        return $this->data; 
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){ return igk_getv($this->data, $n); }
    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){ 
        $reflist = false;
        if ($v instanceof DynamicActivableReference){
            $this->data[$n]  = & $v->getReference();
            $reflist = true;
        } else 
            $this->data[$n] = $v;
        $this->_update_ref_list($n, $this->m_reflist, $reflist);
    }
    /**
    * auto generate doc.
    * @param string $n
    * @param mixed & $reflist
    * @param bool $is_reference
    * @return void
    */
    private function _update_ref_list(string $n, & $reflist, bool $is_reference){
        if ($is_reference){
            $reflist[$n] = 1;
        }else{
            unset($reflist[$n]);
        }
    }   
    /**
     * to implement serialisation
     * @return mixed 
     */
    public function _json_serialize(){
        return $this->data;
    }
    /**
    * auto generate doc.
    * @param mixed $option
    * @param int $flag
    * @return string|false
    */
    public function to_json($option = NULL, int $flag = 0){
        return JSon::Encode($this->data, JSonEncodeOption::IgnoreEmpty());
    }
    /**
    * check if isset innaccessible property
    * @param mixed $n
    */
    public function __isset($n): bool{        
        return isset($this->data[$n]);
    }
}