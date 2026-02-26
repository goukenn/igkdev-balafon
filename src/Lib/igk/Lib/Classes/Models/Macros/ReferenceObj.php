<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ReferenceObj.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\Models\Macros;
use IGKObject;
/**
 * reference object value 
 * @package IGK\Models\Macros
 */
class ReferenceObj extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_ref;

    /**
    * .ctr
    * @param mixed $ref
    */
    public function __construct($ref){
        $this->_ref = $ref;
    }

    /**
    * auto generate doc.
    */
    public function getIsNew(){
        return $this->_ref->newValue;
    }

    /**
    * auto generate doc.
    */
    public function getNextValue(){
        return $this->_ref->clNextValue;
    }
    /**
     * update value 
     * @return void 
     */

    public function update(){
        $update = $this->_ref->update;
        $update();
    }
    /**
     * get next value
     * @return mixed 
     */

    public function getValue(){
        return $this->_ref->value;
    }
}