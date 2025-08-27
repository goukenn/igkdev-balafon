<?php
// @author: C.A.D. BONDJE DOUE
// @file: DynamicActivableTrait.php
// @date: 20250208 16:15:34
namespace IGK\System\Traits;
use Exception;
use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGKException;
/**
* 
* @package IGK\System\Traits
* @author C.A.D. BONDJE DOUE
*/
trait DynamicActivableTrait{
    protected $data;
    public function to_array(): ?array {return $this->data; }
    public function __get($n){ return igk_getv($this->data, $n); } 
    public function __set($n, $v){ return $this->data[$n] = $v; } 
    /**
     * to implement serialisation
     * @return mixed 
     */
    public function _json_serialize(){
        return $this->data;
    }
    /**
     * 
     * @return string|false 
     * @throws IGKException 
     * @throws Exception 
     */
    public function to_json($option = NULL, int $flag = 0){
        return JSon::Encode($this->data, JSonEncodeOption::IgnoreEmpty());
    }
    public function __isset($n){
        igk_wln_e( __FILE__.":".__LINE__ , 'lkjd: '.$n);
    }
}