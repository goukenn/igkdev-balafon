<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BindingContextInfo.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Templates;
use IGK\System\Exceptions\BindingContextPropertyNotFoundException;
/**
 * extra binding context
 * @package 
 */
class BindingContextInfo{
    /**
     * raw data to pass binding info
     * @var mixed
     */
    var $raw;
    /**
     * controller to pass
     * @var ?IGK\Controllers\BaseController 
     */
    var $ctrl;

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return json_encode(array_filter((array)$this));
    }

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if (is_object($this->raw) && property_exists($this->raw, $n)){
            return $this->raw->$n;
        }
        throw new BindingContextPropertyNotFoundException($n);
    }

    /**
    * To array.
    */
    public function to_array(){
        return (array)$this;
    }
}