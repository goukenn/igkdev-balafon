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

/**
* auto generate doc.
* @package IGK\System\Traits
*/
trait DynamicActivableTrait{

    /**
    * Property: data.
    * @var mixed
    */
    protected $data;

    /**
    * To array.
    * @return ?array
    */
    public function to_array(): ?array {return $this->data; }

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
    public function __set($n, $v){ return $this->data[$n] = $v; } 
    /**
     * to implement serialisation
     * @return mixed 
     */

    public function _json_serialize(){
        return $this->data;
    }

    /**
    * auto generate doc.
    * @return string|false
    */

    public function to_json($option = NULL, int $flag = 0){
        return JSon::Encode($this->data, JSonEncodeOption::IgnoreEmpty());
    }

    /**
    * check if isset innaccessible property
    * @param mixed $n
    */
    public function __isset($n){        
        return isset($this->data[$n]);
    }
}